<?php
/*
 * Range slider filter widget for job specifications.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$salary_value_list = $specifications_data;

// Initialize an array to store all numeric values
$all_values     = [];

// Extract numeric values from meta_values
foreach ( $salary_value_list as $item ) {

	// Extract numbers and check for 'k'
	preg_match_all( '/(\d+)(k)?/i', $item['meta_values'], $matches );
	foreach ( $matches[1] as $key => $value ) {
		// If 'k' is present, multiply the number by 1000
		$value = isset( $matches[2][ $key ] ) && strtolower( $matches[2][ $key ] ) == 'k' ? $value * 1000 : $value;

		$all_values[] = $value;
	}
}

// Get the minimum and maximum values
if ( ! empty ( $all_values ) ) :
	$min_values = min( $all_values );
	$max_values = max( $all_values );

	$min_salary = jobus_search_terms( $widget_name )[0] ?? $min_values;
	$max_salary = jobus_search_terms( $widget_name )[1] ?? $max_values;
	?>

    <div class="salary-slider"
         data_widget="<?php echo esc_attr( $widget_name ); ?>[]">
        <div class="price-input jbs-d-flex jbs-align-items-center jbs-pt-5">
            <div class="field jbs-d-flex jbs-align-items-center">
                <input type="number"
                       name="<?php echo esc_attr( $widget_name ); ?>[]"
                       class="input-min"
                       value="<?php echo esc_attr( $min_salary ); ?>"
                       readonly>
            </div>
            <div class="jbs-pe-1 jbs-ps-1">-</div>
            <div class="field jbs-d-flex jbs-align-items-center">
                <input type="number"
                       name="<?php echo esc_attr( $widget_name ); ?>[]"
                       class="input-max"
                       value="<?php echo esc_attr( $max_salary ); ?>"
                       readonly>
            </div>
			<?php if ( ! empty( $range_suffix ) ) : ?>
                <div class="currency jbs-ps-1"><?php echo esc_html( $range_suffix ); ?></div>
			<?php endif; ?>
        </div>
        <div class="slider">
            <div class="progress"></div>
        </div>
        <div class="range-input mb-10">
            <input type="range" class="range-min"
                   min="<?php echo esc_attr( $min_values ); ?>"
                   max="<?php echo esc_attr( $max_values ); ?>"
                   value="<?php echo esc_attr( $min_salary ); ?>" step="1">
            <input type="range" class="range-max"
                   min="<?php echo esc_attr( $min_values ); ?>"
                   max="<?php echo esc_attr( $max_values ); ?>"
                   value="<?php echo esc_attr( $max_salary ); ?>" step="1">
        </div>
    </div>
<?php
endif;