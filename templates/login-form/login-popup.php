<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use jobus\includes\Classes\OAuth\Provider_Manager;
use jobus\includes\Classes\OAuth\Social_Login_Config;

// Template variables passed from block/template loader
$user_input  = ! empty( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
$password    = ! empty( $_POST['pwd'] ) ? sanitize_text_field( wp_unslash( $_POST['pwd'] ) ) : '';

$social_status   = sanitize_key( wp_unslash( $_GET['jobus_social_status'] ?? '' ) );
$social_error    = sanitize_text_field( urldecode( wp_unslash( $_GET['jobus_error'] ?? '' ) ) );
$social_manager  = class_exists( Provider_Manager::class ) ? Provider_Manager::instance() : null;
$social_buttons  = $social_manager ? $social_manager->get_enabled() : [];

if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    ?>
    <div class="jbs-modal jbs-fade jbs-login_from" id="jbsLoginModal" tabindex="-1" aria-hidden="true">
        <div class="jbs-modal-dialog jbs-modal-fullscreen jbs-modal-dialog-centered">
            <div class="jbs-container">
                <div class="jbs-user-data-form jbs-modal-content jbs-shadow-sm">
                    <button type="button" class="jbs-btn-close" data-jbs-dismiss="modal" aria-label="Close"></button>
                    <div class="jbs-text-center">
                        <h2><?php esc_html_e( 'Welcome ', 'jobus' ) ?><?php echo esc_html( $current_user->display_name ); ?></h2>
                        <p><?php esc_html_e( 'You are logged in', 'jobus' ) ?></p>
                        <p>
                            <?php esc_html_e( 'You can logout from', 'jobus' ) ?>
                            <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ) ?>">
                                <?php esc_html_e( 'here', 'jobus' ) ?>
                            </a>
                        </p>
                        <p>
                            <?php esc_html_e( 'Or navigate to the website', 'jobus' ) ?>
                            <a href="<?php echo esc_url( home_url( '/' ) ) ?>">
                                <?php esc_html_e( 'Homepage', 'jobus' ) ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="jbs-modal jbs-fade jbs-login_from" id="jbsLoginModal" tabindex="-1" aria-hidden="true">
        <div class="jbs-modal-dialog jbs-modal-fullscreen jbs-modal-dialog-centered">
            <div class="jbs-container">
                <div class="jbs-user-data-form jbs-modal-content">
                    <button type="button" class="jbs-btn-close" data-jbs-dismiss="modal" aria-label="Close"></button>
                    <div class="jbs-text-center">
                        <h2><?php esc_html_e( 'Hi, Welcome Back!', 'jobus' ) ?></h2>
                        <p>
                            <?php esc_html_e( 'Still don\'t have an account?', 'jobus' ); ?>
                            <?php if ( function_exists( 'jobus_opt' ) ) : ?>
                                <a href="<?php echo esc_url( jobus_opt( 'login_signup_btn_url' ) ) ?>">
                                    <?php echo esc_html( jobus_opt( 'login_signup_btn_label' ) ) ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="jbs-form-wrapper jbs-m-auto">
                        <form action="<?php echo esc_url( home_url( '/' ) ); ?>wp-login.php" class="jbs-mt-10" name="loginform" id="loginform" method="post">

                            <?php wp_nonce_field( 'jobus_login_action', 'jobus_nonce' ); ?>

                            <div class="jbs-row">
                                <div class="jbs-col-12">
                                    <div class="jbs-input-group-meta jbs-position-relative jbs-mb-25">
                                        <label><?php esc_html_e( 'Username/Email*', 'jobus' ); ?></label>
                                        <input type="text" name="log" id="user_input" value="<?php echo esc_attr( $user_input ); ?>" placeholder="<?php esc_attr_e( 'Enter username or email', 'jobus' ); ?>" autocomplete="username" required>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="jbs-input-group-meta jbs-position-relative jbs-mb-20">
                                        <label><?php esc_html_e( 'Password*', 'jobus' ); ?></label>
                                        <input type="password" name="pwd" id="password" value="<?php echo esc_attr( $password ); ?>" placeholder="<?php esc_attr_e( 'Enter Password', 'jobus' ); ?>" class="jbs-pass_log_id" autocomplete="current-password" required>
                                        <span class="jbs-placeholder_icon">
                                        <span class="jbs-passVicon">
                                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon-eye.svg' ); ?>" alt="<?php esc_attr_e( 'eye-icon', 'jobus' ); ?>">
                                        </span>
                                    </span>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <div class="jbs-agreement-checkbox jbs-d-flex jbs-justify-content-between jbs-align-items-center">
                                        <div>
                                            <input type="checkbox" name="rememberme" id="remember" value="forever">
                                            <label for="remember"><?php esc_html_e( 'Keep me logged in', 'jobus' ); ?></label>
                                        </div>
                                        <a href="<?php echo esc_url( home_url( '/' ) ) . '/wp-login.php?action=lostpassword'; ?>">
                                            <?php esc_html_e( 'Forget Password?', 'jobus' ); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="jbs-col-12">
                                    <button class="jbs-btn-eleven jbs-fw-500 jbs-tran3s jbs-d-block jbs-mt-20"><?php esc_html_e( 'Login', 'jobus' ); ?></button>
                                </div>
                            </div>
                        </form>

                        <?php if ( ! empty( $social_buttons ) ) : ?>
                            <div class="jbs-social-auth" data-jbs-social-auth data-context="<?php echo esc_attr( Social_Login_Config::CONTEXT_LOGIN ); ?>">
                                <?php if ( Social_Login_Config::STATUS_ERROR === $social_status && ! empty( $social_error ) ) : ?>
                                    <div class="jbs-social-auth__notice jbs-alert jbs-alert-danger jbs-mb-20" role="alert">
                                        <?php echo esc_html( $social_error ); ?>
                                    </div>
                                <?php endif; ?>

                                <p class="jbs-social-auth__divider"><span><?php esc_html_e( 'OR', 'jobus' ); ?></span></p>

                                <div class="jbs-social-auth__buttons">
                                    <?php foreach ( $social_buttons as $provider ) : ?>
                                        <?php
                                        echo $provider->render_button(
                                            Social_Login_Config::CONTEXT_LOGIN,
                                            home_url( add_query_arg( [] ) )
                                        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
