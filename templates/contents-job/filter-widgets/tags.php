<?php
$post_type = ! empty( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
$job_tags = ! empty( $_GET['job_tags'] ) ? array_map( 'sanitize_text_field', $_GET['job_tags'] ) : [];
if ( $post_type == 'jobus_job' && $job_tags ) {
	$is_collapsed_show = 'collapse show';
	$area_expanded = 'true';
	$is_collapsed = '';
}
?>

<ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
    <?php
    $term_tags = get_terms( array(
        'taxonomy' => 'jobus_job_tag',
    ) );
    if ( ! empty( $term_tags ) ) {
        $searched_opt = jobus_search_terms( 'job_tags' );
        foreach ( $term_tags as $term ) {
            $check_status = array_search( $term->slug, $searched_opt );
            ?>
            <li>
                <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php echo $check_status !== false ? esc_attr( 'checked=checked' ) : ''; ?>>
                <label><?php echo esc_html( $term->name ); ?></label>
            </li>
            <?php
        }
    }
    ?>
</ul>