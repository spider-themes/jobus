<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Track job post views
jobus_count_post_views(get_the_ID(), 'job');

wp_enqueue_script('jobus-job-application-form');
get_header();

$is_block_theme = function_exists('wp_is_block_theme') && wp_is_block_theme();
if ($is_block_theme) {
    echo '<div class="wp-site-blocks"><main class="wp-block-group" id="wp--skip-link--target">';
}

while ( have_posts() ) :
	the_post();

	$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

$job_single_layout_page = $meta['job_details_layout'] ?? ''; // Individual page specific layout
$job_single_layout_opt  = jobus_opt('job_details_layout', '1'); // Default layout for the entire website
$job_single_layout      = ! empty($job_single_layout_page) ? $job_single_layout_page : $job_single_layout_opt;

// Guest application settings
$allow_guest_application = jobus_opt('allow_guest_application', false);
$signin_url              = jobus_opt('signin_btn_url', wp_login_url(get_permalink()));
$signin_label            = jobus_opt('signin_btn_label', __('Sign In', 'jobus'));
$register_url            = jobus_opt('login_signup_btn_url', wp_registration_url());
$register_label          = jobus_opt('login_signup_btn_label', __('Register', 'jobus'));

//================ Select Layout =======================//
	if (jobus_unlock_themes('jobi', 'jobi-child')) {
		include 'single-job/job-single-' . $job_single_layout . '.php';
	} else {
		include 'single-job/job-single-1.php';
	}
endwhile;

if ($is_block_theme) {
    echo '</main></div>';
}

get_footer();

// if user logged in and guest application is enabled, include the modal form
if (is_user_logged_in() || jobus_opt('allow_guest_application', '1')) {
	include 'single-job/job-application-form-modal.php';
}
