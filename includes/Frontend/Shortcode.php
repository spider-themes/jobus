<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Shortcode
 */
class Shortcode {

	/**
	 * Shortcode constructor.
	 *
	 * Registers shortcodes for job and company archives.
	 */
	public function __construct() {
		// Get feature toggle options
		$options = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

		add_shortcode( 'jobus_job_archive', [ $this, 'job_page_shortcode' ] );
		if ( $enable_company || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			add_shortcode( 'jobus_company_archive', [ $this, 'company_page_shortcode' ] );
		}
		if ( $enable_candidate || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			add_shortcode( 'jobus_candidate_archive', [ $this, 'candidate_page_shortcode' ] );
		}
		add_shortcode( 'jobus_login_form', [ $this, 'login_form_shortcode' ] );
		add_shortcode( 'jobus_logout_form', [ $this, 'logout_form_shortcode' ] );
	}

	/**
	 * Job Page Shortcode Handler.
	 *
	 * Generates the HTML content for the job archive page.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return string          Generated HTML content.
	 */
	public function job_page_shortcode( array $atts, string $content = '' ): string {
		// Parse shortcode attributes with defaults
		$atts = shortcode_atts( array(
			'job_layout' => '', // Empty means use global setting
		), $atts, 'jobus_job_archive' );

		ob_start();
		self::job_page_layout( $atts );
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Company Page Shortcode Handler.
	 *
	 * Generates the HTML content for the company archive page.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return string Generated HTML content.
	 */
	public function company_page_shortcode( array $atts, string $content = '' ): string {
		// Parse shortcode attributes with defaults
		$atts = shortcode_atts( array(
			'company_layout' => '', // Empty means use global setting
		), $atts, 'jobus_company_archive' );

		ob_start();
		self::company_page_layout( $atts );
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Candidate Page Shortcode Handler.
	 *
	 * Generates the HTML content for the Candidate archive page.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return string Generated HTML content.
	 */
	public function candidate_page_shortcode( array $atts, string $content = '' ): string {
		// Parse shortcode attributes with defaults
		$atts = shortcode_atts( array(
			'candidate_layout' => '', // Empty means use global setting
		), $atts, 'jobus_candidate_archive' );

		ob_start();
		self::candidate_page_layout( $atts );
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Login Form Shortcode Handler.
	 *
	 * Usage: [jobus_login_form redirect="" signup_link=""]
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Login form HTML.
	 */
	public function login_form_shortcode( array $atts = [] ): string {
		// Verify nonce on form submission
		if ( isset( $_POST['jobus_login_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jobus_login_nonce'] ) ), 'jobus_login_action' ) ) {
				wp_die( esc_html__( 'Security check failed!', 'jobus' ) );
			}
		}

		// If user already logged in, return a message
		if ( is_user_logged_in() ) {
			return '<div class="login-already">' . esc_html__( 'You are already logged in.', 'jobus' ) . '</div>';
		}

		// Extract shortcode attributes with defaults
		$atts = shortcode_atts( array(
			'redirect'             => '',
			'signup_link'          => jobus_opt( 'login_signup_btn_url' ),
			'signup_label'         => jobus_opt( 'login_signup_btn_label' ),
			'submit_label'         => esc_html__( 'Login', 'jobus' ),
			'username_label'       => esc_html__( 'Username/Email*', 'jobus' ),
			'password_label'       => esc_html__( 'Password*', 'jobus' ),
			'remember_label'       => esc_html__( 'Keep me logged in', 'jobus' ),
			'username_placeholder' => esc_attr__( 'Enter username or email', 'jobus' ),
			'password_placeholder' => esc_attr__( 'Enter Password', 'jobus' ),
		), $atts );

		// Set redirect URL with fallback to admin URL
		$redirect_url = ! empty( $atts['redirect'] ) ? esc_url_raw( $atts['redirect'] ) : admin_url();

        // Fetch plugin options
        $plugin_options = get_option('jobus_opt', array());

        // Check if demo credentials feature is enabled
        $enable_demo = isset($plugin_options['enable_demo_credentials']) ? (bool)$plugin_options['enable_demo_credentials'] : false;

        // Fetch demo credentials (empty by default)
        $demo_candidate = isset($plugin_options['login_demo_candidate']) ? trim($plugin_options['login_demo_candidate']) : '';
        $demo_employer = isset($plugin_options['login_demo_employer']) ? trim($plugin_options['login_demo_employer']) : '';
        $demo_password = isset($plugin_options['login_demo_password']) ? trim($plugin_options['login_demo_password']) : '';

        // Determine whether to show the demo block
        $show_demo = $enable_demo && (!empty($demo_candidate) || !empty($demo_employer) || !empty($demo_password));

            ob_start();
            ?>
            <div class="login-form-wrapper section-padding">
                <?php if ($show_demo) : ?>
                    <div class="login-form-demo jbs-text-start">
                        <?php esc_html_e('Username', 'jobus'); ?>:
                        <strong><?php echo esc_html($demo_candidate); ?></strong>
                        <?php esc_html_e('or', 'jobus'); ?>
                        <strong><?php echo esc_html($demo_employer); ?></strong><br>
                        <?php esc_html_e('Password', 'jobus'); ?>:
                        <strong><?php echo esc_html($demo_password); ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div class="login-form-header jbs-text-center">
                <h2 class="form-title"><?php esc_html_e( 'Hi, Welcome Back!', 'jobus' ); ?></h2>
                <p class="form-subtitle">
					<?php esc_html_e( 'Still don\'t have an account?', 'jobus' ); ?>
                    <a href="<?php echo esc_url( $atts['signup_link'] ); ?>" class="signup-link">
						<?php echo esc_html( $atts['signup_label'] ); ?>
                    </a>
                </p>
            </div>
            <div class="form-wrapper login-form-box m-auto">
                <form name="jobus_login_form" id="jobus_login_form" action="<?php echo esc_url( wp_login_url() ); ?>" method="post" class="mt-10">
                    <div class="row">
                        <div class="jbs-col-12">
                            <div class="input-group-meta jbs-position-relative jbs-mb-25">
                                <label for="user_login"><?php echo esc_html( $atts['username_label'] ); ?></label>
                                <input type="text" name="log" id="user_login" class="input" value=""
                                       placeholder="<?php echo esc_attr( $atts['username_placeholder'] ); ?>" autocomplete="username">
                            </div>
                        </div>
                        <div class="jbs-col-12">
                            <div class="input-group-meta jbs-position-relative jbs-mb-20">
                                <label for="user_pass"><?php echo esc_html( $atts['password_label'] ); ?></label>
                                <input type="password" name="pwd" id="user_pass" class="input pass_log_id" value=""
                                       placeholder="<?php echo esc_attr( $atts['password_placeholder'] ); ?>" autocomplete="current-password">
                                <span class="placeholder_icon">
                                    <span class="passVicon">
                                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/view.svg' ); ?>"
                                             alt="<?php esc_attr_e( 'Toggle password visibility', 'jobus' ); ?>" class="eye-icon">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="jbs-col-12">
                            <div class="agreement-checkbox jbs-d-flex jbs-justify-content-between jbs-align-items-center">
                                <div>
                                    <input name="rememberme" type="checkbox" id="rememberme" value="forever">
                                    <label for="rememberme"><?php echo esc_html( $atts['remember_label'] ); ?></label>
                                </div>
                                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="reset-link">
									<?php esc_html_e( 'Forget Password?', 'jobus' ); ?>
                                </a>
                            </div>
                        </div>
                        <div class="jbs-col-12">
                            <button type="submit" name="wp-submit" id="wp-submit" class="btn-eleven jbs-fw-500 tran3s jbs-d-block jbs-mt-20">
								<?php echo esc_html( $atts['submit_label'] ); ?>
                            </button>
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>">
                        </div>
                    </div>
					<?php wp_nonce_field( 'jobus_login_action', 'jobus_login_nonce' ); ?>
                </form>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Logout Form Shortcode Handler.
	 *
	 * Usage: [jobus_logout_form]
	 *
	 * @return string Logout form HTML.
	 */
    public function logout_form_shortcode(): string {
        ob_start();

        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $page_url     = home_url('/');

            // Map role to shortcode
            $role_shortcode_map = [
                'jobus_candidate' => '[jobus_candidate_dashboard]',
                'jobus_employer'  => '[jobus_employer_dashboard]',
            ];

            foreach ( $role_shortcode_map as $role => $shortcode ) {
                if ( in_array( $role, (array) $current_user->roles, true ) ) {
                    $dashboard_page = get_posts([
                        'post_type'      => 'page',
                        'posts_per_page' => 1,
                        'post_status'    => 'publish',
                        'fields'         => 'ids',
                        's'              => $shortcode,
                    ]);

                    if ( ! empty( $dashboard_page ) ) {
                        $page_url = trailingslashit( get_permalink( $dashboard_page[0] ) );
                    }
                    break;
                }
            }
            ?>
            <div class="text-center">
                <h2 class="name">
                    <?php
                    /* translators: %s: user display name */
                    echo esc_html( sprintf( esc_html__( 'Welcome, %s!', 'jobus' ), esc_html( $current_user->display_name ) ) );
                    ?>
                </h2>
                <p><?php esc_html_e( 'You are currently logged in to your account.', 'jobus' ); ?></p>
                <p><?php esc_html_e( 'If you wish to log out, please click the button below.', 'jobus' ); ?></p>
                <div class="user-action-buttons">
                    <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ) ?>" class="btn-three">
                        <?php esc_html_e( 'Logout', 'jobus' ) ?>
                    </a>
                    <a href="<?php echo esc_url( $page_url ) ?>" class="btn-four">
                        <?php esc_html_e( 'Dashboard', 'jobus' ) ?>
                    </a>
                </div>
                <p class="mt-3">
                    <?php esc_html_e( 'Or return to the', 'jobus' ); ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ) ?>"> <?php esc_html_e( 'Homepage', 'jobus' ) ?> </a>
                </p>
            </div>
            <?php
        }

        return ob_get_clean();
    }

	/**
	 * Displays the job archive page layout.
	 *
	 * @param array $args Additional arguments for customizing the layout.
	 *
	 * @return void
	 */
	public static function job_page_layout( array $args = [] ): void {
		if ( ! is_admin() ) {
			jobus_get_template( 'archive-job.php', [
				'jobus_job_archive_layout' => $args['job_layout'],
				'is_shortcode'             => true, // Flag to prevent header/footer output
			] );
		}
	}

	/**
	 * Displays the company archive page layout.
	 *
	 * @param array $args Additional arguments for customizing the layout.
	 *
	 * @return void
	 */
	public static function company_page_layout( array $args = [] ): void {
		if ( ! is_admin() ) {
			jobus_get_template( 'archive-company.php', [
				'jobus_company_archive_layout' => $args['company_layout'],
				'is_shortcode'                 => true, // Flag to prevent header/footer output
			] );
		}
	}

	/**
	 * Displays the candidate archive page layout.
	 *
	 * @param array $args Additional arguments for customizing the layout.
	 *
	 * @return void
	 */
	public static function candidate_page_layout( array $args = [] ) {
		if ( ! is_admin() ) {
			jobus_get_template( 'archive-candidate.php', [
				'jobus_candidate_archive_layout' => $args['candidate_layout'],
				'is_shortcode'                   => true, // Flag to prevent header/footer output
			] );
		}
	}
}
