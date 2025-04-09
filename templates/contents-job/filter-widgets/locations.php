<?php
$post_type = ! empty( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
$locations = ! empty( $_GET['job_locations'] ) ? array_map( 'sanitize_text_field', $_GET['job_locations'] ) : [];
if ( $post_type == 'jobus_job' &&  $locations ) {
	$is_collapsed_show = 'collapse show';
	$area_expanded = 'true';
	$is_collapsed = '';
}
$term_loc = get_terms( array(
	'taxonomy' => 'jobus_job_location',
	'hide_empty' => false,
) );
if ( ! empty( $term_loc) ) {
	?>
    <select class="nice-select" name="job_locations[]">
        <?php
        $searched_opt = jobus_search_terms( 'job_locations' );
        foreach ( $term_loc as $term ) {
            $selected = (in_array( $term->slug, $searched_opt ) ) ? ' selected' : '';
            ?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
            <?php
        }
        ?>
    </select>
	<?php
}