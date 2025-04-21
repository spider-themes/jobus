<?php
/**
 * Dropdown filter widget for candidate specifications.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/filter-widgets/dropdown.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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