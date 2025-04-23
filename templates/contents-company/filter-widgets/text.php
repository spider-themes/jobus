<?php
/**
 * This template is used to display a text input field for filtering company listings by name.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-company/filter-widgets/text.php.
 *
 * However, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="input-box position-relative">
    <input type="text" name="s" value="<?php echo get_search_query(); ?>"
           placeholder="<?php esc_attr_e( 'Company Name', 'jobus' ); ?>">
    <button><i class="bi bi-search"></i></button>
</div>
