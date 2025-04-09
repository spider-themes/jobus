<?php
$widget_name        = $widget['widget_name'] ?? '';
$job_specifications = jobus_get_specs_options();
$job_specifications = $job_specifications[$widget_name] ?? '';
?>

<ul class="style-none filter-input">
	<?php
	if ( isset( $job_specifications ) && is_array( $job_specifications ) ) {
		foreach ( $job_specifications as $key => $value ) {

			$meta_key = $meta['meta_key'] ?? '';
			$meta_value = $value['meta_values'] ?? '';

			$modifiedValues = preg_replace( '/[,\s]+/', '@space@', $meta_value );
			$opt_val = strtolower( $modifiedValues );

			// Get the count for the current meta value
			$meta_value_count = jobus_count_meta_key_usage( 'jobus_job', 'jobus_meta_options', $opt_val );
			if ( $meta_value_count > 0) {
				$searched_opt = jobus_search_terms( $widget_name );
				$check_status = array_search( $opt_val, $searched_opt );
				?>
				<li>
					<input type="checkbox" <?php echo $check_status !== false ? esc_attr( 'checked=checked' ) : ''; ?>
					       name="<?php echo esc_attr( $widget_name ); ?>[]"
					       value="<?php echo esc_attr( $opt_val ); ?>">
					<label>
						<?php echo esc_html( $meta_value ); ?>
						<span><?php echo esc_html( $meta_value_count ); ?></span>
					</label>
				</li>
				<?php
			}
		}
	}
	?>
</ul>