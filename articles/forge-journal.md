## 2026-02-07 - Missing Meta Key and CSF Serialization
**Learning:** The `job_deadline` meta key was expected by `Admin/Dashboard.php` but missing from the codebase. Additionally, CodeStar Framework (CSF) defaults to serialized storage, which prevents direct meta queries unless `data_type => 'unserialize'` is explicitly set.
**Action:** Always verify if a meta key used in queries exists in the configuration. When adding new searchable/queryable meta fields via CSF, explicitly set `data_type => 'unserialize'`.
