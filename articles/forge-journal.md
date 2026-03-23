## 2026-02-07 - Cron Deactivation Hook Limitation
**Learning:** In `jobus.php`, the `deactivate()` method does not clean up cron events. It only deletes the dashboard page. If I add a cron, I must ensure it is properly unregistered in `deactivate()` using `wp_clear_scheduled_hook`.
**Action:** Always include `wp_clear_scheduled_hook` in the deactivation routine when adding new scheduled events.
## 2026-02-07 - Missing Cron Cleanup
**Learning:** `jobus_job_auto_expired` and any related cron jobs do not exist. I should create `jobus_auto_expire_jobs` as a cron job to auto-draft jobs past their `job_deadline`. This is a high-priority enhancement listed in the instructions.
**Action:** Implement `includes/Classes/Cron/Job_Expirator.php` or similar logic. Hook it up via `jobus_daily_maintenance` cron and register/unregister correctly in `jobus.php`.
## 2026-02-07 - Automating Job Expiration
**Learning:** `jobus_job_auto_expired` is a high priority. Missing cron job `jobus_daily_maintenance`.
**Action:** I'll implement `includes/Classes/Cron/Job_Expirator.php` to run on `jobus_daily_maintenance` cron hook. Batch query `jobus_job` with publish status where `job_deadline` < current_time('Y-m-d'). Draft them and fire `jobus_job_auto_expired`. I'll also add it to `init_plugin` in `jobus.php` and update `activate`/`deactivate` in `jobus.php`.
