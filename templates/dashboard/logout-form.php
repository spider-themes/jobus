<?php
/**
 * Template for the logout form in the candidate dashboard.
 *
 * This template is used to display the logout form for candidates who are logged in.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="jobus-logout-from dashboard-body">
    <div class="logout_from">
        <div class="container">
            <div class="user-data-form modal-content shadow-sm">
	            <?php echo do_shortcode('[jobus_logout_form]'); ?>
            </div>
        </div>
    </div>
</div>