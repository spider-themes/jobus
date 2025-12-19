<?php
/**
 * Search form filter widget.
 *
 * This template provides a keyword search input field that allows users
 * to search by entering keywords.
 *
 * @package Jobus
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<div class="input-box jbs-position-relative">
    <input type="text"
           name="s"
           id="searchInput"
           value="<?php echo esc_attr( get_search_query() ); ?>"
           placeholder="<?php esc_attr_e( 'Search', 'jobus' ); ?>"
           aria-label="<?php esc_attr_e( 'Search', 'jobus' ); ?>">
    <button type="button" class="jbs-border-0">
        <i class="bi bi-search"></i>
    </button>
</div>
