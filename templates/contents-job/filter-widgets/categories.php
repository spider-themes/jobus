<?php
// Initialize variables with default values
$is_collapsed_show = 'collapse';
$area_expanded     = 'false';
$is_collapsed      = ' collapsed';

$post_type = ! empty( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
$job_cats  = ! empty( $_GET['job_cats'] ) ? array_map( 'sanitize_text_field', $_GET['job_cats'] ) : [];

if ( $post_type == 'jobus_job' && $job_cats ) {
	$is_collapsed_show = 'collapse show';
	$area_expanded     = 'true';
	$is_collapsed      = '';
}
$term_cats = get_terms( array(
	'taxonomy' => 'jobus_job_cat',
) );

if ( ! empty( $term_cats ) ) {
	?>
    <div class="filter-block bottom-line pb-25">
        <a class="filter-title fw-500 text-dark<?php echo esc_attr( $is_collapsed ); ?>" data-bs-toggle="collapse" href="#collapseCategory" role="button"
           aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
			<?php esc_html_e( 'Categories', 'jobus' ); ?>
        </a>
        <div class="<?php echo esc_attr( $is_collapsed_show ); ?>" id="collapseCategory">
            <div class="main-body">
                <select class="nice-select" name="job_cats[]">
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
            </div>
        </div>
    </div>
	<?php
}
