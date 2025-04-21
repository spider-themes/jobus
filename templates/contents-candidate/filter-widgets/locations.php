<?php
/**
 * Location filter widget for the candidate listing
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/filter-widgets/locations.php.
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

$term_location = get_terms( array(
	'taxonomy'   => 'jobus_candidate_location',
) );

if ( ! empty( $term_location ) ) {
	?>
    <select class="nice-select" name="candidate_locations[]">
        <option value="" disabled selected><?php esc_html_e( 'Select Location', 'jobus' ); ?></option>
		<?php
		$searched_opt = jobus_search_terms( 'candidate_locations' );
		foreach ( $term_location as $term ) {
			$selected = in_array($term->slug, $searched_opt) ? ' selected' : '';
			echo '<option value="' . esc_attr($term->slug) . '"' . esc_attr($selected) . '>' . esc_html($term->name) . '</option>';
		}
		?>
    </select>
	<?php
}