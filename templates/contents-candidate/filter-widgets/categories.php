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
	'taxonomy' => 'jobus_job_cat',
) );

if ( ! empty( $term_cats ) ) {
	?>
    <select class="nice-select" name="job_cats[]">
        <option value="" disabled selected><?php esc_html_e( 'Select Category', 'jobus' ); ?></option>
		<?php
		$searched_opt = jobus_search_terms( 'job_cats' );
		foreach ( $term_cats as $term ) {
			$selected = ( in_array( $term->slug, $searched_opt ) ) ? ' selected' : '';
			?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php echo esc_attr( $selected ); ?>>
				<?php echo esc_html( $term->name ); ?>
            </option>
			<?php
		}
		?>
    </select>
	<?php
}
