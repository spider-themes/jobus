<?php
/*
 * Dropdown filter widget for job specifications.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<select class="nice-select" name="<?php echo esc_attr($widget_name) ?>[]">
	<?php
	if ( is_array($candidate_specifications) ) {
		foreach ( $candidate_specifications as $key => $value ) {

			$meta_value = $value[ 'meta_values' ] ?? '';

			$modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
			$modifiedVal = strtolower($modifiedSelect);

			$meta_value_count   = jobus_count_meta_key_usage('jobus_candidate','jobus_meta_candidate_options', $modifiedVal);

			if ( $meta_value_count > 0 ) {
				$searched_val = jobus_search_terms($widget_name);
				$selected_val = $searched_val[ 0 ] ?? $modifiedVal;
				$selected_val = $modifiedVal == $selected_val ? ' selected' : '';
				?>
                <option value="<?php echo esc_attr($modifiedVal) ?>" <?php echo esc_attr($selected_val) ?>>
					<?php echo esc_html($meta_value) ?>
                </option>
				<?php
			}
		}
	}
	?>
</select>