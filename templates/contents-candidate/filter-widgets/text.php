<?php
/**
 * Search form filter widget for candidate specifications.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/filter-widgets/text.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="input-box position-relative">
    <input type="text" name="s" id="searchInput" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_html_e('Name or keyword', 'jobus') ?>">
    <button><i class="bi bi-search"></i></button>
</div>