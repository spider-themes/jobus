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
	 * Usage: [jobus_login_form]
	 *
	 * @return string Login form HTML.
	 */
	public function login_form_shortcode(): string {
		ob_start();

		if ( ! is_user_logged_in() ) {
			$user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '';
			$password   = $_POST['pwd'] ?? '';
			?>
			<div class="text-center">
				<h2><?php esc_html_e( 'Hi, Welcome Back!', 'jobus' ) ?></h2>
				<p><?php esc_html_e( 'Still don\'t have an account?', 'jobus' ); ?>
					<a href="<?php echo esc_url( jobus_opt( 'login_signup_btn_url' ) ) ?>">
						<?php echo esc_html( jobus_opt( 'login_signup_btn_label' ) ) ?>
					</a>
				</p>
			</div>
			<div class="form-wrapper m-auto">
				<form action="<?php echo esc_url( home_url( '/' ) ) ?>wp-login.php" class="mt-10" name="loginform" id="loginform" method="post">
					<?php wp_nonce_field( 'jobus_login_action', 'jobus_login_nonce' ); ?>
					<div class="row">
						<div class="col-12">
							<div class="input-group-meta position-relative mb-25">
								<label for="user_login"><?php esc_html_e( 'Username/Email*', 'jobus' ); ?></label>
								<input type="text" name="log" id="user_login" value="<?php echo esc_attr( $user_login ) ?>"
									   placeholder="<?php esc_attr_e( 'Enter username or email', 'jobus' ); ?>" autocapitalize="off"
									   autocomplete="username" required>
							</div>
						</div>
						<div class="col-12">
							<div class="input-group-meta position-relative mb-20">
								<label for="user_pass"><?php esc_html_e( 'Password*', 'jobus' ) ?></label>
								<input type="password" name="pwd" id="user_pass"
									   value="<?php echo esc_attr( $password ) ?>"
									   placeholder="<?php esc_attr_e( 'Enter Password', 'jobus' ); ?>"
									   class="pass_log_id" required>
								<span class="placeholder_icon">
									<span class="passVicon">
                                        <img src="<?php echo esc_url(JOBUS_IMG . '/dashboard/icons/view.svg') ?>"
                                             alt="<?php esc_attr_e( 'eye-icon', 'jobus' ); ?>">
									</span>
								</span>
							</div>
						</div>
						<div class="col-12">
							<div class="agreement-checkbox d-flex justify-content-between align-items-center">
								<div>
									<input type="checkbox" name="rememberme" id="rememberme">
									<label for="rememberme"><?php esc_html_e( 'Keep me logged in', 'jobus' ); ?></label>
								</div>
								<a href="<?php echo esc_url( home_url( '/' ) ) . 'wp-login.php?action=lostpassword'; ?>">
									<?php esc_html_e( 'Forget Password?', 'jobus' ); ?>
								</a>
							</div>
						</div>
						<div class="col-12">
							<button type="submit" name="wp-submit" id="wp-submit" class="btn-eleven fw-500 tran3s d-block mt-20"><?php esc_html_e( 'Login',
								'jobus' ); ?></button>
							<input type="hidden" name="redirect_to" value="<?php echo esc_url( admin_url() ); ?>">
						</div>
					</div>
				</form>
			</div>
			<?php
		}

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
					// translators: %s: User's display name
					echo sprintf(
						esc_html__( 'Welcome, %s!', 'jobus' ),
						esc_html( $current_user->display_name )
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
