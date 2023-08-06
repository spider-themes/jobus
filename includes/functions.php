<?php
// A Custom function for get an option
if ( ! function_exists( 'jobly_opt' ) ) {
	function jobly_opt( $option = '', $default = null ) {
		$options = get_option( 'jobly_opt' ); // Attention: Set your unique id of the framework
		return ( isset( $options[$option] ) ) ? $options[$option] : $default;
	}
}


/**
 * Add Topic Generator for job template
 * @return array
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
 * Custom function to get repeater field configurations from settings options.
 *
 * @param string $settings_prefix The prefix used for the settings options.
 * @param string $repeater_id The ID of the repeater field in the settings options.
 * @return array|false Array of field configurations if available, or false if not found.
 */
if ( ! function_exists( 'jobly_get_settings_repeater_fields' ) ) {
	function jobly_get_settings_repeater_fields($settings_prefix, $repeater_id) {
		$settings_options = get_option($settings_prefix);

		if (isset($settings_options[$repeater_id]) && is_array($settings_options[$repeater_id])) {
			return $settings_options[$repeater_id];
		}

		return false;
	}
}


/**
 * Jobly Custom Template Part
 * @return array
 */
if ( !function_exists( 'jobly_get_template_part') ) {

	function jobly_get_template_part( $template ) {

		// Get the slug
		$template_slug = rtrim( $template );
		$template      = $template_slug . '.php';

		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( array( 'jobly/' . $template ) ) ) {
			$file = $theme_file;
		} else {
			//here path to '/single-paper.php'
			$file = JOBLY_PATH . "/templates/" . $template;
		}

		if ( $file ) {
			load_template( $file, false );
		}
	}

}


/**
 * @param $max_num_pages
 * @param $pagination_class
 *
 * @return void
 */
function jobly_pagination ( $max_num_pages, $pagination_class = '' ) {

	$current_page = max(1, get_query_var('paged'));
	$pagination = '<ul class="'.$pagination_class.'">';

		if ($current_page > 1) {
			$pagination .= '<li><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '">' . __('Previous Page', 'jobly') . '</a></li>';
		}

		for ($i = 1; $i <= $max_num_pages; $i++) {
			$active_class = ($current_page == $i) ? 'active' : '';
			$pagination .= '<li class="' . $active_class . '"><a href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
		}

		if ($current_page < $max_num_pages) {
			$pagination .= '<li class="ms-2"><a href="' . esc_url(get_pagenum_link($max_num_pages)) . '" class="d-flex align-items-center">'.esc_html__('Next Page', 'jobly').'</a></li>';
		}

	$pagination .= '</ul>';

	echo $pagination;

}


function jobly_get_post_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	if ( empty( $value ) ) {
		return $default;
	}
	return $value;
}


/**
 * @param $term
 * @get the first taxonomy
 * @return string
 */
function jobly_get_the_first_taxonomoy ( $term = 'job_cat' ) {

	$terms = get_the_terms( get_the_ID(), $term );
	$term = is_array( $terms ) ? $terms[0]->name : '';

	return esc_html($term);

}

/**
 * @param $term
 * @get the first taxonomy url
 * @return string
 */
function jobly_get_the_first_taxonomoy_link ( $term = 'job_cat' ) {

	$terms = get_the_terms( get_the_ID(), $term );
	$term = is_array( $terms ) ? get_category_link($terms[0]->term_id) : '';

	return esc_url($term);

}



function jobly_job_specification_meta( $post_id, $key, $default = '' ) {

    $meta = get_post_meta(get_the_ID(), 'jobly_meta', true);
    $job_types = !empty($meta['job-type']) ? $meta['job-type'] : [];


}