<?php
/**
 * Category - This template is used to display a dropdown list of company categories for filtering company listings.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-company/filter-widgets/categories.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
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

$term_cats = get_terms( array(
	'taxonomy' => 'jobus_company_cat',
) );

if ( ! empty( $term_cats ) ) {
	?>
	<ul class="style-none filter-input">
		<?php
		$searched_opt = jobus_search_terms( 'company_cats' );
		foreach ( $term_cats as $key => $term ) {
			$list_class   = $key > 3 ? ' class=hide' : '';
			$check_status = in_array( $term->slug, $searched_opt );
			$check_status = $check_status !== false ? ' checked' : '';
			?>
			<li<?php echo esc_attr( $list_class ) ?>>
				<input type="checkbox" name="company_cats[]" value="<?php echo esc_attr( $term->slug ) ?>" <?php echo esc_attr( $check_status ) ?>>
				<label>
					<?php echo esc_html( $term->name ) ?>
					<span><?php echo esc_html( $term->count ) ?></span>
				</label>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
	if ( count( $term_cats ) > 3 ) {
		?>
		<div class="more-btn">
			<i class="bi bi-plus"></i>
			<?php esc_html_e( 'Show More', 'jobus' ); ?>
		</div>
		<?php
	}
}