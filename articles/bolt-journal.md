## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Hidden Heavy Query in Archive Loader]
**Learning:** `jobus_all_range_field_value()` fetches and unserializes `jobus_meta_options` for *all* published jobs/candidates whenever range widgets are present in the sidebar, even if no search is actually performed by the user. This causes massive overhead on simple archive page views.
**Action:** Always check if the user has provided input for a filter before running expensive queries to fetch data for that filter. In `jobus_process_range_filters`, skip the heavy DB call if no range filter inputs are present in `$_GET`.

## 2026-01-17 - [Redundant Querying in Template Loops]
**Learning:** `jobus_get_selected_company_count` was being called twice per company in archive loops (once for count, once for link), triggering two separate DB queries. The second call (`posts_per_page => -1`) was particularly heavy as it fetched all job IDs.
**Action:** When a function provides related data (like count and link) that requires the same underlying query, cache the result of the "heavier" query (e.g., fetching IDs) and use it to serve both requests, significantly reducing DB hits in loops.

## 2026-01-17 - [Caching Empty States]
**Learning:** When caching query results, using `empty($cached_data['ids'])` to validate the cache can fail if the valid result is an empty set (e.g., 0 jobs). This causes the code to treat the valid empty result as "missing cache" and re-query unnecessarily.
**Action:** Use `!isset()` to check for cache existence, or explicitly check for `false` if `get_transient` is used, to ensure that valid empty results are respected and served from cache.
