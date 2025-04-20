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
    <input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_html_e('Name or keyword', 'jobus') ?>">
    <button><i class="bi bi-search"></i></button>
</div>