## 2026-02-07 - Application Status Change Notifications
**Learning:** The `jobus_application_status_changed` action provides a critical integration point for Pro notifications, filling the highest-impact communication gap for candidates.
**Action:** When implementing status updates, always fire `jobus_application_status_changed` even if the free version doesn't handle full templating, so extensions can hook in.
