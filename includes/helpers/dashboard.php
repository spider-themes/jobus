<?php
/**
 * Jobus helper functions: mail, view tracking, dashboard/application helpers.
 *
 * Extracted from includes/functions.php, which was split into focused includes
 * under includes/helpers/ for maintainability. Loaded by includes/functions.php.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure PHPMailer with SMTP settings from plugin options.
 *
 * Hooks into phpmailer_init to configure SMTP server credentials and settings
 * for WordPress email delivery.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer The PHPMailer instance.
 *
 * @return PHPMailer\PHPMailer\PHPMailer The configured PHPMailer instance.
 */
add_action( 'phpmailer_init', 'jobus_phpmailer_init' );
function jobus_phpmailer_init( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = jobus_opt( 'smtp_host' ); // your SMTP server
    $phpmailer->Port       = jobus_opt( 'smtp_port' ); // SSL
    $phpmailer->CharSet    = "utf-8";
    $phpmailer->SMTPAuth   = jobus_opt( 'smtp_authentication' );
    $phpmailer->Username   = jobus_opt( 'smtp_username' );
    $phpmailer->Password   = jobus_opt( 'smtp_password' );
    $phpmailer->SMTPSecure = jobus_opt( 'smtp_encryption' );
    $phpmailer->From       = jobus_opt( 'smtp_from_mail_address' );
    $phpmailer->FromName   = jobus_opt( 'smtp_from_name' );

    return $phpmailer;
}

/**
 * Retrieve a sanitized query parameter with optional nonce verification.
 *
 * Use for filtering, sorting, or paginating query parameters securely.
 *
 * @param string $param        The query parameter key.
 * @param mixed  $default      Fallback value if not set or nonce fails.
 * @param string $nonce_action Optional nonce action slug (for secure checks).
 *
 * @return mixed Sanitized parameter value or default.
 */
function jobus_get_sanitized_query_param( string $param, $default = '', string $nonce_action = '' ) {

    if ( ! isset( $_GET[ $param ] ) ) {
        return $default;
    }

    $value = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );

    // If nonce validation is requested
    if ( $nonce_action ) {
        $nonce = sanitize_text_field( wp_unslash( $_GET['jobus_nonce'] ?? '' ) );

        // No nonce? Allow fallback unless strict security is needed
        if ( empty( $nonce ) ) {
            return $value;
        }

        if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
            return $default;
        }
    }

    return $value ? $value : $default;
}


/**
 * Tracks and increments the view count for a specific post (job or candidate).
 * Uses cookies for guests and user meta for logged-in users to ensure unique views are counted.
 * Differentiates between general views and employer-specific views.
 *
 * @param int    $post_id The ID of the post whose views are being tracked.
 * @param string $type    The type of post: 'job' or 'candidate'.
 *
 * @return void
 */
function jobus_count_post_views( int $post_id, string $type = 'job' ): void {
    if ( ! $post_id ) {
        return;
    }

    // Skip obvious bots/crawlers so automated traffic neither inflates view counts nor
    // hammers postmeta with a write on every hit.
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';
    if ( '' === $ua || preg_match( '/(bot|crawl|spider|slurp|bing|google|yandex|baidu|duckduck|facebookexternalhit|headless|curl|wget|python-requests|httpclient)/', $ua ) ) {
        return;
    }

    $is_logged_in = is_user_logged_in();
    $user_id      = 0;
    if ( $is_logged_in ) {
        $user    = wp_get_current_user();
        $user_id = $user->ID;
    }

    // Unique key for this user/guest and post
    if ( $is_logged_in ) {
        $user_viewed_key = 'jobus_user_viewed_' . $type . '_' . $user_id . '_' . $post_id;
        if ( get_user_meta( $user_id, $user_viewed_key, true ) ) {
            return; // Already counted for this user
        }
        update_user_meta( $user_id, $user_viewed_key, '1' );
    } else {
        $cookie_key = 'jobus_guest_viewed_' . $type . '_' . $post_id;

        // Server-side backstop to the cookie: a cookieless client (e.g. a scripted bot)
        // would otherwise be counted on every request. Dedupe per IP+post for a day.
        $ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $ip_key = 'jobus_vw_' . md5( $type . '_' . $post_id . '_' . $ip );

        if ( ( isset( $_COOKIE[ $cookie_key ] ) && '1' === $_COOKIE[ $cookie_key ] ) || get_transient( $ip_key ) ) {
            return; // Already counted for this guest (browser or IP)
        }

        set_transient( $ip_key, 1, DAY_IN_SECONDS );

        // Only set cookie if headers haven't been sent yet
        if ( ! headers_sent() ) {
            setcookie( $cookie_key, '1', time() + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN );
        }
        $_COOKIE[ $cookie_key ] = '1';
    }

    // Increment total visitor count atomically. A get_post_meta()/update_post_meta()
    // read-modify-write loses concurrent increments under traffic (two simultaneous
    // views both read N and both write N+1, so one view is dropped). A single SQL
    // UPDATE ... meta_value + 1 is atomic at the database level.
    jobus_increment_post_meta_counter( $post_id, 'all_user_view_count' );

    // Increment employer-specific count for logged-in employers only
    if ( $is_logged_in && in_array( 'jobus_employer', (array) $user->roles ) ) {
        jobus_increment_post_meta_counter( $post_id, 'employer_view_count' );
    }
}

/**
 * Atomically increment an integer post-meta counter.
 *
 * Uses a single UPDATE statement so concurrent requests can't lose increments,
 * seeding the row on first use. Falls back to creating the row when it does not
 * exist yet.
 *
 * @param int    $post_id  Post ID.
 * @param string $meta_key Meta key holding the counter.
 * @return void
 */
function jobus_increment_post_meta_counter( int $post_id, string $meta_key ): void {
    global $wpdb;

    $updated = $wpdb->query( $wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
        $post_id,
        $meta_key
    ) );

    // No existing row to increment — create it. unique=true makes a racing second
    // creation a harmless no-op rather than a duplicate counter row.
    if ( ! $updated ) {
        add_post_meta( $post_id, $meta_key, 1, true );
    }

    // The direct SQL bypasses the object cache; drop the stale cached meta.
    wp_cache_delete( $post_id, 'post_meta' );
}


if ( ! function_exists( 'jobus_get_cv_download_url' ) ) {
    /**
     * Build an authenticated, nonce-protected download URL for a CV/resume attachment.
     *
     * CVs are stored as normal media attachments; linking their raw uploads URL exposes
     * candidate PII to anyone with the link. This routes downloads through an authorization
     * gate (jobus_download_cv) instead. Existing files are NOT moved — the gate reads them
     * by path — so there is zero data migration/risk.
     *
     * @param int $attachment_id  The CV attachment ID.
     * @param int $application_id  Optional applicant post ID (lets the receiving employer download).
     * @return string Download URL, or empty string if no attachment.
     */
    function jobus_get_cv_download_url( int $attachment_id, int $application_id = 0 ): string {
        if ( ! $attachment_id ) {
            return '';
        }

        $args = [
            'action'        => 'jobus_download_cv',
            'attachment_id' => $attachment_id,
        ];

        if ( $application_id ) {
            $args['application_id'] = $application_id;
        }

        return wp_nonce_url(
            add_query_arg( $args, admin_url( 'admin-ajax.php' ) ),
            'jobus_download_cv_' . $attachment_id
        );
    }
}

/**
 * Get the save status of a post (job or candidate) for the current user.
 *
 * @param int|string $post_id  The post ID to check. Defaults to current post ID if empty.
 * @param string     $meta_key The user meta key to use. Defaults to 'jobus_saved_jobs'.
 *
 * @return array Status information about the saved post.
 */
function jobus_get_save_status( int|string $post_id = '', string $meta_key = 'jobus_saved_jobs' ): array {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $user_id     = get_current_user_id();
    $saved_items = is_user_logged_in() ? (array) get_user_meta( $user_id, $meta_key, true ) : [];
    $is_saved    = in_array( $post_id, $saved_items );

    return [
            'post_id'     => $post_id,
            'user_id'     => $user_id,
            'saved_items' => $saved_items,
            'is_saved'    => $is_saved
    ];
}


/**
 * Recursively sanitize text data including arrays.
 *
 * Applies sanitize_text_field to all elements in an array recursively,
 * handling nested arrays appropriately.
 *
 * @param array|mixed $data The data to sanitize, can be an array or scalar value.
 *
 * @return array|string Sanitized data in the same structure as input.
 */
if ( ! function_exists( 'jobus_recursive_sanitize_text_field' ) ) {
    function jobus_recursive_sanitize_text_field( $data ) {
        if ( is_array( $data ) ) {
            return array_map( 'jobus_recursive_sanitize_text_field', wp_unslash( $data ) );
        }

        return sanitize_text_field( wp_unslash( $data ) );
    }
}

/**
 * Render the save/bookmark button for posts.
 *
 * Outputs an anchor element with data attributes for saving/bookmarking jobs or candidates.
 * The button uses Bootstrap icons to display the save state.
 *
 * @param array     $args         {
 *                                Optional. Array of arguments for the save button.
 *
 * @type int|string $post_id      The post ID to save/bookmark.
 * @type string     $post_type    The post type (e.g., 'jobus_job', 'jobus_candidate').
 * @type string     $meta_key     The user meta key for saving posts.
 * @type bool       $is_saved     Whether the post is already saved by the user.
 * @type string     $button_title The title/tooltip for the button.
 * @type string     $class        CSS classes for the button element.
 *                                }
 *
 * @return void
 */
if ( ! function_exists( 'jobus_render_post_save_button' ) ) {
    function jobus_render_post_save_button( $args ) {
        $post_id      = $args['post_id'] ?? '';
        $post_type    = $args['post_type'] ?? '';
        $meta_key     = $args['meta_key'] ?? '';
        $is_saved     = ! empty( $args['is_saved'] ) ? 'bi bi-bookmark-check-fill jbs-text-primary' : 'bi bi-bookmark-dash';
        $button_title = $args['button_title'] ?? '';
        $class        = $args['class'] ?? '';
        ?>
        <a href="javascript:void(0);"
           class="save_post_btn <?php echo esc_attr( $class ); ?>"
           data-post_id="<?php echo esc_attr( $post_id ); ?>"
           data-post_type="<?php echo esc_attr( $post_type ); ?>"
           data-meta_key="<?php echo esc_attr( $meta_key ); ?>"
           title="<?php echo esc_attr( $button_title ); ?>">
            <i class="<?php echo esc_attr( $is_saved ); ?>"></i>
        </a>
        <?php
    }
}
/**
 * Get default company logo URL
 *
 * Returns the custom default company logo from settings, or falls back to the default image.
 *
 * @return string URL of the default company logo
 */
if ( ! function_exists( 'jobus_get_default_company_logo' ) ) {
    function jobus_get_default_company_logo() {
        // Try to get custom logo from settings
        $custom_logo = jobus_opt( 'default_company_logo' );

        if ( ! empty( $custom_logo ) && isset( $custom_logo['url'] ) ) {
            return esc_url( $custom_logo['url'] );
        }

        // Fallback to default logo
        return plugins_url( 'jobus/assets/images/default-company.png' );
    }
}

/**
 * Get endpoint URL for dashboard navigation.
 *
 * Handles both plain and pretty permalink structures and ensures the 'dashboard'
 * item points to the main dashboard page.
 *
 * @param string $endpoint      The endpoint slug (e.g., 'profile', 'jobs').
 * @param string $dashboard_url The base URL of the dashboard page.
 *
 * @return string The correctly formatted URL for the endpoint.
 */
if ( ! function_exists( 'jobus_get_dashboard_endpoint_url' ) ) {
    function jobus_get_dashboard_endpoint_url( string $endpoint, string $dashboard_url ): string {
        // Special case: the main dashboard doesn't need an endpoint appended
        if ( 'dashboard' === $endpoint ) {
            return $dashboard_url;
        }

        if ( get_option( 'permalink_structure' ) ) {
            if ( strpos( $dashboard_url, '?' ) !== false ) {
                $query_string = '?' . wp_parse_url( $dashboard_url, PHP_URL_QUERY );
                $dashboard_url = current( explode( '?', $dashboard_url ) );
            } else {
                $query_string = '';
            }

            return trailingslashit( $dashboard_url ) . $endpoint . '/' . $query_string;
        }

        return add_query_arg( $endpoint, '', $dashboard_url );
    }
}

if ( ! function_exists( 'jobus_get_application_statuses' ) ) {
    /**
     * Single source of truth for job application statuses.
     *
     * Add a status here once and it cascades to the admin metabox dropdown,
     * the employer status selector, the AJAX validator and analytics counts.
     *
     * @return array<string, array<string, string>>
     */
    function jobus_get_application_statuses(): array {
        // Labels are returned unescaped; callers escape at output (esc_html / esc_attr).
        $statuses = [
            'pending'     => [
                'label'        => __( 'Pending', 'jobus' ),
                'badge_class'  => 'jbs-bg-warning',
                'icon'         => 'bi-hourglass-split',
                'action_label' => __( 'Mark Pending', 'jobus' ),
            ],
            'shortlisted' => [
                'label'        => __( 'Shortlisted', 'jobus' ),
                'badge_class'  => 'jbs-bg-info',
                'icon'         => 'bi-star',
                'action_label' => __( 'Shortlist', 'jobus' ),
            ],
            'approved'    => [
                'label'        => __( 'Approved', 'jobus' ),
                'badge_class'  => 'jbs-bg-success',
                'icon'         => 'bi-check-circle',
                'action_label' => __( 'Approve', 'jobus' ),
            ],
            'rejected'    => [
                'label'        => __( 'Rejected', 'jobus' ),
                'badge_class'  => 'jbs-bg-danger',
                'icon'         => 'bi-x-circle',
                'action_label' => __( 'Reject', 'jobus' ),
            ],
        ];

        return (array) apply_filters( 'jobus_application_statuses', $statuses );
    }
}

if ( ! function_exists( 'jobus_get_application_status' ) ) {
    /**
     * Resolve a single application's normalized status meta to its descriptor.
     *
     * @param int    $application_id Application post ID.
     * @param string $fallback       Status key to use when meta is missing/unknown.
     * @return array{key:string,label:string,badge_class:string,icon:string,action_label:string}
     */
    function jobus_get_application_status( int $application_id, string $fallback = 'pending' ): array {
        $statuses = jobus_get_application_statuses();
        $raw      = (string) get_post_meta( $application_id, 'application_status', true );
        $key      = isset( $statuses[ $raw ] ) ? $raw : ( isset( $statuses[ $fallback ] ) ? $fallback : 'pending' );

        return array_merge( [ 'key' => $key ], $statuses[ $key ] );
    }
}


if ( ! function_exists( 'jobus_user_can_view_dashboard' ) ) {
	/**
	 * Whether the current user may view a role-specific dashboard section.
	 *
	 * Used as a defence-in-depth guard at the top of dashboard templates: the
	 * dashboard router already gates by role before including them, but this keeps
	 * a template safe if it is ever loaded through another path (theme override,
	 * direct template part call, custom integration).
	 *
	 * @param string $role Required role, e.g. 'jobus_candidate' or 'jobus_employer'.
	 * @return bool
	 */
	function jobus_user_can_view_dashboard( string $role ): bool {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return in_array( $role, (array) wp_get_current_user()->roles, true );
	}
}


if ( ! function_exists( 'jobus_get_candidate_completeness' ) ) {
	/**
	 * Profile-completeness steps for the candidate dashboard checklist.
	 *
	 * Each step maps to data the candidate can already edit in the dashboard; the
	 * checklist only presents it as a guided journey. Steps are filterable so Pro
	 * or site-specific fields can plug in.
	 *
	 * @param int  $user_id      Candidate's WP user ID.
	 * @param int  $candidate_id Candidate profile post ID (0 if missing).
	 * @param bool $has_applied  Whether the candidate has applied to at least one job.
	 * @return array{percent:int,steps:array<int,array{key:string,label:string,done:bool,endpoint:string}>}
	 */
	function jobus_get_candidate_completeness( int $user_id, int $candidate_id, bool $has_applied ): array {
		$has_photo = (bool) get_user_meta( $user_id, 'candidate_profile_picture_id', true );

		$has_description = false;
		$has_cv          = false;
		$has_skills      = false;

		if ( $candidate_id && class_exists( '\jobus\includes\Classes\submission\Candidate_Form_Submission' ) ) {
			$description_data = \jobus\includes\Classes\submission\Candidate_Form_Submission::get_candidate_description( $candidate_id );
			$has_description  = ! empty( $description_data['description'] );

			$cv_data = \jobus\includes\Classes\submission\Candidate_Form_Submission::get_candidate_cv( $candidate_id );
			$has_cv  = ! empty( $cv_data['cv_attachment'] );

			$terms      = wp_get_object_terms( $candidate_id, [ 'jobus_candidate_skill', 'jobus_candidate_cat' ], [ 'fields' => 'ids' ] );
			$has_skills = ! is_wp_error( $terms ) && ! empty( $terms );
		}

		$steps = [
			[
				'key'      => 'photo',
				'label'    => __( 'Add a profile photo', 'jobus' ),
				'done'     => $has_photo,
				'endpoint' => 'profile',
			],
			[
				'key'      => 'about',
				'label'    => __( 'Write a short bio', 'jobus' ),
				'done'     => $has_description,
				'endpoint' => 'profile',
			],
			[
				'key'      => 'cv',
				'label'    => __( 'Upload your CV', 'jobus' ),
				'done'     => $has_cv,
				'endpoint' => 'resume',
			],
			[
				'key'      => 'skills',
				'label'    => __( 'Pick your skills & categories', 'jobus' ),
				'done'     => $has_skills,
				'endpoint' => 'resume',
			],
			[
				'key'      => 'apply',
				'label'    => __( 'Apply to your first job', 'jobus' ),
				'done'     => $has_applied,
				'endpoint' => '',
			],
		];

		$steps = (array) apply_filters( 'jobus_candidate_completeness_steps', $steps, $user_id, $candidate_id );

		$total = count( $steps );
		$done  = count( array_filter( $steps, static fn( $step ) => ! empty( $step['done'] ) ) );

		return [
			'percent' => $total ? (int) round( ( $done / $total ) * 100 ) : 100,
			'steps'   => $steps,
		];
	}
}
