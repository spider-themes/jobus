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





function allow_attachment_actions( $user_caps, $req_cap, $args ) {
	// if no post is connected with capabilities check just return original array
	if ( empty($args[2]) )
		return $user_caps;

	$post = get_post( $args[2] );

	if ( 'attachment' == $post->post_type ) {
		$user_caps[$req_cap[0]] = true;
		return $user_caps;
	}

	// for any other post type return original capabilities
	return $user_caps;
}
add_filter( 'user_has_cap', 'allow_attachment_actions', 10, 3 );