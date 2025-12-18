<?php
/**
 * Search form filter widget for job keyword search.
 *
 * This template provides a keyword search input field that allows users
 * to search for jobs by entering keywords.
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
           placeholder="<?php esc_attr_e( 'Search by Keywords', 'jobus' ); ?>">
    <button type="button" class="jbs-border-0">
        <i class="bi bi-search"></i>
    </button>
</div>
