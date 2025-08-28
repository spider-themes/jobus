<?php
/**
 * Reusable taxonomy field partial for Jobus dashboard forms.
 * Usage: include with variables set before including.
 * $taxonomy_name, $input_name, $list_id, $label_text, $data_attr, $object_id
 */
if ( ! isset( $taxonomy_name, $input_name, $list_id, $label_text, $data_attr, $object_id ) ) {
	return;
}

$current_terms = ! empty( $object_id ) ? wp_get_object_terms( $object_id, $taxonomy_name ) : [];

// To store taxonomy IDs
$term_ids      = ! empty( $current_terms ) ? implode( ',', wp_list_pluck( $current_terms, 'term_id' ) ) : '';

echo '<pre>';
print_r($current_terms);
echo '</pre>';
?>
<div class="dash-input-wrapper mb-40 mt-20">
    <label for="<?php echo esc_attr( $list_id ); ?>"><?php echo esc_html( $label_text ); ?></label>
    <div class="skills-wrapper">
        <ul id="<?php echo esc_attr( $list_id ); ?>" class="style-none d-flex flex-wrap align-items-center">
            <?php
            if ( ! empty( $current_terms ) ) {
                foreach ( $current_terms as $term ) {
                    ?>
                    <li class="is_tag" data-<?php echo esc_attr( $data_attr ); ?>="<?php echo esc_attr( $term->term_id ); ?>">
                        <button type="button"><?php echo esc_html( $term->name ); ?> <i class="bi bi-x"></i></button>
                    </li>
                    <?php
                }
            }
            ?>
            <li class="more_tag">
                <button type="button"><?php esc_html_e( '+', 'jobus' ); ?></button>
            </li>
        </ul>
        <input type="hidden" name="<?php echo esc_attr( $input_name ); ?>" id="<?php echo esc_attr( $input_name ); ?>_input" value="<?php echo esc_attr( $term_ids ); ?>">
    </div>
</div>
