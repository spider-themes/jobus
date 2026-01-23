## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-18 - [Optimizing Heavy Range Filters]
**Learning:** Functions that fetch and process all posts (like `jobus_all_range_field_value` which unserializes meta for every job) can become massive bottlenecks if not guarded by checks for active user input. In this case, a nonce check was also preventing the feature from working, but fixing the nonce without adding the guard would have caused severe performance degradation on all searches.
**Action:** Always check if a heavy filter is actually active (via `$_GET` or specific params) before triggering the expensive data fetching logic.
