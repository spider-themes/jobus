## 2024-10-24 - Code Style Inconsistencies
**Learning:** The codebase previously used a mix of Allman (braces on new line) and K&R (braces on same line) styles, along with mixed array syntax (`array()` vs `[]`). `jobus.php` and `Ajax_Actions.php` were notably inconsistent with WPCS.
**Action:** Standardized on K&R brace style and short array syntax `[]` for all future development to align with WordPress Coding Standards and modern PHP practices.

## 2024-10-24 - WPCS Spacing
**Learning:** Many files lacked proper spacing inside parentheses (e.g., `if (!defined)` instead of `if ( ! defined )`).
**Action:** Enforce WPCS spacing rules strictly during code reviews and edits.
