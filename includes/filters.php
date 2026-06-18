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
		/*
		 * SVG is intentionally NOT allowed. SVG is an active document (it can carry
		 * <script>, on* handlers, javascript: URIs, <foreignObject>, etc.) and a job
		 * board has no need for user-uploaded vector images. Allowing it for
		 * unprivileged candidate/employer roles is a stored-XSS vector that fires in
		 * the browser of any admin reviewing the profile. Raster + documents only.
		 */
		return [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'webp'         => 'image/webp',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		];
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'jobus_dashboard_upload_mimes' );

/**
 * Sanitize uploaded SVG files before saving.
 *
 * Defense in depth: Jobus does not allow SVG for its own roles (see
 * jobus_dashboard_upload_mimes()), but a theme or another plugin may enable SVG
 * uploads site-wide. If an SVG does reach the upload pipeline, neutralise the
 * active-content vectors with an allowlist DOM pass instead of a naive regex,
 * which cannot reliably catch event handlers, javascript: URIs or entities.
 *
 * @param array $file Upload data from wp_handle_upload_prefilter.
 * @return array
 */
function jobus_sanitize_svg( $file ) {
	if ( empty( $file['type'] ) || 'image/svg+xml' !== $file['type'] ) {
		return $file;
	}

	if ( empty( $file['tmp_name'] ) || ! is_readable( $file['tmp_name'] ) || ! class_exists( 'DOMDocument' ) ) {
		// Cannot safely inspect it — reject rather than store an unscanned SVG.
		$file['error'] = esc_html__( 'SVG files could not be processed and were rejected for security reasons.', 'jobus' );
		return $file;
	}

	$dirty = file_get_contents( $file['tmp_name'] );
	if ( false === $dirty || '' === trim( (string) $dirty ) ) {
		$file['error'] = esc_html__( 'The uploaded SVG file is empty or unreadable.', 'jobus' );
		return $file;
	}

	$clean = jobus_clean_svg_markup( (string) $dirty );
	if ( null === $clean ) {
		$file['error'] = esc_html__( 'The uploaded SVG file is malformed and was rejected.', 'jobus' );
		return $file;
	}

	file_put_contents( $file['tmp_name'], $clean );
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'jobus_sanitize_svg' );

/**
 * Strip active content from SVG markup using a DOM allowlist.
 *
 * Removes script/foreignObject elements, all on* event-handler attributes, and
 * any href/xlink:href whose scheme is not safe (blocks javascript: and data:).
 * Also disables external entity loading to prevent XXE.
 *
 * @param string $svg Raw SVG markup.
 * @return string|null Sanitised markup, or null if it cannot be parsed.
 */
function jobus_clean_svg_markup( string $svg ) {
	// Drop DOCTYPE/ENTITY declarations outright (XXE / entity-expansion vectors).
	$svg = preg_replace( '/<!DOCTYPE.*?>/is', '', $svg );
	$svg = preg_replace( '/<!ENTITY.*?>/is', '', $svg );

	$dom                      = new DOMDocument();
	$dom->preserveWhiteSpace  = false;
	$previous                 = libxml_use_internal_errors( true );
	$loaded                   = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOENT | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	if ( ! $loaded ) {
		return null;
	}

	$blocked_elements = [ 'script', 'foreignObject', 'iframe', 'embed', 'object', 'audio', 'video', 'animate', 'animatetransform', 'animatemotion', 'set', 'handler', 'use' ];

	$xpath = new DOMXPath( $dom );
	// Remove disallowed elements.
	foreach ( $xpath->query( '//*' ) as $node ) {
		if ( in_array( strtolower( $node->nodeName ), $blocked_elements, true ) ) {
			$node->parentNode->removeChild( $node );
			continue;
		}
		if ( ! $node->hasAttributes() ) {
			continue;
		}
		// Iterate over a static list because we mutate the attribute set.
		$attributes = [];
		foreach ( $node->attributes as $attr ) {
			$attributes[] = $attr;
		}
		foreach ( $attributes as $attr ) {
			$name  = strtolower( $attr->nodeName );
			$value = preg_replace( '/\s+/', '', strtolower( $attr->nodeValue ) );
			// Strip event handlers and unsafe URI schemes.
			if ( 0 === strpos( $name, 'on' )
				|| ( in_array( $name, [ 'href', 'xlink:href', 'src' ], true )
					&& ( 0 === strpos( $value, 'javascript:' ) || 0 === strpos( $value, 'data:' ) ) ) ) {
				$node->removeAttribute( $attr->nodeName );
			}
		}
	}

	$output = $dom->saveXML( $dom->documentElement );
	return ( false === $output ) ? null : $output;
}


/**
 * Redirect user after login based on their role
 */
function jobus_login_redirect_by_role( $redirect_to, $request, $user ) {
	if ( ! $user || ! is_a( $user, 'WP_User' ) ) {
		return $redirect_to;
	}

	$user_roles = (array) $user->roles;

	// Never force redirect administrators (allow them to access wp-admin naturally)
	if ( in_array( 'administrator', $user_roles, true ) || user_can( $user, 'manage_options' ) ) {
		return $redirect_to;
	}

	$user_role = reset( $user_roles );

	// Check for custom redirect settings first
	if ( function_exists( 'jobus_opt' ) && jobus_opt( 'enable_custom_redirects' ) ) {
		$page_id = absint( jobus_opt( 'dashboard_redirect_page' ) );
		if ( $page_id > 0 ) {
			$url = get_permalink( $page_id );
			if ( ! empty( $url ) ) {
				return esc_url_raw( $url );
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
	
	if ( current_user_can( 'manage_options' ) ) {
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

/**
 * Remove a permanently deleted job/candidate from every user's saved list.
 *
 * Saved jobs (candidates' `jobus_saved_jobs`) and saved candidates (employers'
 * `jobus_saved_candidates`) are stored as serialized ID arrays in user meta, so
 * nothing cleans them up automatically and deleted posts would otherwise inflate
 * saved counts and pagination forever.
 *
 * Runs on permanent delete only — a trashed post can be restored, so its saved
 * entries are kept (front-end queries already filter out non-publish posts).
 */
function jobus_cleanup_saved_lists_on_post_delete( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$meta_keys_by_post_type = array(
		'jobus_job'       => 'jobus_saved_jobs',
		'jobus_candidate' => 'jobus_saved_candidates',
	);

	if ( ! isset( $meta_keys_by_post_type[ $post->post_type ] ) ) {
		return;
	}

	global $wpdb;
	$meta_key = $meta_keys_by_post_type[ $post->post_type ];
	$post_id  = (int) $post_id;

	// LIKE on the serialized integer narrows the scan to users that plausibly saved
	// this post (it can false-positive on array keys, which the re-filter below
	// handles); the authoritative removal happens through the meta API per user.
	$user_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value LIKE %s",
			$meta_key,
			'%' . $wpdb->esc_like( 'i:' . $post_id . ';' ) . '%'
		)
	);

	foreach ( $user_ids as $user_id ) {
		$saved = get_user_meta( $user_id, $meta_key, true );
		if ( ! is_array( $saved ) ) {
			continue;
		}

		$filtered = array_values( array_filter( array_map( 'intval', $saved ), static function ( $saved_id ) use ( $post_id ) {
			return $saved_id !== $post_id;
		} ) );

		if ( count( $filtered ) !== count( $saved ) ) {
			update_user_meta( $user_id, $meta_key, $filtered );
		}
	}
}
add_action( 'before_delete_post', 'jobus_cleanup_saved_lists_on_post_delete' );