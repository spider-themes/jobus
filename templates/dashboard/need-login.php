<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="jobus-dashboard-message">
    <p><?php esc_html_e( 'Please log in to access your dashboard.', 'jobus' ); ?></p>
    <a href="<?php echo wp_login_url(); ?>" class="jobus-button"><?php esc_html_e( 'Login', 'jobus' ); ?></a>
</div>