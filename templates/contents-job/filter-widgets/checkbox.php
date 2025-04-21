<?php
/**
 * This template is used to display a checkbox list of job specifications for filtering job listings.
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/filter-widgets/checkbox.php.
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

<ul class="style-none filter-input">
	<?php
	if ( isset( $job_specifications ) && is_array( $job_specifications ) ) {
		foreach ( $job_specifications as $key => $value ) {

			$meta_key   = $meta['meta_key'] ?? '';
			$meta_value = $value['meta_values'] ?? '';

			$modifiedValues = preg_replace( '/[,\s]+/', '@space@', $meta_value );
			$opt_val        = strtolower( $modifiedValues );

			// Get the count for the current meta value
			$meta_value_count = jobus_count_meta_key_usage( 'jobus_job', 'jobus_meta_options', $opt_val );
			if ( $meta_value_count > 0 ) {
				$searched_opt = jobus_search_terms( $widget_name );
				$check_status = in_array( $opt_val, $searched_opt );
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