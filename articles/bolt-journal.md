# Bolt's Journal

## 2026-01-17 - N+1 Query in Employer Dashboard
**Learning:** The employer dashboard (`templates/dashboard/employer/dashboard.php`) iterates over all job IDs to calculate total views using `get_post_meta` inside a loop. For employers with many jobs, this triggers one query per job if caches are cold.
**Action:** Use `$wpdb` to aggregate the sum in a single query when possible, or batch prime caches. In this case, a direct SQL SUM is most efficient as we only need the total count.
