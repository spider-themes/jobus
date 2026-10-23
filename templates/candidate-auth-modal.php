<?php
/**
 * Candidate Auth Prompt Modal
 *
 * Prompts unauthenticated visitors to log in or register before downloading
 * a candidate's resume or contacting them directly.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$current_url    = get_permalink();
$login_url      = wp_login_url( $current_url );
$dashboard_url  = function_exists( 'jobus_opt' ) ? jobus_opt( 'signin_btn_url', '' ) : '';
$signin_action  = ! empty( $dashboard_url ) ? esc_url( add_query_arg( 'redirect_to', urlencode( $current_url ), $dashboard_url ) ) : esc_url( $login_url );
$signup_url     = function_exists( 'jobus_opt' ) ? jobus_opt( 'login_signup_btn_url', wp_registration_url() ) : wp_registration_url();
?>

<div class="jbs-modal jbs-fade jbs-login_from" id="candidateAuthPromptModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="candidateAuthModalLabel" aria-hidden="true">
    <div class="jbs-modal-dialog jbs-modal-dialog-centered">
        <div class="jbs-container">
            <div class="jbs-user-data-form jbs-modal-content">
                <button type="button" class="jbs-btn-close" data-jbs-dismiss="modal" data-target="#candidateAuthPromptModal" aria-label="<?php esc_attr_e( 'Close', 'jobus' ); ?>"></button>
                <div class="jbs-text-center">
                    <h2 id="candidateAuthModalLabel" class="jbs-mb-15">
                        <?php esc_html_e( 'Sign In to Access Candidate Details', 'jobus' ); ?>
                    </h2>
                    <p class="jbs-mb-30">
                        <?php esc_html_e( 'Please log in with an Employer or Recruiter account to download candidate resumes and send direct messages.', 'jobus' ); ?>
                    </p>
                    <div class="jbs-d-grid jbs-gap-2">
                        <a href="<?php echo esc_url( $signin_action ); ?>" class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center jbs-tran3s">
                            <?php esc_html_e( 'Sign In', 'jobus' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $signup_url ); ?>" class="jbs-btn-nine jbs-fw-500 jbs-text-dark jbs-w-100 jbs-text-center jbs-tran3s jbs-mt-10">
                            <?php esc_html_e( 'Create an Account', 'jobus' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
