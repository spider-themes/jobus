<?php
/*
 * Checkbox filter widget for job specifications.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<ul class="style-none filter-input">
	<?php
	if ( !empty($candidate_specifications) ) {
		foreach ( $candidate_specifications as $key => $value ) {

			$meta_value = $value[ 'meta_values' ] ?? '';
			$modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
			$opt_val = strtolower($modifiedValues);

			// Get the count for the current meta-value
			$meta_value_count   = jobus_count_meta_key_usage('jobus_candidate', 'jobus_meta_candidate_options', $opt_val );

			if ( $meta_value_count > 0 ) {
				$searched_opt   = jobus_search_terms($widget_name);
				$check_status   = in_array( $opt_val, $searched_opt );
				$check_status   = $check_status !== false ? ' checked' : '';
				?>
                <li>
                    <input type="checkbox" name="<?php echo esc_attr($widget_name) ?>[]" value="<?php echo esc_attr($opt_val) ?>" <?php echo esc_attr($check_status) ?>>
                    <label>
						<?php echo esc_html($meta_value); ?>
                    </label>
                </li>
				<?php
			}
		}
	}
	?>
</ul>