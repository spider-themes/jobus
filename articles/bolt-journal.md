## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-25 - [Caching expensive meta parsing operations]
**Learning:** Functions that query all posts and unserialize meta values for filtering (like `jobus_all_range_field_value`) are extremely CPU intensive. Running this on every request causes significant performance degradation.
**Action:** Cache the processed results of such operations in a WordPress Transient. Invalidate the cache when the underlying data (posts or options) changes to ensure data consistency while maximizing performance.
