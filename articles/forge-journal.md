## 2024-03-10 - Job Auto Expiration
**Learning:** Jobs past their deadline are not automatically expired, forcing site admins to do so manually, and leading to bad UX since candidates see closed jobs. Added `includes/Classes/Cron/Cron_Tasks.php` to handle automated tasks.
**Action:** Created `jobus_daily_maintenance` cron job to automatically draft expired jobs in batches using WP Cron API and batch processing.
