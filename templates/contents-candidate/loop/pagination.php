<?php
/**
 * Pagination - Show numbered pagination for candidate listings
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/loop/pagination.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="pt-20 d-sm-flex align-items-center justify-content-between">

	<?php jobus_showing_post_result_count($candidate_query) ?>

	<?php jobus_pagination($candidate_query, 'jobus_pagination_two', '<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'); ?>

</div>
