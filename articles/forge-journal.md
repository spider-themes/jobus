## 2026-02-07 - Auto-expire jobs via Cron
**Learning:** When using batched processing in a scheduled WordPress cron to avoid timeouts, always hook the continuation batch to a separate hook (e.g. `jobus_auto_expire_jobs_batch_continue`) or register the handler directly to it rather than re-scheduling the daily hook (`jobus_daily_maintenance`) to avoid overlapping schedules.
**Action:** Implemented `wp_schedule_single_event` for batch continuation linking to the same class handler in `Job_Expirator.php`.
