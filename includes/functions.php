<?php
/**
 * Translation ready
*/
add_action('wp_head', function(){
    esc_html__('Translation ready', 'jobly');
});


// A Custom function for get an option
if ( ! function_exists( 'jobly_opt' ) ) {
	function jobly_opt( $option = '', $default = null ) {
		$options = get_option( 'jobly_opt' ); // Attention: Set your unique id of the framework
		return ( isset( $options[$option] ) ) ? $options[$option] : $default;
	}
}


/**
 * Add Topic Generator for job template
 */
if ( ! function_exists( 'jobly_job_topics_text' ) ) {
	function jobly_job_topics_text() {

		$options = jobly_opt( 'job_topics' );

		$job_options_text = array();

		if ( ! empty( $options ) ) {
			foreach ( $options as $value ) {
				$job_options_text[$value['text']] = $value['text'];
			}
		}

		return $job_options_text;

	}
}




/**
 * Custom function to retrieve repeater data from the settings options.
 *
 * @return array|false Array of repeater data if available, or false if not found.
 */
function jobly_get_repeater_data() {
	// Set a unique slug-like ID for settings options
	$settings_prefix = 'jobly_opt';

	// Retrieve the settings options using WordPress get_option()
	$settings_options = get_option($settings_prefix);

	// Check if the repeater data is available in the settings options
	if (isset($settings_options['job_specifications']) && is_array($settings_options['job_specifications'])) {
		return $settings_options['job_specifications'];
	}

	return false; // Return false if repeater data is not found
}




/**
 * Custom function to get repeater field configurations from settings options.
 *
 * @param string $settings_prefix The prefix used for the settings options.
 * @param string $repeater_id The ID of the repeater field in the settings options.
 * @return array|false Array of field configurations if available, or false if not found.
 */
function jobly_get_settings_repeater_fields($settings_prefix, $repeater_id) {
	$settings_options = get_option($settings_prefix);

	if (isset($settings_options[$repeater_id]) && is_array($settings_options[$repeater_id])) {
		return $settings_options[$repeater_id];
	}

	return false;
}