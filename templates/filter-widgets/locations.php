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
    <select class="nice-select" name="<?php echo esc_attr($taxonomy) ?>[]">
        <option value="" disabled selected><?php esc_html_e( 'Select Location', 'jobus' ); ?></option>
		<?php
		$searched_opt = jobus_search_terms( $taxonomy );
		foreach ( $term_location as $term ) {
			$selected = ( in_array( $term->slug, $searched_opt ) ) ? ' selected' : '';
			?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
			<?php
		}
		?>
    </select>
	<?php
}