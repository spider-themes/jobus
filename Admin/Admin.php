<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin class for Jobus plugin
 *
 * Adds custom classes to the WordPress admin body tag based on the active theme and Jobus-specific options.
 *
 * @package Jobus\Admin
 */
class Admin {
	/**
	 * Admin constructor.
	 * Adds the body_class filter for admin area.
	 */
	function __construct() {
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_action( 'admin_menu', [ $this, 'create_nested_submenus' ] );
		add_filter( 'parent_file', [ $this, 'active_top_level_menu' ] );
		add_filter( 'submenu_file', [ $this, 'active_sub_menus' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_meta_links' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'setup_radius_engine' ] );
		add_action( 'admin_init', [ $this, 'setup_matching_engine' ] );
		add_action( 'admin_notices', [ $this, 'radius_sync_notice' ] );
		add_action( 'admin_notices', [ $this, 'matching_sync_notice' ] );
		add_action( 'admin_notices', [ $this, 'version_update_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_free_vs_pro_assets' ] );
		add_action( 'admin_head', [ $this, 'hide_free_vs_pro_notices' ], 1 );
	}

	/**
	 * Adds custom classes to the admin body tag for Jobi theme.
	 *
	 * @param string $classes Existing body classes.
	 *
	 * @return string Modified body classes.
	 */
	public function body_class( string $classes ): string {
		$current_theme = get_template();
		$classes      .= ' ' . $current_theme;

		// Add premium/pro class if needed for Jobus
		if ( function_exists( 'jobus_is_premium' ) && jobus_is_premium() ) {
			$classes .= ' jbs-premium';
		}

		return $classes;
	}

	/**
	 * Setup Utility: Force Database Table Creation & Backfill Locations.
	 * Triggered by navigating to wp-admin/edit.php?post_type=jobus_job&jobus_setup_radius=true
	 *
	 * @return void
	 */
	public function setup_radius_engine(): void {
		if ( ! isset( $_GET['jobus_setup_radius'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Verify nonce for security.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'jobus_setup_radius_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'jobus' ) );
		}

		// Force Setup the required structural DB table
		if ( class_exists( '\jobus\includes\Classes\Lifecycle' ) ) {
			\jobus\includes\Classes\Lifecycle::create_tables();
		}

		// Setup / Backfill all existing Jobs with Coordinates
		if ( class_exists( '\jobus\includes\Classes\Geolocation' ) ) {
			$geo_engine = new \jobus\includes\Classes\Geolocation();

			$jobs = get_posts(
				[
					'post_type'      => 'jobus_job',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				]
			);

			$synced_count = 0;
			if ( ! empty( $jobs ) && ! is_wp_error( $jobs ) ) {
				foreach ( $jobs as $job_id ) {
					$post = get_post( $job_id );
					if ( $post ) {
						$geo_engine->sync_job_location( $job_id, $post );
						$synced_count++;
					}
				}
			}

			update_option( 'jobus_radius_setup_completed', true );
			
			// Output success and redirect securely
			$redirect_url = remove_query_arg( [ 'jobus_setup_radius', '_wpnonce' ] );
			$redirect_url = add_query_arg( [ 'jobus_radius_synced' => $synced_count ], $redirect_url );
			
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Flash an admin notice upon successful radius search setup.
	 */
	public function radius_sync_notice(): void {
		if ( isset( $_GET['jobus_radius_synced'] ) && current_user_can( 'manage_options' ) ) {
			$count   = absint( $_GET['jobus_radius_synced'] );
			$message = sprintf( 
				/* translators: %d: number of jobs synced */
				esc_html__( 'Radius Search Setup Complete. Database Table created and synced %d jobs with geolocation coordinates.', 'jobus' ), 
				$count 
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
		}
	}

	/**
	 * Create matching tables and sync candidate coordinates (requires Jobus Pro).
	 *
	 * @return void
	 */
	public function setup_matching_engine(): void {
		if ( ! isset( $_GET['jobus_setup_matching'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'jobus_setup_matching_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'jobus' ) );
		}

		$redirect_url = remove_query_arg( array( 'jobus_setup_matching', '_wpnonce' ) );

		if ( ! class_exists( '\Jobus_Pro\Matching\Candidate_Geo_Query' ) || ! class_exists( '\Jobus_Pro\Matching\Schema' ) ) {
			$redirect_url = add_query_arg( array( 'jobus_matching_error' => 'pro_required' ), $redirect_url );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		if ( class_exists( '\jobus\includes\Classes\Lifecycle' ) ) {
			\jobus\includes\Classes\Lifecycle::create_tables();
		}
		\Jobus_Pro\Matching\Schema::create_tables();

		$synced = 0;
		if ( class_exists( '\jobus\includes\Classes\Geolocation' ) ) {
			$geo = new \jobus\includes\Classes\Geolocation();
			$candidates = get_posts(
				array(
					'post_type'      => 'jobus_candidate',
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'fields'         => 'ids',
				)
			);

			foreach ( $candidates as $candidate_id ) {
				$post = get_post( $candidate_id );
				if ( $post && $geo->sync_candidate_location( $candidate_id, $post ) ) {
					$synced++;
				}
			}
		}

		update_option( 'jobus_matching_setup_completed', true );

		$redirect_url = add_query_arg( array( 'jobus_matching_synced' => $synced ), $redirect_url );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Admin notice after matching setup.
	 *
	 * @return void
	 */
	public function matching_sync_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['jobus_matching_error'] ) && 'pro_required' === $_GET['jobus_matching_error'] ) {
			echo '<div class="notice notice-error is-dismissible"><p>'
				. esc_html__( 'Candidate matching sync requires Jobus Pro. Activate Jobus Pro, then try again.', 'jobus' )
				. '</p></div>';
			return;
		}

		if ( isset( $_GET['jobus_matching_synced'] ) ) {
			$count   = absint( $_GET['jobus_matching_synced'] );
			$message = sprintf(
				/* translators: %d: number of candidates synced */
				esc_html__( 'Matching setup complete. Tables created and %d candidate locations synced.', 'jobus' ),
				$count
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	public function create_nested_submenus() {
		// === COMPANY PAGES ===
		if ( jobus_opt('enable_company') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company', 'jobus' ), __( 'Company', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_company&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company' );
			
			// Add divider bottom if Candidate is not enabled
			$divider_bottom = jobus_opt('enable_candidate') != true ? '&has_jbs_divider_bottom=true' : '';
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company' . $divider_bottom );
		}

		// === CANDIDATE PAGES ===
		if ( jobus_opt('enable_candidate') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate', 'jobus' ), __( 'Candidate', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_candidate&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Skills', 'jobus' ), '&nbsp; Skills', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate&has_jbs_divider_bottom=true' );
		}

		// === FREE VS PRO COMPARISON PAGE ===
		if ( ! function_exists( 'jobus_is_premium' ) || ! jobus_is_premium() ) {
			add_submenu_page(
				'edit.php?post_type=jobus_job',
				__( 'Free vs Pro', 'jobus' ),
				__( 'Free vs Pro', 'jobus' ),
				'manage_options',
				'jobus-free-vs-pro',
				[ $this, 'render_free_vs_pro_page' ]
			);
		}
	}

	/**
	 * Highlight the correct parent menu item for post types and taxonomies.
	 *
	 * @param string $parent_file The current parent file.
	 * @return string Modified parent file.
	 */
	public function active_top_level_menu( $parent_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $parent_file;

		// Keep the Jobus top menu expanded for all Jobus post types and taxonomies.
		if (
			in_array( $screen->post_type, [ 'jobus_job', 'jobus_company', 'jobus_candidate' ] ) ||
			in_array( $screen->taxonomy, [
				'jobus_company_cat', 'jobus_company_location',
				'jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'
			]) ||
			( isset( $_GET['page'] ) && sanitize_key( $_GET['page'] ) === 'jobus-social-login' )
		) {
			$parent_file = 'edit.php?post_type=jobus_job';
		}

		return $parent_file;
	}

	/**
	 * Highlight the correct submenu item for post types and taxonomies.
	 *
	 * @param string $submenu_file The current submenu file.
	 * @return string Modified submenu file.
	 */
	public function active_sub_menus( $submenu_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $submenu_file;

		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
		$taxonomy  = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';

		// ===== Company menu =====
		if ( $post_type === 'jobus_company' || in_array( $taxonomy, ['jobus_company_cat','jobus_company_location'] ) ) {
			if ( $taxonomy === 'jobus_company_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company';
			} elseif ( $taxonomy === 'jobus_company_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_company&has_jbs_divider=true';
			}
		}

		// ===== Candidate menu =====
		if ( $post_type === 'jobus_candidate' || in_array( $taxonomy, ['jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'] ) ) {
			if ( $taxonomy === 'jobus_candidate_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_skill' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_candidate&has_jbs_divider=true';
			}
		}

		return $submenu_file;
	}


	/**
	 * Add custom meta links to the plugin row in the plugins list.
	 *
	 * @param array  $links Existing plugin row meta links.
	 * @param string $file  The plugin file path.
	 *
	 * @return array Modified plugin row meta links.
	 */
	public function add_meta_links( array $links, string $file ): array {

		if ( JOBUS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$links[] = '<a href="https://helpdesk.spiderthemes.net/docs/jobus-wordpress-plugin/" target="_blank">'.esc_html__( 'Docs & FAQs', 'jobus' ).'</a>';

		return $links;
	}

	/**
	 * Check for major updates and display a friendly release update notice.
	 *
	 * @return void
	 */
	public function version_update_notice(): void {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$dismissed = get_option( 'jobus_dismissed_update_notice_' . JOBUS_VERSION );
		if ( $dismissed ) {
			return;
		}

		// Only show on Jobus screens
		$screen = get_current_screen();
		if ( ! $screen || false === strpos( $screen->id, 'jobus' ) ) {
			return;
		}

		// If notice dismissed via GET
		if ( isset( $_GET['jobus_dismiss_update_notice'] ) && check_admin_referer( 'jobus_dismiss_update' ) ) {
			update_option( 'jobus_dismissed_update_notice_' . JOBUS_VERSION, 1 );
			return;
		}

		$dismiss_url = wp_nonce_url( add_query_arg( 'jobus_dismiss_update_notice', '1' ), 'jobus_dismiss_update' );
		?>
		<div class="notice notice-info is-dismissible" style="border-left-color: #31795A; padding: 12px 16px;">
			<p style="margin: 0 0 6px;">
				<strong style="color: #31795A;">
					<span class="dashicons dashicons-megaphone" style="vertical-align: middle; margin-right: 4px;"></span>
					<?php printf( esc_html__( 'Jobus v%s is Active!', 'jobus' ), esc_html( JOBUS_VERSION ) ); ?>
				</strong>
			</p>
			<p style="margin: 0; color: #475569; font-size: 13px;">
				<?php esc_html_e( 'Enjoy improved theme styling compatibility, dark mode support, and seamless first-run setup. Make sure to keep your installation updated for maximum performance.', 'jobus' ); ?>
				<a href="<?php echo esc_url( $dismiss_url ); ?>" style="margin-left: 8px; color: #31795A; text-decoration: underline;">
					<?php esc_html_e( 'Dismiss', 'jobus' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Enqueue assets for Free vs Pro comparison page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_free_vs_pro_assets( string $hook ): void {
		if ( 'jobus_job_page_jobus-free-vs-pro' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'jobus-free-vs-pro',
			JOBUS_URL . '/assets/css/free-vs-pro.css',
			[ 'dashicons' ],
			JOBUS_VERSION
		);

		wp_enqueue_script(
			'jobus-free-vs-pro',
			JOBUS_URL . '/assets/js/free-vs-pro.js',
			[ 'jquery' ],
			JOBUS_VERSION,
			true
		);
	}

	/**
	 * Suppress external admin notices on the Free vs Pro page for clean presentation.
	 *
	 * @return void
	 */
	public function hide_free_vs_pro_notices(): void {
		$screen = get_current_screen();

		if ( ! $screen || 'jobus_job_page_jobus-free-vs-pro' !== $screen->id ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

		echo '<style id="jobus-fvp-admin-clean">
			body.jobus_job_page_jobus-free-vs-pro #wpbody-content > h1,
			body.jobus_job_page_jobus-free-vs-pro .update-nag,
			body.jobus_job_page_jobus-free-vs-pro .fs-notice,
			body.jobus_job_page_jobus-free-vs-pro .fs-secure-notice,
			body.jobus_job_page_jobus-free-vs-pro .fs-terms { display: none !important; }
		</style>';
	}

	/**
	 * Render Free vs Pro comparison screen.
	 *
	 * @return void
	 */
	public function render_free_vs_pro_page(): void {
		include JOBUS_PATH . '/Admin/templates/free-vs-pro.php';
	}
}

