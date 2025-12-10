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
	if ( ! is_user_logged_in() ) {
		return $mimes;
	}
	
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	
	if ( in_array( 'jobus_candidate', $user_roles, true ) || in_array( 'jobus_employer', $user_roles, true ) ) {
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

	// Check for custom redirect settings first
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

	// Default: redirect to role-specific dashboard
	if ( class_exists( '\jobus\includes\Frontend\Dashboard' ) ) {
		if ( $user_role === 'jobus_candidate' ) {
			$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_candidate' );
			if ( ! empty( $dashboard_url ) && $dashboard_url !== home_url( '/' ) ) {
				return $dashboard_url;
			}
		}
		if ( $user_role === 'jobus_employer' ) {
			$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
			if ( ! empty( $dashboard_url ) && $dashboard_url !== home_url( '/' ) ) {
				return $dashboard_url;
			}
		}
	}

	return $redirect_to;
}
add_filter( 'login_redirect', 'jobus_login_redirect_by_role', 10, 3 );

/**
 * Hide admin bar for candidates and employers
 */
function jobus_hide_admin_bar_for_roles( $show ) {
	if ( ! is_user_logged_in() ) {
		return $show;
	}
	
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	
	// Hide admin bar if user has candidate or employer role
	if ( in_array( 'jobus_candidate', $user_roles, true ) || in_array( 'jobus_employer', $user_roles, true ) ) {
		return false;
	}
	
	return $show;
}
add_filter( 'show_admin_bar', 'jobus_hide_admin_bar_for_roles' );

/**
 * Restrict admin panel access for candidates and employers
 */
function jobus_restrict_admin_access(): void {
	if ( ! is_user_logged_in() || wp_doing_ajax() ) {
		return;
	}
	
	$user = wp_get_current_user();
	$user_roles = (array) $user->roles;
	
	// Block admin access for candidate and employer roles
	if ( is_admin() && ( in_array( 'jobus_candidate', $user_roles, true ) || in_array( 'jobus_employer', $user_roles, true ) ) ) {
		// Redirect to home page or dashboard
		if ( class_exists( '\jobus\includes\Frontend\Dashboard' ) ) {
			$role = in_array( 'jobus_candidate', $user_roles, true ) ? 'jobus_candidate' : 'jobus_employer';
			$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( $role );
			if ( ! empty( $dashboard_url ) ) {
				wp_safe_redirect( $dashboard_url );
				exit;
			}
		}
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'admin_init', 'jobus_restrict_admin_access' );

/**
 * Clear object cache when jobus post types are trashed, deleted or status changes
 * This ensures trashed/deleted candidates, jobs, companies don't appear in archives
 */
function jobus_clear_cache_on_post_status_change( $new_status, $old_status, $post ) {
	$jobus_post_types = array( 'jobus_candidate', 'jobus_job', 'jobus_company' );
	
	if ( ! in_array( $post->post_type, $jobus_post_types, true ) ) {
		return;
	}
	
	// Clear object cache for the post
	wp_cache_delete( $post->ID, 'posts' );
	wp_cache_delete( $post->ID, 'post_meta' );
	
	// Clear any related term caches
	clean_post_cache( $post->ID );
}
add_action( 'transition_post_status', 'jobus_clear_cache_on_post_status_change', 10, 3 );

/**
 * Clear cache when post is trashed
 */
function jobus_clear_cache_on_trash( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}
	
	$jobus_post_types = array( 'jobus_candidate', 'jobus_job', 'jobus_company' );
	
	if ( in_array( $post->post_type, $jobus_post_types, true ) ) {
		wp_cache_delete( $post_id, 'posts' );
		wp_cache_delete( $post_id, 'post_meta' );
		clean_post_cache( $post_id );
	}
}
add_action( 'wp_trash_post', 'jobus_clear_cache_on_trash' );
add_action( 'before_delete_post', 'jobus_clear_cache_on_trash' );

/**
 * Delete associated candidate/company post when a user is deleted
 * This prevents orphaned posts from appearing in archives
 */
function jobus_delete_user_posts_on_user_delete( $user_id, $reassign, $user ) {
	// Delete candidate post if user was a candidate
	$candidate_posts = get_posts( [
		'post_type'   => 'jobus_candidate',
		'author'      => $user_id,
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	] );
	
	foreach ( $candidate_posts as $post_id ) {
		wp_delete_post( $post_id, true ); // Force delete (bypass trash)
	}
	
	// Delete company post if user was an employer
	$company_posts = get_posts( [
		'post_type'   => 'jobus_company',
		'author'      => $user_id,
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	] );
	
	foreach ( $company_posts as $post_id ) {
		wp_delete_post( $post_id, true ); // Force delete (bypass trash)
	}
}
add_action( 'delete_user', 'jobus_delete_user_posts_on_user_delete', 10, 3 );

/**
 * Also handle when user is deleted from multisite
 */
function jobus_delete_user_posts_on_wpmu_delete( $user_id ) {
	jobus_delete_user_posts_on_user_delete( $user_id, null, null );
}
add_action( 'wpmu_delete_user', 'jobus_delete_user_posts_on_wpmu_delete' );
add_action( 'remove_user_from_blog', 'jobus_delete_user_posts_on_wpmu_delete' );

/**
 * Delete associated user when a candidate or company post is deleted
 * This ensures user is removed when their profile post is deleted from admin
 * 
 * NOTE: This function handles both trash and permanent delete actions
 */
function jobus_delete_user_on_post_delete( $post_id ) {
	// Prevent running multiple times
	static $processed_posts = array();
	if ( isset( $processed_posts[ $post_id ] ) ) {
		return;
	}
	$processed_posts[ $post_id ] = true;
	
	$post = get_post( $post_id );
	
	if ( ! $post ) {
		return;
	}
	
	// Only process candidate and company post types
	if ( ! in_array( $post->post_type, array( 'jobus_candidate', 'jobus_company' ), true ) ) {
		return;
	}
	
	$user_id = $post->post_author;
	
	// Make sure user exists and is valid
	if ( ! $user_id || $user_id <= 0 ) {
		return;
	}
	
	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		return;
	}
	
	// Only delete users with candidate or employer roles (don't delete admins!)
	$user_roles = $user->roles;
	$allowed_roles = array( 'jobus_candidate', 'jobus_employer' );
	
	$can_delete = false;
	foreach ( $user_roles as $role ) {
		if ( in_array( $role, $allowed_roles, true ) ) {
			$can_delete = true;
			break;
		}
	}
	
	if ( ! $can_delete ) {
		return;
	}
	
	// Prevent infinite loop - temporarily remove the user deletion hook
	remove_action( 'delete_user', 'jobus_delete_user_posts_on_user_delete', 10 );
	
	// Delete the user (requires user management capability check)
	if ( current_user_can( 'delete_users' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( $user_id );
	}
	
	// Re-add the hook
	add_action( 'delete_user', 'jobus_delete_user_posts_on_user_delete', 10, 3 );
}
// Hook into BOTH trash and permanent delete actions
add_action( 'wp_trash_post', 'jobus_delete_user_on_post_delete' );       // When post is trashed
add_action( 'before_delete_post', 'jobus_delete_user_on_post_delete' );  // When post is permanently deleted