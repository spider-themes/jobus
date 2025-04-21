<?php
/**
 * Checkbox filter widget for candidate specifications.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/filter-widgets/checkbox.php.
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