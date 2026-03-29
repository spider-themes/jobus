## 2026-02-07 - Cron Batch Processing Continuation Events
**Learning:** When processing large datasets via WP Cron, using a distinct continuation hook (e.g., `jobus_auto_expire_jobs_batch_continue`) instead of re-scheduling the primary daily hook prevents overlapping schedules, timeouts, and logic collisions when returning a 50-item batch.
**Action:** Always map continuation events to their own distinct hook and map both the primary and continuation hook to the same processing method. Also, always clear BOTH hooks in the `deactivate()` method.
