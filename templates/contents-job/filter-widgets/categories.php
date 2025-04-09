<?php

$term_cats = get_terms( array(
	'taxonomy' => 'jobus_job_cat',
) );

if ( ! empty( $term_cats ) ) {
	?>
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
	<?php
}
