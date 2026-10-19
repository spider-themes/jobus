# Candidate Matching — Quick Guide

**Off by default.** Enable under **Settings → Candidate Matching**. **Requires Jobus Pro**.

## Plugin split

| Free (`jobus`) | Pro (`jobus-pro`, namespace `Jobus_Pro\Matching`) |
|----------------|--------------------------------------------------|
| Settings + form/meta field UI (gated by premium) | `Match_Engine`, criteria, `Match_Fields`, repositories |
| Job geo index | Candidate geo index + `jobus_job_matches` (`Schema`) |
| Endpoint + `get_template_part_pro` / Pro-lock | `templates/.../matched-candidates.php` |
| My Jobs table hooks | Matches column + emails (`Match_Notification_Handler`) |

## Flow

Job published → Pro engine stores matches → Pro UI + optional email.

## Setup

1. Activate Jobus Pro
2. Enable matching + edit Subjects
3. Sync Candidate Locations
4. Geolocation → sync jobs
5. Permalinks → Save (once)
