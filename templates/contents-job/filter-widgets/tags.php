<?php
/*
 * Tags filter widget for the job listing
 *
 * @package Jobus
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$term_tags = get_terms( array(
	'taxonomy' => 'jobus_job_tag',
) );

if ( ! empty( $term_tags ) ) {
	?>
    <ul class="tags style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
		<?php
		$searched_opt = jobus_search_terms( 'job_tags' );
		foreach ( $term_tags as $term ) {
			$check_status = in_array( $term->slug, $searched_opt );
			?>
            <li>
                <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php echo $check_status !== false
					? esc_attr( 'checked=checked' ) : ''; ?>>
                <label><?php echo esc_html( $term->name ); ?></label>
            </li>
			<?php
		}
		?>
    </ul>
	<?php
}
