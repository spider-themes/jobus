## 2026-01-17 - Unrestricted File Upload Types in Resume Form
**Vulnerability:** The candidate resume upload form used `wp_handle_upload` without specifying allowed MIME types. This meant any file type allowed by WordPress (including images, audio, video) could be uploaded as a resume, potentially leading to storage abuse or confusion, even if not direct RCE (since WP blocks PHP by default).
**Learning:** `wp_handle_upload` defaults to `get_allowed_mime_types()`, which is broad. Specific file inputs (like resumes) must explicitly define their allowed MIME types using the `mimes` override parameter to enforce business logic and security depth.
**Prevention:** Always define a strict whitelist of allowed MIME types when handling specific file uploads using `wp_handle_upload`.

## 2026-01-17 - IDOR in Application Deletion due to Weak Ownership Verification
**Vulnerability:** The `remove_job_application` AJAX handler verified permission by matching the user's email to the `candidate_email` meta field. This allowed any user with a matching email (spoofed or shared) to delete another user's application.
**Learning:** Relying on mutable or unverified strings (like email addresses stored in meta) for authorization is insecure. Users can potentially manipulate these fields or their own profile to impersonate ownership.
**Prevention:** Always verify object ownership using immutable, trusted identifiers like `post_author` or `user_id` when performing destructive actions.
