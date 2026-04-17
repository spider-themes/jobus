<?php
/*
 * Location filter widget for the job listing
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$term_location = get_terms( array(
	'taxonomy'   => $taxonomy,
) );

if ( ! empty( $term_location ) ) {
	?>
    <?php $searched_opt = jobus_search_terms( $taxonomy ); ?>
    <select class="jbs-nice-select" name="<?php echo esc_attr($taxonomy) ?>[]">
        <option value="" <?php selected( empty( $searched_opt ), true ); ?>><?php esc_html_e( 'Select Location', 'jobus' ); ?></option>
		<?php
		foreach ( $term_location as $term ) {
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