## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Hidden Heavy Query in Archive Loader]
**Learning:** `jobus_all_range_field_value()` fetches and unserializes `jobus_meta_options` for *all* published jobs/candidates whenever range widgets are present in the sidebar, even if no search is actually performed by the user. This causes massive overhead on simple archive page views.
**Action:** Always check if the user has provided input for a filter before running expensive queries to fetch data for that filter. In `jobus_process_range_filters`, skip the heavy DB call if no range filter inputs are present in `$_GET`.

## 2026-01-17 - [Caching expensive meta parsing]
**Learning:** Parsing serialized meta data for range sliders (`jobus_all_range_field_value`) is extremely expensive (N+1 unserialize operations). Since this data only changes when jobs are updated, transient caching is highly effective.
**Action:** Use `set_transient` to cache the parsed range values, keyed by a hash of the active widget configuration. Invalidate this cache on `save_post` and option updates to ensure data freshness.
