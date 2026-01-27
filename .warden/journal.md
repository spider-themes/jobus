## 2024-05-22 - [Duplicate Functions]
**Learning:** Functions in `includes/functions.php` (e.g., `jobus_rtl`) were defined twice (once unguarded, once guarded), causing potential redeclaration errors.
**Action:** Always grep for function names before defining them or refactoring, and ensure all global functions are wrapped in `function_exists` checks.

## 2024-05-22 - [Array Syntax]
**Learning:** The codebase mixed `array()` and `[]` syntax.
**Action:** Enforce short array syntax `[]` in all refactored files for consistency with modern WP/PHP standards.
## 2024-05-22 - WPCS Enforcement
**Learning:** This codebase had inconsistent array syntax (mixed `array()` and `[]`) and spacing conventions.
**Action:** Enforce short array syntax `[]` and standard WPCS spacing (spaces inside parentheses) in all modified files. Use strict comparisons (`===`) and Yoda conditions (`'value' === $var`) where possible to prevent bugs.
