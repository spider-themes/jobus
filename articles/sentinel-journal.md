## 2026-01-17 - Unrestricted File Upload Types in Resume Form
**Vulnerability:** The candidate resume upload form used `wp_handle_upload` without specifying allowed MIME types. This meant any file type allowed by WordPress (including images, audio, video) could be uploaded as a resume, potentially leading to storage abuse or confusion, even if not direct RCE (since WP blocks PHP by default).
**Learning:** `wp_handle_upload` defaults to `get_allowed_mime_types()`, which is broad. Specific file inputs (like resumes) must explicitly define their allowed MIME types using the `mimes` override parameter to enforce business logic and security depth.
**Prevention:** Always define a strict whitelist of allowed MIME types when handling specific file uploads using `wp_handle_upload`.

## 2026-01-17 - Email Spoofing and Missing Rate Limiting in Contact Form
**Vulnerability:** The candidate contact form allowed unauthenticated users to send emails with user-controlled "From" headers, leading to potential phishing/spoofing. It also lacked rate limiting, allowing for email spam.
**Learning:** `wp_mail` headers must be carefully constructed. Allowing user input in the "From" header is dangerous. Always use the site's authoritative email for "From" and put user email in "Reply-To". Also, public AJAX endpoints sending email MUST have rate limiting.
**Prevention:** Enforce "From" header to be `get_bloginfo('name') <admin_email>`. Use transients for simple IP-based rate limiting on public forms.
