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

<div class="jbs-logout-from jbs-dashboard-body">
    <div class="jbs-logout_from">
        <div class="jbs-container">
            <div class="jbs-user-data-form jbs-modal-content jbs-shadow-sm">
	            <?php echo do_shortcode('[jobus_logout_form]'); ?>
            </div>
        </div>
    </div>
</div>