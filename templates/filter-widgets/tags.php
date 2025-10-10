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
	'taxonomy' => $taxonomy,
) );

if ( ! empty( $term_tags ) ) {
	$initial_limit = 6; // Number of tags to show initially
	$total_tags = count($term_tags);

	?>
    <ul class="jobus-tags-wrapper tags style-none jbs-d-flex jbs-flex-wrap justify-space-between radio-filter mb-5">
		<?php
		$searched_opt = jobus_search_terms( $taxonomy );
		foreach ( $term_tags as $index => $term ) {
			$selected = (in_array($term->slug, $searched_opt)) ? ' checked' : '';
			$hidden_class = $index >= $initial_limit ? ' hide' : '';
			?>
            <li class="tag-item<?php echo esc_attr($hidden_class); ?>">
                <input type="checkbox" name="<?php echo esc_attr($taxonomy) ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php echo esc_attr($selected) ?>>
                <label><?php echo esc_html( $term->name ); ?></label>
            </li>
			<?php
		}
		?>
    </ul>
    <?php
    if ( $total_tags > $initial_limit ) {
        ?>
        <div class="more-btn"
             data-total="<?php echo esc_attr($total_tags); ?>"
             data-limit="<?php echo esc_attr($initial_limit); ?>">
            <i class="bi bi-plus"></i> Show More
        </div>
        <?php
    }
}
