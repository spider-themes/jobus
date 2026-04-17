<?php
/**
 * This template is used to display a dropdown list of job specifications for filtering job listings.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/filter-widgets/dropdown.php.
 *
 * However, on occasion Jobus will need to update template files and you
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

<select class="jbs-nice-select" name="<?php echo esc_attr( $widget_name ); ?>[]">
	<?php $searched_val = jobus_search_terms( $widget_name ); ?>
	<option value="" <?php selected( empty( $searched_val ), true ); ?>>
		<?php 
		/* translators: %s: Widget title */
		printf( esc_html__( 'Select %s', 'jobus' ), esc_html( $widget_title ) ); 
		?>
	</option>
	<?php
	if ( isset( $specifications_data ) && is_array( $specifications_data ) ) {
		foreach ( $specifications_data as $key => $value ) {

			$meta_value       = $value['meta_values'] ?? '';
			$modifiedSelect   = preg_replace( '/[,\s]+/', '@space@', $meta_value );
			$modifiedVal      = strtolower( $modifiedSelect );
            // Format correctly for browser URLs seamlessly decoded from internal logic
            $beautiful_val    = str_replace( '@space@', ' ', $modifiedVal );
			$meta_value_count = jobus_count_meta_key_usage( $post_type, $meta_opt_parent_key, $modifiedVal );

			if ( $meta_value_count > 0 ) {
				$is_selected = in_array( $modifiedVal, $searched_val, true ) || in_array( $beautiful_val, $searched_val, true );
				?>
                <option value="<?php echo esc_attr( $beautiful_val ); ?>" <?php selected( $is_selected, true ); ?>>
					<?php echo esc_html( $meta_value ); ?>
                </option>
				<?php
			}
		}
	}
	?>
</select>