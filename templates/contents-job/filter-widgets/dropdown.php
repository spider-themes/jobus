<?php
$widget_name        = $widget['widget_name'] ?? '';
$job_specifications = jobus_get_specs_options();
$job_specifications = $job_specifications[$widget_name] ?? '';
?>

<select class="nice-select bg-white" name="<?php echo esc_attr( $widget_name ); ?>[]">
	<?php
	if ( isset( $job_specifications ) && is_array( $job_specifications ) ) {
		foreach ( $job_specifications as $key => $value ) {

			$meta_value = $value['meta_values'] ?? '';
			$modifiedSelect = preg_replace( '/[,\s]+/', '@space@', $meta_value );
			$modifiedVal = strtolower( $modifiedSelect );
			$meta_value_count = jobus_count_meta_key_usage( 'jobus_job', 'jobus_meta_options', $modifiedVal );

			if ( $meta_value_count > 0) {
				$searched_val = jobus_search_terms( $widget_name );
				$selected_val = $searched_val[0] ?? $modifiedVal;
				$selected_val = $modifiedVal == $selected_val ? ' selected' : '';
				?>
				<option value="<?php echo esc_attr( $modifiedVal ); ?>" <?php echo esc_attr( $selected_val ); ?>>
					<?php echo esc_html( $meta_value ); ?>
				</option>
				<?php
			}
		}
	}
	?>
</select>