## 2026-01-17 - Initial Setup
**Learning:** The `WP_Query` usage with `posts_per_page => -1` for counting is a common pattern in this codebase that causes significant performance issues by hydrating full post objects unnecessarily.
**Action:** When identifying count-only queries, always check for `fields => 'ids'` and `posts_per_page => 1` (or use `$wpdb` directly if appropriate/safe).
