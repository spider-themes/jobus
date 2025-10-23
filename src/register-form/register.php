<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Placeholder values from block attributes
$candidate_placeholder_username     = $attributes['candidate_username'] ?? '';
$candidate_placeholder_email        = $attributes['candidate_email'] ?? '';
$candidate_placeholder_pass         = $attributes['candidate_pass'] ?? '';
$candidate_placeholder_confirm_pass = $attributes['candidate_confirm_pass'] ?? '';

$employer_placeholder_username     = $attributes['employer_username'] ?? '';
$employer_placeholder_email        = $attributes['employer_email'] ?? '';
$employer_placeholder_pass         = $attributes['employer_pass'] ?? '';
$employer_placeholder_confirm_pass = $attributes['employer_confirm_pass'] ?? '';
?>
<section class="registration-section position-relative pt-100 jbs-lg-pt-80 jbs-pb-150 jbs-lg-pb-80">
    <div class="user-data-form">

        <div class="jbs-text-center">
            <h2><?php esc_html_e( 'Create Account', 'jobus' ); ?></h2>
        </div>

        <div class="form-wrapper m-auto">
            <ul class="jbs-nav jbs-nav-tabs border-0 w-100 jbs-mt-30" role="tablist">
                <li class="jbs-nav-item" role="presentation">
                    <button class="jbs-nav-link active" data-jbs-toggle="tab" data-jbs-target="#fc1" role="tab">
                        <?php esc_html_e( 'Candidates', 'jobus' ); ?>
                    </button>
                </li>
                <li class="jbs-nav-item" role="presentation">
                    <button class="jbs-nav-link" data-jbs-toggle="tab" data-jbs-target="#fc2" role="tab">
                        <?php esc_html_e( 'Employer', 'jobus' ); ?>
                    </button>
                </li>
            </ul>
            <div class="jbs-tab-content jbs-mt-40">
                <div class="jbs-tab-pane jbs-fade jbs-show active" role="tabpanel" id="fc1">
                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" name="jobus-candidate-registration-form"
                          id="jobus-candidate-registration-form" method="post">
                        <?php wp_nonce_field( 'register_candidate_action', 'register_candidate_nonce' ); ?>
                        <input type="hidden" name="action" value="register_candidate">
                        <div class="jbs-row">
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-25">
                                    <label for="candidate_username"><?php esc_html_e( 'Name*', 'jobus' ); ?></label>
                                    <input type="text" name="candidate_username" id="candidate_username"
                                           placeholder="<?php echo esc_attr( $candidate_placeholder_username ) ?>" required>
                                </div>
                            </div>
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-25">
                                    <label for="candidate_email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                                    <input type="email" name="candidate_email" id="candidate_email"
                                           placeholder="<?php echo esc_attr( $candidate_placeholder_email ) ?>" required>
                                </div>
                            </div>
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-20">
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
                                <div class="input-group-meta position-relative jbs-mb-20">
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
                                <button type="submit" class="btn-eleven jbs-fw-500 tran3s jbs-d-block jbs-mt-20"><?php esc_html_e( 'Register', 'jobus' ); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.tab-pane -->
                <div class="jbs-tab-pane jbs-fade" role="tabpanel" id="fc2">
                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" name="jobus-employer-registration-form"
                          id="jobus-employer-registration-form" method="post">
                        <?php wp_nonce_field( 'register_employer_action', 'register_employer_nonce' ); ?>
                        <input type="hidden" name="action" value="register_employer">
                        <div class="jbs-row">
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-25">
                                    <label for="employer_username"><?php esc_html_e( 'Name*', 'jobus' ); ?></label>
                                    <input type="text" name="employer_username" id="employer_username"
                                           placeholder="<?php echo esc_attr( $employer_placeholder_username ) ?>" required>
                                </div>
                            </div>
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-25">
                                    <label for="employer_email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                                    <input type="email" name="employer_email" id="employer_email"
                                           placeholder="<?php echo esc_attr( $employer_placeholder_email ) ?>" required>
                                </div>
                            </div>
                            <div class="jbs-col-12">
                                <div class="input-group-meta position-relative jbs-mb-20">
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
                                <div class="input-group-meta position-relative jbs-mb-20">
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
                                        <input type="checkbox" id="remember">
                                        <label for="remember"><?php esc_html_e( 'By hitting the "Register" button, you agree to the', 'jobus' ); ?>
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
            <p class="jbs-text-center jbs-mt-20 jbs-mb-0"><?php esc_html_e( 'Have an account?', 'jobus' ); ?>
                <a href="javascript:void(0)" class="jbs-fw-500" data-jbs-toggle="modal" data-jbs-target="#loginModal"><?php esc_html_e( 'Sign In', 'jobus' ); ?></a>
            </p>
        </div>
    </div>
</section>