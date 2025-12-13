<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Template variables passed from Blocks::register_form_block_render()
$nonce                              = $nonce ?? wp_create_nonce( 'jobus_register_form_nonce' );
$candidate_placeholder_username     = $candidate_username ?? 'Enter Your Name';
$candidate_placeholder_email        = $candidate_email ?? 'Enter Your Email';
$candidate_placeholder_pass         = $candidate_pass ?? 'Enter Your Password';
$candidate_placeholder_confirm_pass = $candidate_confirm_pass ?? 'Enter Your Confirm Password';

$employer_placeholder_username     = $employer_username ?? 'Enter Your Name';
$employer_placeholder_email        = $employer_email ?? 'Enter Your Email';
$employer_placeholder_pass         = $employer_pass ?? 'Enter Your Password';
$employer_placeholder_confirm_pass = $employer_confirm_pass ?? 'Enter Your Confirm Password';

$singin_btn_label = jobus_opt( 'signin_btn_label' );
$singin_btn_url   = jobus_opt( 'signin_btn_url' );
$redirect_url     = ! empty( $redirect_url ) ? esc_url_raw( $redirect_url ) : '';

// Get error message and tab from URL
$jobus_error = ! empty( $_GET['jobus_error'] ) ? sanitize_text_field( urldecode( $_GET['jobus_error'] ) ) : '';
$jobus_tab   = ! empty( $_GET['jobus_tab'] ) ? sanitize_text_field( $_GET['jobus_tab'] ) : 'candidate';
$is_employer_tab = ( $jobus_tab === 'employer' );
?>
    <section class="registration-section jbs-position-relative jbs-pt-100 jbs-lg-pt-80 jbs-pb-150 jbs-lg-pb-80">
        <div class="user-data-form">

            <div class="jbs-text-center">
                <h2><?php esc_html_e( 'Create Account', 'jobus' ); ?></h2>
            </div>

            <div class="form-wrapper jbs-m-auto">
				<?php if ( ! empty( $jobus_error ) ) : ?>
                    <div class="jobus-registration-error jbs-alert jbs-alert-danger jbs-mt-20" role="alert">
						<?php echo esc_html( $jobus_error ); ?>
                    </div>
				<?php endif; ?>
                <ul class="jbs-nav jbs-nav-tabs jbs-border-0 jbs-w-100 jbs-mt-30" role="tablist">
                    <li class="jbs-nav-item" role="presentation">
                        <button class="jbs-nav-link <?php echo ! $is_employer_tab ? 'active' : ''; ?>" data-jbs-toggle="tab" data-jbs-target="#fc1" role="tab">
							<?php esc_html_e( 'Candidates', 'jobus' ); ?>
                        </button>
                    </li>
                    <li class="jbs-nav-item" role="presentation">
                        <button class="jbs-nav-link <?php echo $is_employer_tab ? 'active' : ''; ?>" data-jbs-toggle="tab" data-jbs-target="#fc2" role="tab">
							<?php esc_html_e( 'Employer', 'jobus' ); ?>
                        </button>
                    </li>
                </ul>
                <div class="jbs-tab-content jbs-mt-40">
                    <div class="jbs-tab-pane jbs-fade <?php echo ! $is_employer_tab ? 'jbs-show active' : ''; ?>" role="tabpanel" id="fc1">
                        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" name="jobus-candidate-registration-form"
                              id="jobus-candidate-registration-form" method="post">
							<?php wp_nonce_field( 'register_candidate_action', 'register_candidate_nonce' ); ?>
                            <input type="hidden" name="action" value="register_candidate">
                            <?php if ( ! empty( $redirect_url ) ) : ?>
                                <input type="hidden" name="redirect_url" value="<?php echo esc_attr( $redirect_url ); ?>">
                            <?php endif; ?>
                            <div class="jbs-row">
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-25">
                                        <label for="candidate_username"><?php esc_html_e( 'Name*', 'jobus' ); ?></label>
                                        <input type="text" name="candidate_username" id="candidate_username"
                                               placeholder="<?php echo esc_attr( $candidate_placeholder_username ) ?>" required>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-25">
                                        <label for="candidate_email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                                        <input type="email" name="candidate_email" id="candidate_email"
                                               placeholder="<?php echo esc_attr( $candidate_placeholder_email ) ?>" required>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-20">
                                        <label for="candidate_pass"><?php esc_html_e( 'Password*', 'jobus' ); ?></label>
                                        <input type="password" name="candidate_pass" id="candidate_pass"
                                               placeholder="<?php echo esc_attr( $candidate_placeholder_pass ) ?>" class="pass_log_id" required>
                                        <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon-eye.svg' ) ?>" alt="<?php esc_attr_e( 'eye', 'jobus' ); ?>">
                                        </span>
                                    </span>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-20">
                                        <label for="candidate_confirm_pass"><?php esc_html_e( 'Confirm Password*', 'jobus' ); ?></label>
                                        <input type="password" name="candidate_confirm_pass" id="candidate_confirm_pass"
                                               placeholder="<?php echo esc_attr( $candidate_placeholder_confirm_pass ) ?>" class="pass_log_id" required>
                                        <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon-eye.svg' ) ?>" alt="<?php esc_attr_e( 'eye', 'jobus' ); ?>">
                                        </span>
                                    </span>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="agreement-checkbox jbs-d-flex jbs-justify-content-between jbs-align-items-center">
                                        <div>
                                            <input type="checkbox" id="remember">
                                            <label for="remember"><?php esc_html_e( 'By hitting the "Register" button, you agree to the', 'jobus' ); ?>
                                                <a href="#"><?php esc_html_e( 'Terms conditions', 'jobus' ); ?></a> & <a
                                                        href="#"><?php esc_html_e( 'Privacy Policy', 'jobus' ); ?></a>
                                            </label>
                                        </div>
                                    </div> <!-- /.agreement-checkbox -->
                                </div>
                                <div class="jbs-col-12">
                                    <button type="submit" class="btn-eleven jbs-fw-500 tran3s jbs-d-block jbs-mt-20"><?php esc_html_e( 'Register',
											'jobus' ); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="jbs-tab-pane jbs-fade <?php echo $is_employer_tab ? 'jbs-show active' : ''; ?>" role="tabpanel" id="fc2">
                        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" name="jobus-employer-registration-form"
                              id="jobus-employer-registration-form" method="post">
							<?php wp_nonce_field( 'register_employer_action', 'register_employer_nonce' ); ?>
                            <input type="hidden" name="action" value="register_employer">
                            <?php if ( ! empty( $redirect_url ) ) : ?>
                                <input type="hidden" name="redirect_url" value="<?php echo esc_attr( $redirect_url ); ?>">
                            <?php endif; ?>
                            <div class="jbs-row">
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-25">
                                        <label for="employer_username"><?php esc_html_e( 'Name*', 'jobus' ); ?></label>
                                        <input type="text" name="employer_username" id="employer_username"
                                               placeholder="<?php echo esc_attr( $employer_placeholder_username ) ?>" required>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-25">
                                        <label for="employer_email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                                        <input type="email" name="employer_email" id="employer_email"
                                               placeholder="<?php echo esc_attr( $employer_placeholder_email ) ?>" required>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-20">
                                        <label for="employer_pass"><?php esc_html_e( 'Password*', 'jobus' ); ?></label>
                                        <input type="password" name="employer_pass" id="employer_pass"
                                               placeholder="<?php echo esc_attr( $employer_placeholder_pass ) ?>" class="pass_log_id" required>
                                        <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon-eye.svg' ) ?>" alt="<?php esc_attr_e( 'eye', 'jobus' ); ?>">
                                        </span>
                                    </span>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="input-group-meta jbs-position-relative jbs-mb-20">
                                        <label for="employer_confirm_pass">Confirm Password*</label>
                                        <input type="password" name="employer_confirm_pass" id="employer_confirm_pass"
                                               placeholder="<?php echo esc_attr( $employer_placeholder_confirm_pass ) ?>" class="pass_log_id" required>
                                        <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon-eye.svg' ) ?>" alt="<?php esc_attr_e( 'eye', 'jobus' ); ?>">
                                        </span>
                                    </span>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="agreement-checkbox jbs-d-flex jbs-justify-content-between jbs-align-items-center">
                                        <div>
                                            <input type="checkbox" id="remember2">
                                            <label for="remember2"><?php esc_html_e( 'By hitting the "Register" button, you agree to the', 'jobus' ); ?>
                                                <a href="#">
													<?php esc_html_e( 'Terms conditions', 'jobus' ); ?>
                                                </a> &
                                                <a href="#"><?php esc_html_e( 'Privacy Policy', 'jobus' ); ?></a>
                                            </label>
                                        </div>
                                    </div> <!-- /.agreement-checkbox -->
                                </div>
                                <div class="jbs-col-12">
                                    <button type="submit" class="btn-eleven jbs-fw-500 tran3s jbs-d-block jbs-mt-20">
										<?php esc_html_e( 'Register', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
				<?php if ( ! empty( $singin_btn_label ) && ( $singin_btn_url ) ) : ?>
                    <p class="jbs-text-center jbs-mt-20 jbs-mb-0"><?php esc_html_e( 'Have an account?', 'jobus' ); ?>
                        <a href="<?php echo esc_url( $singin_btn_url ) ?>" class="jbs-fw-500" data-jbs-toggle="modal" data-jbs-target="#jobusLoginModal">
							<?php echo esc_html( $singin_btn_label ); ?>
                        </a>
                    </p>
				<?php endif; ?>
            </div>
        </div>
    </section>
<?php
// Output hidden nonce field after template content
echo '<input type="hidden" name="jobus_nonce" value="' . esc_attr( $nonce ) . '">';

