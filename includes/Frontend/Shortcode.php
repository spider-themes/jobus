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
		add_shortcode( 'jobus_job_archive', [ $this, 'job_page_shortcode' ] );
		add_shortcode( 'jobus_company_archive', [ $this, 'company_page_shortcode' ] );
		add_shortcode( 'jobus_candidate_archive', [ $this, 'candidate_page_shortcode' ] );
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
			if ( ! wp_verify_nonce( wp_unslash($_POST['jobus_login_nonce']), 'jobus_login_action' ) ) {
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
			'signup_link'         => jobus_opt('login_signup_btn_url'),
			'signup_label'        => jobus_opt('login_signup_btn_label'),
			'submit_label'        => esc_html__( 'Login', 'jobus' ),
			'username_label'      => esc_html__( 'Username/Email*', 'jobus' ),
			'password_label'      => esc_html__( 'Password*', 'jobus' ),
			'remember_label'      => esc_html__( 'Keep me logged in', 'jobus' ),
			'username_placeholder'=> esc_attr__( 'Enter username or email', 'jobus' ),
			'password_placeholder'=> esc_attr__( 'Enter Password', 'jobus' ),
		), $atts );

		// Set redirect URL with fallback to admin URL
		$redirect_url = ! empty( $atts['redirect'] ) ? esc_url_raw( $atts['redirect'] ) : admin_url();

		ob_start();
		?>
        <div class="login-form-wrapper section-padding">
            <div class="login-form-header text-center">
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
                        <div class="col-12">
                            <div class="input-group-meta position-relative mb-25">
                                <label for="user_login"><?php echo esc_html( $atts['username_label'] ); ?></label>
                                <input type="text" name="log" id="user_login" class="input" value="" placeholder="<?php echo esc_attr( $atts['username_placeholder'] ); ?>" autocomplete="username">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group-meta position-relative mb-20">
                                <label for="user_pass"><?php echo esc_html( $atts['password_label'] ); ?></label>
                                <input type="password" name="pwd" id="user_pass" class="input pass_log_id" value="" placeholder="<?php echo esc_attr( $atts['password_placeholder'] ); ?>" autocomplete="current-password">
                                <span class="placeholder_icon">
                                    <span class="passVicon">
                                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/view.svg' ); ?>" alt="<?php esc_attr_e( 'Toggle password visibility', 'jobus' ); ?>" class="eye-icon">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                <div>
                                    <input name="rememberme" type="checkbox" id="rememberme" value="forever">
                                    <label for="rememberme"><?php echo esc_html( $atts['remember_label'] ); ?></label>
                                </div>
                                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="reset-link">
                                    <?php esc_html_e( 'Forget Password?', 'jobus' ); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="wp-submit" id="wp-submit" class="btn-eleven fw-500 tran3s d-block mt-20">
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
			?>
			<div class="text-center">
                <h2>
					<?php
					echo sprintf(
					    // translators: %s is the display name of the current user
						esc_html__( 'Welcome, %s!', 'jobus' ),
						esc_html($current_user->display_name)
					);
					?>
                </h2>
                <p><?php esc_html_e('You are currently logged in to your account.', 'jobus'); ?></p>
				<p><?php esc_html_e('If you wish to log out, please click the button below.', 'jobus'); ?></p>
				<a href="<?php echo esc_url(wp_logout_url(home_url('/'))) ?>" class="btn btn-eleven fw-500 tran3s d-block mt-20">
					<?php esc_html_e('Logout', 'jobus') ?>
				</a>
				<p class="mt-3">
					<?php esc_html_e('Or return to the', 'jobus'); ?>
					<a href="<?php echo esc_url(home_url('/')) ?>"> <?php esc_html_e('Homepage', 'jobus') ?> </a>
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
			] );
		}
	}
}
