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

<div class="input-box position-relative">
	<input type="text" name="s" id="searchInput" value="<?php echo get_search_query(); ?>" placeholder="Search by Keywords">
	<button><i class="bi bi-search"></i></button>
</div>