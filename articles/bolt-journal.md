## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Hidden Heavy Query in Archive Loader]
**Learning:** `jobus_all_range_field_value()` fetches and unserializes `jobus_meta_options` for *all* published jobs/candidates whenever range widgets are present in the sidebar, even if no search is actually performed by the user. This causes massive overhead on simple archive page views.
**Action:** Always check if the user has provided input for a filter before running expensive queries to fetch data for that filter. In `jobus_process_range_filters`, skip the heavy DB call if no range filter inputs are present in `$_GET`.

## 2026-01-20 - [N+1 Queries in Filter Widgets]
**Learning:** Functions like `jobus_count_meta_key_usage` are often called inside loops (e.g., rendering checkbox lists for filters). Without caching, this triggers a database query for every single filter option, causing an N+1 problem that scales with the number of filter options.
**Action:** Wrap these "count" functions with `get_transient` / `set_transient` using a unique hash of the parameters as the key. A 1-hour cache duration is usually a good balance for filter counts.
