## 2024-05-22 - [Duplicate Functions]
**Learning:** Functions in `includes/functions.php` (e.g., `jobus_rtl`) were defined twice (once unguarded, once guarded), causing potential redeclaration errors.
**Action:** Always grep for function names before defining them or refactoring, and ensure all global functions are wrapped in `function_exists` checks.

## 2024-05-22 - [Array Syntax]
**Learning:** The codebase mixed `array()` and `[]` syntax.
**Action:** Enforce short array syntax `[]` in all refactored files for consistency with modern WP/PHP standards.
