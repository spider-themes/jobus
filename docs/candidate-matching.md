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

## Custom Matching Configuration

Settings → Candidate Matching controls the engine without code:

- **Enable/disable** each criterion: Teaching Level, Subjects, Travel Distance, Availability, and Job Date & Time (off by default).
- **Mode per criterion** (`matching_mode_{id}`): `Required` (default) excludes candidates who fail; `Preferred` never excludes, only lowers their score — avoids empty shortlists.
- **Weight per criterion** (`matching_weight_{id}`, 0-10, default 1): weighted ranking score in `Match_Engine`. All weights at 1 reproduces the original equal-weight average.
- **Job Date & Time**: job sets `match_job_date` + start/end time; candidate sets available days (`mon`–`sun`) + available from/until. The job date's weekday must be an available day and the time windows must overlap. Empty job fields skip the rule.

New criteria are added by implementing `Criterion_Interface` (`id`/`is_enabled`/`matches`/`score`) and registering via the `jobus_matching_criteria` filter or `Criteria_Registry` — no engine changes needed. Mode/weight settings then work automatically via `matching_mode_{id}` / `matching_weight_{id}`.

## Setup

1. Activate Jobus Pro
2. Enable matching + edit Subjects
3. Sync Candidate Locations
4. Geolocation → sync jobs
5. Permalinks → Save (once)
