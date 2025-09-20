<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Filters & Upload Security for Jobus
 *
 * Handles image sizes, safe file upload MIME types,
 * login redirects, and hides admin bar for low-privileged roles.
 */

// Add custom image size for candidate profile
add_image_size( 'jobus_280x268', 280, 268, true );

/**
 * Allow only safe file types for candidates and employers
 * - Images: jpg, png, gif, webp
 * - Documents: pdf, doc, docx
 * - Optional SVG with sanitizer
 */
function jobus_dashboard_upload_mimes( $mimes ) {
	if ( current_user_can( 'jobus_candidate' ) || current_user_can( 'jobus_employer' ) ) {
		return [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'webp'         => 'image/webp',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'svg|svgz'     => 'image/svg+xml', // Will be sanitized below
		];
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'jobus_dashboard_upload_mimes' );

/**
 * Sanitize uploaded SVG files before saving
 */
function jobus_sanitize_svg( $file ) {
	if ( isset( $file['type'] ) && $file['type'] === 'image/svg+xml' ) {
		if ( file_exists( $file['tmp_name'] ) && is_readable( $file['tmp_name'] ) ) {
			$dirty_svg = file_get_contents( $file['tmp_name'] );
			// Very simple sanitizer (strip script tags)
			$clean_svg = preg_replace( '/<script.*?<\/script>/is', '', $dirty_svg );
			file_put_contents( $file['tmp_name'], $clean_svg );
		}
	}
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'jobus_sanitize_svg' );

/**
 * Redirect user after login based on their role
 */
function jobus_login_redirect_by_role( $redirect_to, $request, $user ) {
	if ( ! $user || ! is_a( $user, 'WP_User' ) ) {
		return $redirect_to;
	}

	$user_role = reset( $user->roles );

	if ( function_exists( 'jobus_opt' ) && jobus_opt( 'enable_custom_redirects' ) ) {
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

	return admin_url();
}
add_filter( 'login_redirect', 'jobus_login_redirect_by_role', 10, 3 );

/**
 * Hide admin bar for candidates and employers
 */
function jobus_hide_admin_bar_for_roles(): void {
	if ( current_user_can( 'jobus_candidate' ) || current_user_can( 'jobus_employer' ) ) {
		show_admin_bar( false );
	}
}
add_action( 'init', 'jobus_hide_admin_bar_for_roles' );