<?php
/**
 * Category - This template is used to display a dropdown list of job categories for filtering job listings.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/filter-widgets/categories.php.
 *
 *  HOWEVER, on occasion Jobus will need to update template files and you
 *  (the theme developer) will need to copy the new files to your theme to
 *  maintain compatibility. We try to do this as little as possible, but it does
 *  happen. When this occurs the version of the template file will be bumped and
 *  the readme will list any important changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$term_cats = get_terms( array(
	'taxonomy' => $taxonomy,
) );

if ( ! empty( $term_cats ) ) {
	?>
    <?php $searched_opt = jobus_search_terms($taxonomy); ?>
    <select class="jbs-nice-select" name="<?php echo esc_attr($taxonomy) ?>[]">
        <option value="" <?php selected( empty( $searched_opt ), true ); ?>> <?php esc_html_e( 'Select Category', 'jobus' ); ?> </option>
		<?php
		foreach ( $term_cats as $term ) {
			$is_selected = in_array( $term->slug, $searched_opt, true );
			?>
			<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $is_selected, true ); ?>>
				<?php echo esc_html( $term->name ); ?>
			</option>
			<?php
		}
		?>
    </select>
	<?php
}
