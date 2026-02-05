## 2024-05-22 - WPCS Enforcement
**Learning:** This codebase had inconsistent array syntax (mixed `array()` and `[]`) and spacing conventions.
**Action:** Enforce short array syntax `[]` and standard WPCS spacing (spaces inside parentheses) in all modified files. Use strict comparisons (`===`) and Yoda conditions (`'value' === $var`) where possible to prevent bugs.

## 2024-05-22 - I18n in Main Plugin File
**Learning:** Hardcoded strings in `jobus.php` for default page titles prevented proper translation.
**Action:** When refactoring main plugin files, always check for and wrap user-facing strings (like page titles) in `esc_html__()` or `__()`.
