<?php
/*
 * Search form filter widget for job specifications.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="input-box jbs-position-relative">
    <input type="text" name="s" id="searchInput" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search by Keywords', 'jobus' ); ?>">
    <button class="jbs-border-0"><i class="bi bi-search"></i></button>
</div>