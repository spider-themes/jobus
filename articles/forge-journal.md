## 2026-02-07 - Cron Implementation and Meta Usage
**Learning:** Implemented daily cron for job expiration. The `job_deadline` meta key stores dates in `Y-m-d` format, which is critical for direct meta queries using `DATE` type.
**Action:** When working with date-based meta queries in Jobus, ensure the format matches `Y-m-d` and use `type => DATE`. Always batch cron processing (e.g., 50 items) to avoid timeouts.
