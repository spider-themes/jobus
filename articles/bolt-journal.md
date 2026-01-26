## 2026-01-17 - [Optimizing count queries with WP_Query vs wpdb]
**Learning:** `WP_Query` with `posts_per_page => -1` hydrates all post objects, which is extremely memory intensive and slow when only a count is needed. Even with `fields => 'ids'`, it still loads all IDs into memory.
**Action:** When only a count is needed, especially for meta queries, use `$wpdb->get_var` with `COUNT(*)` (or `COUNT(DISTINCT ID)`) to avoid object hydration and reduce memory usage significantly.

## 2026-01-17 - [Caching facet counts in loops]
**Learning:** Facet counts (e.g., in filter sidebars) often trigger N+1 queries using expensive `LIKE` comparisons on postmeta. Running these queries on every page load causes significant performance degradation.
**Action:** Always cache the results of such count queries in transients (e.g., for 1 hour) using a unique key based on the query arguments. This transforms O(N) database load into O(1) cache hits for most users.
