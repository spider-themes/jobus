<?php
/**
 * Jobus helper functions: cache versioning, locks, invalidation, attachment ownership.
 *
 * Extracted from includes/functions.php, which was split into focused includes
 * under includes/helpers/ for maintainability. Loaded by includes/functions.php.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Invalidate the range field values cache when a job is saved or deleted.
 *
 * Ensures the cached salary/range data is never stale after job edits.
 */
add_action( 'save_post', function( $post_id ) {
    // Revisions and autosaves are not real content changes — bumping on them
    // needlessly cold-caches every aggregate (a single manual save fires save_post
    // for the autosave AND the revision AND the post itself).
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }
    $post_type = get_post_type( $post_id );
    if ( in_array( $post_type, ['jobus_job', 'jobus_candidate', 'jobus_company'], true ) ) {
        // One version bump invalidates every version-namespaced count/aggregate cache.
        jobus_bump_cache_version();
    }
} );

add_action( 'before_delete_post', function( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( in_array( $post_type, ['jobus_job', 'jobus_candidate', 'jobus_company'], true ) ) {
        jobus_bump_cache_version();
    }
} );

/**
 * Current cache version used to namespace plugin count/aggregate caches.
 *
 * Invalidation is done by bumping this version rather than deleting transients by
 * prefix. The old LIKE-on-wp_options DELETE was a no-op under a persistent object
 * cache (Redis/Memcached) — transients don't live in wp_options there — and it also
 * never matched the real cache keys. Versioning is O(1) and object-cache safe.
 *
 * @return int
 */
function jobus_cache_version(): int {
	return (int) get_option( 'jobus_cache_version', 1 );
}

/**
 * Invalidate all version-namespaced caches by incrementing the cache version.
 *
 * @return void
 */
function jobus_bump_cache_version(): void {
	// Debounce: a single request (e.g. a bulk import or a save that triggers
	// several hooks) only needs to invalidate the caches once. Without this, an
	// N-post import does N read-modify-write option updates and guarantees a cold
	// cache for the whole batch.
	static $bumped_this_request = false;
	if ( $bumped_this_request ) {
		return;
	}
	$bumped_this_request = true;

	update_option( 'jobus_cache_version', jobus_cache_version() + 1 );
}

/**
 * Acquire a short-lived lock to prevent a cache stampede (the "dogpile" effect)
 * where many concurrent requests recompute the same heavy query on a cold cache.
 *
 * Uses wp_cache_add() which is atomic under a persistent object cache, falling back
 * to a transient otherwise.
 *
 * @param string $key Lock identifier.
 * @param int    $ttl Lock lifetime in seconds.
 * @return bool True if the lock was acquired by this process.
 */
function jobus_acquire_cache_lock( string $key, int $ttl = 30 ): bool {
	$lock = 'jobus_lock_' . md5( $key );

	if ( wp_using_ext_object_cache() ) {
		return wp_cache_add( $lock, 1, 'jobus', $ttl );
	}

	if ( false !== get_transient( $lock ) ) {
		return false;
	}

	set_transient( $lock, 1, $ttl );
	return true;
}

/**
 * Release a lock acquired via jobus_acquire_cache_lock().
 *
 * @param string $key Lock identifier.
 * @return void
 */
function jobus_release_cache_lock( string $key ): void {
	$lock = 'jobus_lock_' . md5( $key );

	if ( wp_using_ext_object_cache() ) {
		wp_cache_delete( $lock, 'jobus' );
		return;
	}

	delete_transient( $lock );
}

/**
 * Verify that an uploaded attachment belongs to the given user before a
 * self-service profile form stores its ID.
 *
 * Front-end profile/CV/portfolio/logo fields accept an attachment ID straight
 * from the request. Without an ownership check a user could submit any attachment
 * ID on the site (an IDOR), surfacing another user's otherwise-unlinked upload on
 * their own public profile. Media-library uploads are always authored by the
 * uploader, so post_author is the correct ownership signal; users who can edit
 * others' files (admins/editors) are allowed through.
 *
 * @param int $attachment_id Attachment post ID from the request.
 * @param int $user_id       The user the attachment is being attached to.
 * @return bool
 */
function jobus_user_owns_attachment( int $attachment_id, int $user_id ): bool {
	if ( $attachment_id <= 0 || $user_id <= 0 ) {
		return false;
	}
	if ( 'attachment' !== get_post_type( $attachment_id ) ) {
		return false;
	}
	if ( (int) get_post_field( 'post_author', $attachment_id ) === $user_id ) {
		return true;
	}
	// Allow privileged users who can legitimately manage others' media.
	return user_can( $user_id, 'edit_others_posts' );
}

/**
 * Helper: Invalidate dynamically cached meta counts (used in sidebar checkboxes).
 *
 * Retained for backward compatibility; now bumps the cache version, which
 * transparently invalidates every version-namespaced count/aggregate cache.
 */
function jobus_clear_meta_count_caches() {
	jobus_bump_cache_version();
}


