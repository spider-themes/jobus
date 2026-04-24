<?php
/*
 * Range jbs-slider filter widget for job specifications.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Fetch dynamic range values extracted directly from actual published posts of this particular post_type.
// Variables $post_type, $meta_opt_key, and $widgets_option_key are injected by the generic-sidebar-filters.php template loader.
$dynamic_job_values = jobus_all_range_field_value( $post_type ?? 'jobus_job', $meta_opt_key ?? 'jobus_meta_options', $widgets_option_key ?? 'job_sidebar_widgets' );

// Initialize an array to store all numeric values
$all_values = [];

// Method A: Dynamic scan from actual database (highly accurate, scales automatically)
if ( ! empty( $dynamic_job_values[ $widget_name ] ) ) {
	foreach ( $dynamic_job_values[ $widget_name ] as $raw_meta_string ) {
		// Bulletproof check to handle cases where database stores meta as an array
		$subject = is_array( $raw_meta_string ) ? implode( '-', $raw_meta_string ) : (string) $raw_meta_string;

		preg_match_all( '/(\d+)(k)?/i', $subject, $matches );
		foreach ( $matches[1] as $key => $value ) {
			// If 'k' is present, multiply the number by 1000
			$value = isset( $matches[2][ $key ] ) && strtolower( $matches[2][ $key ] ) == 'k' ? $value * 1000 : $value;
			$all_values[] = $value;
		}
	}
}

// Method B: Fallback to static admin plugin settings if no jobs have this field populated yet
if ( empty( $all_values ) && ! empty( $specifications_data ) ) {
	foreach ( $specifications_data as $item ) {
		// Bulletproof check for admin config arrays
		$subject = is_array( $item['meta_values'] ) ? implode( '-', $item['meta_values'] ) : (string) $item['meta_values'];

		preg_match_all( '/(\d+)(k)?/i', $subject, $matches );
		foreach ( $matches[1] as $key => $value ) {
			$value = isset( $matches[2][ $key ] ) && strtolower( $matches[2][ $key ] ) == 'k' ? $value * 1000 : $value;
			$all_values[] = $value;
		}
	}
}

// Get the minimum and maximum values
if ( ! empty ( $all_values ) ) :
	$min_values = min( $all_values );
	$max_values = max( $all_values );

	$min_salary = jobus_search_terms( $widget_name )[0] ?? $min_values;
	$max_salary = jobus_search_terms( $widget_name )[1] ?? $max_values;
	?>

    <div class="jbs-salary-slider"
         data_widget="<?php echo esc_attr( $widget_name ); ?>[]">
        <div class="jbs-price-input jbs-d-flex jbs-align-items-center">
            <div class="field jbs-d-flex jbs-align-items-center">
                <input type="number"
                       name="<?php echo esc_attr( $widget_name ); ?>[]"
                       class="jbs-input-min"
                       value="<?php echo esc_attr( $min_salary ); ?>"
                       readonly>
            </div>
            <div class="jbs-pe-1 jbs-ps-1">-</div>
            <div class="field jbs-d-flex jbs-align-items-center">
                <input type="number"
                       name="<?php echo esc_attr( $widget_name ); ?>[]"
                       class="jbs-input-max"
                       value="<?php echo esc_attr( $max_salary ); ?>"
                       readonly>
            </div>
			<?php if ( ! empty( $range_suffix ) ) : ?>
                <div class="currency jbs-ps-1"><?php echo esc_html( $range_suffix ); ?></div>
			<?php endif; ?>
        </div>
        <div class="jbs-slider">
            <div class="jbs-progress"></div>
        </div>
        <div class="jbs-range-input">
            <input type="range" class="jbs-range-min"
                   min="<?php echo esc_attr( $min_values ); ?>"
                   max="<?php echo esc_attr( $max_values ); ?>"
                   value="<?php echo esc_attr( $min_salary ); ?>" step="1">
            <input type="range" class="jbs-range-max"
                   min="<?php echo esc_attr( $min_values ); ?>"
                   max="<?php echo esc_attr( $max_values ); ?>"
                   value="<?php echo esc_attr( $max_salary ); ?>" step="1">
        </div>
    </div>
<?php
endif;