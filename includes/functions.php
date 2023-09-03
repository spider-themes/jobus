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
if ( !function_exists( 'jobly_pagination') ) {
    function jobly_pagination ($max_num_pages, $pagination_class = '') {

        $current_page = max(1, get_query_var('paged'));
        $pagination = '<ul class="' . $pagination_class . '">';

        if ($current_page > 1) {
            $pagination .= '<li><a href="' . esc_url(get_pagenum_link($current_page - 1)) . '">' . __('Previous Page', 'jobly') . '</a></li>';
        }

        for ( $i = 1; $i <= $max_num_pages; $i++ ) {
            $active_class = ($current_page == $i) ? 'active' : '';
            $pagination .= '<li class="' . $active_class . '"><a href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a></li>';
        }

        if ($current_page < $max_num_pages) {
            $pagination .= '<li class="ms-2"><a href="' . esc_url(get_pagenum_link($max_num_pages)) . '" class="d-flex align-items-center">' . esc_html__('Next Page', 'jobly') . '</a></li>';
        }

        $pagination .= '</ul>';

        echo $pagination;

    }
}


/**
 * @param $term
 * @get the first taxonomy
 * @return string
 */
if ( ! function_exists( 'jobly_get_the_first_taxonomoy' ) ) {
    function jobly_get_the_first_taxonomoy ( $term = 'job_cat' ) {

        $terms = get_the_terms( get_the_ID(), $term );
        $term = is_array( $terms ) ? $terms[0]->name : '';

        return esc_html($term);

    }
}


/**
 * @param $term
 * @get the first taxonomy url
 * @return string
 */
if ( ! function_exists( 'jobly_get_the_first_taxonomoy_link' ) ) {
    function jobly_get_the_first_taxonomoy_link ( $term = 'job_cat' ) {

        $terms = get_the_terms( get_the_ID(), $term );
        $term = is_array( $terms ) ? get_category_link($terms[0]->term_id) : '';

        return esc_url($term);

    }
}


/**
 * @param $term
 * @get the tag list of job_tag
 * @return string
 */
if ( ! function_exists( 'jobly_get_the_tag_list' ) ) {
    function jobly_get_the_tag_list ($term = 'job_tag') {

        $terms = get_the_terms(get_the_ID(), $term);
        $term = is_array($terms) ? $terms : '';

        $tag_list = '';

        if (!empty($term)) {
            foreach ( $term as $tag ) {
                $tag_list .= '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
            }
        }

        return $tag_list;

    }
}


/**
 * Get categories array
 *
 * @param string $term
 *
 * @return array
 */
function jobly_get_the_categories( $term = 'job_cat' ) {
    $cats      = get_terms( array(
        'taxonomy'   => $term,
        'hide_empty' => true,
    ) );
    $cat_array = [];
    foreach ( $cats as $cat ) {
        $cat_array[ $cat->term_id ] = $cat->name;
    }

    return $cat_array;
}


/**
 * Get title excerpt length
 * @param $settings
 * @param $settings_key
 * @param int $default
 * @return string|void
 */
function jobly_the_title_length($settings, $settings_key, $default = 10) {

    $title_length = !empty($settings[$settings_key]) ? $settings[$settings_key] : $default;
    $title = get_the_title() ? wp_trim_words(get_the_title(), $title_length, '') : the_title();
    echo esc_html($title);
}

/**
 * Get post excerpt length
 * @param $settings
 * @param $settings_key
 * @param int $default
 * @return string
 */
function jobly_get_the_excerpt_length($settings, $settings_key, $default = 10) {

    $excerpt_length = !empty($settings[$settings_key]) ? $settings[$settings_key] : $default;
    $excerpt = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), $excerpt_length, '...') : wp_trim_words(get_the_content(), $excerpt_length, '...');

    return wp_kses_post($excerpt);
}

/** 
 * post featured image support 
*/

if ( function_exists( 'add_post_type_support' ) ) {
    add_post_type_support( 'job', 'thumbnail' );
}


/**
 * @param $settings_key
 * @param $is_echo
 *
 * The button link
 * @return void
 */
function jobly_the_button( $settings_key, $is_echo = true ) {

    if ( $is_echo == true ) {
        echo !empty($settings_key['url']) ? "href='{$settings_key['url']}'" : '';
        echo $settings_key['is_external'] == true ? 'target="_blank"' : '';
        echo $settings_key['nofollow'] == true ? 'rel="nofollow"' : '';

        if ( !empty($settings_key['custom_attributes']) ) {
            $attrs = explode(',', $settings_key['custom_attributes']);

            if(is_array($attrs)){
                foreach($attrs as $data) {
                    $data_attrs = explode('|', $data);
                    echo esc_attr( $data_attrs[0].'='.$data_attrs[1] );
                }
            }
        }
    }

}