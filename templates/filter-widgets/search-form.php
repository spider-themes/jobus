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

$unit_label = $radius_unit === 'km' ? esc_html__( 'km', 'jobus' ) : esc_html__( 'miles', 'jobus' );
?>
<div class="search-form-widget">
    <div class="input-box jbs-position-relative jbs-mb-20">
        <label for="searchInput" class="jbs-fw-500 jbs-mb-2 jbs-d-block jbs-fs-14"><?php esc_html_e( 'Keyword', 'jobus' ); ?></label>
        <input type="text"
               name="s"
               id="searchInput"
               value="<?php echo esc_attr( get_search_query() ); ?>"
               placeholder="<?php esc_attr_e( 'Job title, keywords...', 'jobus' ); ?>"
               aria-label="<?php esc_attr_e( 'Search', 'jobus' ); ?>"
               class="jbs-w-100 jbs-rounded" style="height: 45px; padding-left: 15px; border: 1px solid #e5e5e5;">
        <button type="button" class="jbs-border-0 jbs-position-absolute" style="right: 15px; top: 38px; background: transparent; color: #a0a0a0;">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <!-- Geolocation Radius Search -->
    <div class="radius-search-wrapper jbs-p-20 jbs-rounded" style="background: rgba(0,0,0,0.02); border: 1px dashed #e2e2e2; padding-top: 16px;">
        <label for="radius_location" class="jbs-fw-500 jbs-mb-10 jbs-d-block jbs-fs-14"><?php esc_html_e( 'Location Radius', 'jobus' ); ?></label>
        <div class="input-box jbs-mb-15 jbs-position-relative">
            <input type="text"
                   name="radius_location"
                   id="radius_location"
                   value="<?php echo esc_attr( $radius_location ); ?>"
                   placeholder="<?php esc_attr_e( 'City or Zip Code', 'jobus' ); ?>"
                   class="jbs-w-100 jbs-rounded" style="height: 40px; padding-left: 35px; padding-right: 35px; border: 1px solid #e5e5e5;">
            <i class="bi bi-geo-alt jbs-position-absolute" style="left: 12px; top: 10px; color: #a0a0a0;"></i>
            
            <input type="hidden" name="radius_lat" id="radius_lat" value="<?php echo esc_attr( $radius_lat ); ?>">
            <input type="hidden" name="radius_lng" id="radius_lng" value="<?php echo esc_attr( $radius_lng ); ?>">
            
            <button type="button" id="jbs_get_my_location" class="jbs-border-0 jbs-position-absolute jbs-p-0" style="right: 12px; top: 10px; background: transparent; color: #28a745;" title="<?php esc_attr_e( 'Use My Location', 'jobus' ); ?>">
                <i class="bi bi-crosshair"></i>
            </button>
        </div>
        <div class="input-box">
            <select name="radius_distance" id="radius_distance" class="jbs-nice-select jbs-w-100">
                <option value=""><?php esc_html_e( 'Exact Location Only', 'jobus' ); ?></option>
                <?php
                $distances = [ 5, 10, 25, 50, 100, 250 ];
                foreach ( $distances as $dist ) {
                    $selected = selected( $radius_distance, $dist, false );
                    echo '<option value="' . esc_attr( $dist ) . '" ' . $selected . '>' . sprintf( esc_html__( 'Within %d %s', 'jobus' ), $dist, $unit_label ) . '</option>';
                }
                ?>
            </select>
            <div class="jbs-clearfix"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById('jbs_get_my_location');
    if(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = btn.querySelector('i');
            icon.className = 'bi bi-arrow-repeat jbs-spin'; // Add spinner interaction
            
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('radius_lat').value = position.coords.latitude;
                    document.getElementById('radius_lng').value = position.coords.longitude;
                    document.getElementById('radius_location').value = "<?php esc_html_e( 'My Current Location', 'jobus' ); ?>";
                    
                    // If no distance select is previously chosen, set it to 50 magically.
                    const distSelect = document.getElementById('radius_distance');
                    if(distSelect && distSelect.value === "") {
                        distSelect.value = "<?php echo esc_js( $default_radius ); ?>";
                        if(window.jQuery) {
                            jQuery(distSelect).niceSelect('update');
                        }
                    }
                    
                    icon.className = 'bi bi-check-circle';
                    icon.style.color = '#28a745';
                    
                }, function(error) {
                    alert("<?php esc_html_e( 'Unable to retrieve your location.', 'jobus' ); ?>");
                    icon.className = 'bi bi-crosshair';
                });
            } else {
                alert("<?php esc_html_e( 'Geolocation is not supported by your browser.', 'jobus' ); ?>");
                icon.className = 'bi bi-crosshair';
            }
        });
        
        // Ensure lat/lng are cleared if user types a new custom location
        document.getElementById('radius_location').addEventListener('input', function() {
            if (this.value !== "<?php esc_html_e( 'My Current Location', 'jobus' ); ?>") {
                document.getElementById('radius_lat').value = '';
                document.getElementById('radius_lng').value = '';
            }
        });
    }
});
</script>
