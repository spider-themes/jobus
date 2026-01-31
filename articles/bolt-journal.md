## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Hidden Heavy Query in Archive Loader]
**Learning:** `jobus_all_range_field_value()` fetches and unserializes `jobus_meta_options` for *all* published jobs/candidates whenever range widgets are present in the sidebar, even if no search is actually performed by the user. This causes massive overhead on simple archive page views.
**Action:** Always check if the user has provided input for a filter before running expensive queries to fetch data for that filter. In `jobus_process_range_filters`, skip the heavy DB call if no range filter inputs are present in `$_GET`.

## 2026-01-17 - [Caching Heavy Range Filter Queries]
**Learning:** `jobus_all_range_field_value` performs full table scans and unserialization even when optimized to run only on active searches. This remains a scalability bottleneck for active filtering.
**Action:** Use transients to cache the processed range values, keyed by the active widget configuration, to serve repeated search requests instantly without hitting the DB.
