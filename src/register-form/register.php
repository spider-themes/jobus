<?php
// Placeholder values from block attributes
$candidate_placeholder_username = $attributes['candidate_username'] ?? '';
$candidate_placeholder_email = $attributes['candidate_email'] ?? '';
$candidate_placeholder_pass = $attributes['candidate_pass'] ?? '';
$candidate_placeholder_confirm_pass = $attributes['candidate_confirm_pass'] ?? '';

$employer_placeholder_username = $attributes['employer_username'] ?? '';
$employer_placeholder_email = $attributes['employer_email'] ?? '';
$employer_placeholder_pass = $attributes['employer_pass'] ?? '';
$employer_placeholder_confirm_pass = $attributes['employer_confirm_pass'] ?? '';
?>
<section class="registration-section position-relative pt-100 lg-pt-80 pb-150 lg-pb-80">
    <div class="user-data-form">

        <div class="text-center">
            <h2><?php esc_html_e('Create Account', 'jobus'); ?></h2>
        </div>

        <div class="form-wrapper m-auto">
            <ul class="nav nav-tabs border-0 w-100 mt-30" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#fc1" role="tab">
                        <?php esc_html_e('Candidates', 'jobus'); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fc2" role="tab">
                        <?php esc_html_e('Employer', 'jobus'); ?>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-40">
                <div class="tab-pane fade show active" role="tabpanel" id="fc1">
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name="jobly-candidate-registration-form" id="jobly-candidate-registration-form" method="post">
                        <?php wp_nonce_field('register_candidate_action', 'register_candidate_nonce'); ?>
                        <input type="hidden" name="action" value="register_candidate">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-25">
                                    <label for="candidate_username"> Name* </label>
                                    <input type="text" name="candidate_username" id="candidate_username" placeholder="<?php echo esc_attr($candidate_placeholder_username) ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-25">
                                    <label for="candidate_email">Email*</label>
                                    <input type="email" name="candidate_email" id="candidate_email" placeholder="<?php echo esc_attr($candidate_placeholder_email) ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-20">
                                    <label for="candidate_pass">Password*</label>
                                    <input type="password" name="candidate_pass" id="candidate_pass" placeholder="<?php echo esc_attr($candidate_placeholder_pass) ?>" class="pass_log_id" required>
                                    <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobus'); ?>">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-20">
                                    <label for="candidate_confirm_pass">Confirm Password*</label>
                                    <input type="password" name="candidate_confirm_pass" id="candidate_confirm_pass" placeholder="<?php echo esc_attr($candidate_placeholder_confirm_pass) ?>" class="pass_log_id" required>
                                    <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobus'); ?>">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="checkbox" id="remember">
                                        <label for="remember"><?php esc_html_e('By hitting the "Register" button, you agree to the'); ?>
                                            <a href="#"><?php esc_html_e('Terms conditions', 'jobus'); ?></a> & <a href="#"><?php esc_html_e('Privacy Policy', 'jobus'); ?></a>
                                        </label>
                                    </div>
                                </div> <!-- /.agreement-checkbox -->
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-eleven fw-500 tran3s d-block mt-20"><?php esc_html_e('Register', 'jobus'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane fade" role="tabpanel" id="fc2">
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name="jobly-employer-registration-form" id="jobly-employer-registration-form" method="post">
                        <?php wp_nonce_field('register_employer_action', 'register_employer_nonce'); ?>
                        <input type="hidden" name="action" value="register_employer">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-25">
                                    <label for="employer_username">Name*</label>
                                    <input type="text" name="employer_username" id="employer_username" placeholder="<?php echo esc_attr($employer_placeholder_username) ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-25">
                                    <label for="employer_email">Email*</label>
                                    <input type="email" name="employer_email" id="employer_email" placeholder="<?php echo esc_attr($employer_placeholder_email) ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-20">
                                    <label for="employer_pass">Password*</label>
                                    <input type="password" name="employer_pass" id="employer_pass" placeholder="<?php echo esc_attr($employer_placeholder_pass) ?>" class="pass_log_id" required>
                                    <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobus'); ?>">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-group-meta position-relative mb-20">
                                    <label for="employer_confirm_pass">Confirm Password*</label>
                                    <input type="password" name="employer_confirm_pass" id="employer_confirm_pass" placeholder="<?php echo esc_attr($employer_placeholder_confirm_pass) ?>" class="pass_log_id" required>
                                    <span class="placeholder_icon">
                                        <span class="passVicon">
                                            <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobus'); ?>">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="checkbox" id="remember">
                                        <label for="remember"><?php esc_html_e('By hitting the "Register" button, you agree to the', 'jobus'); ?>
                                            <a href="#">
                                                <?php esc_html_e('Terms conditions', 'jobus'); ?>
                                            </a> &
                                            <a href="#"><?php esc_html_e('Privacy Policy', 'jobus'); ?></a>
                                        </label>
                                    </div>
                                </div> <!-- /.agreement-checkbox -->
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-eleven fw-500 tran3s d-block mt-20">
                                    <?php esc_html_e('Register', 'jobus'); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.tab-pane -->
            </div>
            <div class="d-flex align-items-center mt-30 mb-10">
                <div class="line"></div>
            </div>
            <p class="text-center mt-10"><?php esc_html_e('Have an account?', 'jobus'); ?>
                <a href="javascript:void(0)" class="fw-500" data-bs-toggle="modal" data-bs-target="#loginModal"><?php esc_html_e('Sign In', 'jobus'); ?></a>
            </p>
        </div>
    </div>
</section>