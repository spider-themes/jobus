<?php
if (!defined('ABSPATH')) {
    exit();
}

$user_input = sanitize_text_field($_POST['user_input']) ?? '';
$password = sanitize_textarea_field($_POST['user_pwd']) ?? '';

if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    ?>
    <div class="modal fade login_from" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="container">
                <div class="user-data-form modal-content shadow-sm">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <h2><?php esc_html_e('Welcome ', 'jobus') ?><?php echo esc_html($current_user->display_name); ?></h2>
                        <p><?php esc_html_e('You are logged in', 'jobus') ?></p>
                        <p> <?php esc_html_e('You can logout from', 'jobus') ?>
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))) ?>"> <?php esc_html_e('here', 'jobus') ?> </a>
                        </p>
                        <p><?php esc_html_e('Or navigate to the website', 'jobus') ?>
                            <a href="<?php echo esc_url(home_url('/')) ?>"> <?php esc_html_e('Homepage', 'jobus') ?> </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="modal fade login_from" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="container">
                <div class="user-data-form modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <h2><?php esc_html_e('Hi, Welcome Back!', 'jobus') ?></h2>
                        <p><?php esc_html_e('Still don\'t have an account?', 'jobus'); ?>
                            <a href="<?php echo esc_url(jobi_opt('login_signup_btn_url')) ?>">
                                <?php echo esc_html(jobi_opt('login_signup_btn_label')) ?>
                            </a>
                        </p>
                    </div>
                    <div class="form-wrapper m-auto">
                        <form action="<?php echo esc_url(home_url('/')) ?>wp-login.php" class="mt-10" name="loginform" id="loginform" method="post">

                            <?php wp_nonce_field('jobi_login_action', 'jobi_login_nonce'); ?>

                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group-meta position-relative mb-25">
                                        <label><?php esc_html_e('Username/Email*', 'jobus'); ?></label>
                                        <input type="text" name="user_input" id="user_input"
                                               value="<?php echo esc_attr($user_input) ?>"
                                               placeholder="<?php esc_attr_e('Enter username or email', 'jobus'); ?>">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group-meta position-relative mb-20">
                                        <label><?php esc_html_e('Password*', 'jobus') ?></label>
                                        <input type="password" name="pwd" id="password"
                                               value="<?php echo esc_attr($password) ?>"
                                               placeholder="<?php esc_attr_e('Enter Password', 'jobus'); ?>"
                                               class="pass_log_id">
                                        <span class="placeholder_icon">
                                            <span class="passVicon">
                                                <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>"
                                                     alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                        <div>
                                            <input type="checkbox" id="remember">
                                            <label for="remember"><?php esc_html_e('Keep me logged in', 'jobus'); ?></label>
                                        </div>
                                        <a href="<?php echo esc_url(home_url('/')) . '/wp-login.php?action=lostpassword'; ?>">
                                            <?php esc_html_e('Forget Password?', 'jobus'); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn-eleven fw-500 tran3s d-block mt-20"><?php esc_html_e('Login', 'jobus'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}