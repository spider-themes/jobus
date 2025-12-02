<?php
/**
 * Template for the login form in the candidate dashboard.
 *
 * This template is used to display the login form for candidates who are not logged in.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


?>
<div class="jobus-login-from dashboard-body">
    <div class="login_from">
        <div class="jbs-container">
            <div class="user-data-form">
	            <?php echo do_shortcode('[jobus_login_form]'); ?>
            </div>
        </div>
    </div>
</div>
