<?php
/**
 * Search form filter widget.
 *
 * This template provides a keyword search input field that allows users
 * to search by entering keywords.
 *
 * @package Jobus
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<?php
$radius_location = isset( $_GET['radius_location'] ) ? sanitize_text_field( $_GET['radius_location'] ) : '';
$radius_distance = isset( $_GET['radius_distance'] ) ? absint( $_GET['radius_distance'] ) : '';
$radius_lat      = isset( $_GET['radius_lat'] ) ? sanitize_text_field( $_GET['radius_lat'] ) : '';
$radius_lng      = isset( $_GET['radius_lng'] ) ? sanitize_text_field( $_GET['radius_lng'] ) : '';

$jobus_opt      = get_option( 'jobus_opt', [] );
$radius_unit    = $jobus_opt['radius_unit'] ?? 'mi';
$default_radius = $jobus_opt['default_radius'] ?? 50;
$max_radius     = $jobus_opt['max_radius'] ?? 250;

$unit_label = $radius_unit === 'km' ? esc_html__( 'km', 'jobus' ) : esc_html__( 'miles', 'jobus' );
?>
<div class="jbs-search-form-widget">
    <label for="jbs-searchInput" class="jbs-search-widget-label"><?php esc_html_e( 'Keyword', 'jobus' ); ?></label>
    <div class="jbs-input-box jbs-position-relative jbs-mb-20">
        <input type="text"
               name="s"
               id="jbs-searchInput"
               value="<?php echo esc_attr( get_search_query() ); ?>"
               placeholder="<?php esc_attr_e( 'Job title, keywords...', 'jobus' ); ?>"
               aria-label="<?php esc_attr_e( 'Search', 'jobus' ); ?>"
               class="jbs-w-100 jbs-rounded">
        <button type="button" class="jbs-border-0 jbs-search-submit-btn">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <?php if ( ! empty( $jobus_opt['enable_radius_search'] ) || ! isset( $jobus_opt['enable_radius_search'] ) ) : ?>
    <!-- Geolocation Radius Search -->
    <div class="jbs-radius-search-wrapper jbs-p-20 jbs-rounded"
         data-text-my-loc="<?php esc_attr_e( 'My Current Location', 'jobus' ); ?>"
         data-text-err-loc="<?php esc_attr_e( 'Unable to retrieve your location.', 'jobus' ); ?>"
         data-text-err-sup="<?php esc_attr_e( 'Geolocation is not supported by your browser.', 'jobus' ); ?>"
         data-text-exact="<?php esc_attr_e( 'Exact', 'jobus' ); ?>"
         data-default-radius="<?php echo esc_attr( $default_radius ); ?>">
        <label for="radius_location" class="jbs-search-widget-label"><?php esc_html_e( 'Location Radius', 'jobus' ); ?></label>
        <div class="jbs-input-box jbs-position-relative">
            <input type="text"
                   name="radius_location"
                   id="radius_location"
                   value="<?php echo esc_attr( $radius_location ); ?>"
                   placeholder="<?php esc_attr_e( 'City, State, or Country', 'jobus' ); ?>"
                   class="jbs-w-100 jbs-rounded jbs-radius-location-input">
            <i class="bi bi-geo-alt jbs-position-absolute jbs-location-icon"></i>
            
            <input type="hidden" name="radius_lat" id="radius_lat" value="<?php echo esc_attr( $radius_lat ); ?>">
            <input type="hidden" name="radius_lng" id="radius_lng" value="<?php echo esc_attr( $radius_lng ); ?>">
            
            <button type="button" id="jbs_get_my_location" class="jbs-border-0 jbs-position-absolute jbs-p-0" title="<?php esc_attr_e( 'Use My Location', 'jobus' ); ?>">
                <i class="bi bi-crosshair"></i>
            </button>
        </div>
        <div class="jbs-input-box jbs-mt-20">
            <div class="jbs-d-flex jbs-justify-content-between jbs-mb-10">
                <label for="radius_distance" class="jbs-search-widget-label jbs-mb-0"><?php esc_html_e( 'Max Distance', 'jobus' ); ?></label>
                <div class="jbs-radius-value-display jbs-fw-500">
                    <span id="radius_val_text"><?php echo esc_html( empty($radius_distance) ? esc_html__( 'Exact', 'jobus' ) : $radius_distance ); ?></span>
                    <span id="radius_val_unit" style="display: <?php echo empty($radius_distance) ? 'none' : 'inline'; ?>"><?php echo esc_html( $unit_label ); ?></span>
                </div>
            </div>
            <div class="jbs-radius-distance-slider jbs-mt-10 jbs-mb-5">
                <?php 
                $max_dist = absint($max_radius);
                $current_dist = empty($radius_distance) ? 0 : absint($radius_distance);
                $percentage = ($current_dist / $max_dist) * 100;
                ?>
                <input type="range" 
                       name="radius_distance" 
                       id="radius_distance" 
                       class="jbs-w-100" 
                       min="0" 
                       max="<?php echo esc_attr($max_dist); ?>" 
                       step="5" 
                       data-percentage="<?php echo esc_attr($percentage); ?>"
                       value="<?php echo esc_attr( $current_dist ); ?>">
            </div>
            <div class="jbs-clearfix"></div>
        </div>
    </div>
    <?php endif; ?>
</div>
