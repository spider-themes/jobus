## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Hidden Heavy Query in Archive Loader]
**Learning:** `jobus_all_range_field_value()` fetches and unserializes `jobus_meta_options` for *all* published jobs/candidates whenever range widgets are present in the sidebar, even if no search is actually performed by the user. This causes massive overhead on simple archive page views.
**Action:** Always check if the user has provided input for a filter before running expensive queries to fetch data for that filter. In `jobus_process_range_filters`, skip the heavy DB call if no range filter inputs are present in `$_GET`.

## 2026-01-18 - [N+1 Queries in Filter Widgets]
**Learning:** Functions like `jobus_count_meta_key_usage` that execute direct SQL queries are often called inside loops in filter templates (e.g., `templates/filter-widgets/checkbox.php`), leading to severe N+1 query performance issues (one query per filter option).
**Action:** Always wrap these count-heavy helper functions with `get_transient`/`set_transient` caching, using a hash of the arguments as the key, especially when they are likely to be called repeatedly on the same page.
