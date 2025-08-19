<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

//Add Image Size
add_image_size('jobus_280x268', 280, 268, true ); //Candidate Profile 01/02


// Ensure candidates can edit and delete attachments
function jobus_ensure_candidate_attachment_capabilities() {
    if (current_user_can('jobus_candidate')) {
        // Dynamically add capabilities to the current user
        $user = wp_get_current_user();
        $user->allcaps['delete_posts'] = true;
        $user->allcaps['delete_published_posts'] = true;
        $user->allcaps['edit_post'] = true;
        $user->allcaps['edit_posts'] = true;
    }
}
add_action('admin_init', 'jobus_ensure_candidate_attachment_capabilities');


// Allow all common file types for candidates
function jobus_candidate_upload_mimes($mimes) {
    if (current_user_can('jobus_candidate')) {
        return array(
            // Images
            'jpg|jpeg|jpe' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg|svgz' => 'image/svg+xml',

            // Documents
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

            // Archives
            'zip' => 'application/zip'
        );
    }
    return $mimes;
}
add_filter('upload_mimes', 'jobus_candidate_upload_mimes');



/**
 * Redirect user after login based on their role
 */
function jobus_login_redirect_by_role( $redirect_to, $request, $user ) {

	// If no user object, fallback
	if ( ! $user || ! is_a( $user, 'WP_User' ) ) {
		return $redirect_to;
	}

	// Get user role
	$user_role = reset( $user->roles );

	// Custom redirect logic
	if ( jobus_opt( 'enable_custom_redirects' ) ) {
		if ( $user_role === 'jobus_candidate' ) {
			$page_id = jobus_opt( 'candidate_redirect_page' );
			if ( $page_id ) {
				return get_permalink( $page_id );
			}
		}
		if ( $user_role === 'jobus_employer' ) {
			$page_id = jobus_opt( 'employer_redirect_page' );
			if ( $page_id ) {
				return get_permalink( $page_id );
			}
		}
	}

	// Default redirect
	return admin_url();
}
add_filter( 'login_redirect', 'jobus_login_redirect_by_role', 10, 3 );


/**
 * Hide admin bar for candidates and employers
 */
function jobus_hide_admin_bar_for_roles() {
	if ( current_user_can( 'jobus_candidate' ) || current_user_can( 'jobus_employer' ) ) {
		show_admin_bar( false );
	}
}
add_action( 'init', 'jobus_hide_admin_bar_for_roles' );