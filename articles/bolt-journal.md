## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Avoid Unnecessary Range Filter Queries]
**Learning:** `jobus_all_range_field_value` fetched and unserialized metadata for ALL posts if *any* range widget was active in the sidebar, even if the user wasn't filtering by it. It also used an incorrect nonce, breaking functionality.
**Action:** Always check if a specific filter parameter is actually present in the request (`$_GET`) before triggering heavy database queries for that filter. Verify nonce actions match frontend implementation.
