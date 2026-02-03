## 2024-05-22 - WPCS Enforcement
**Learning:** This codebase had inconsistent array syntax (mixed `array()` and `[]`) and spacing conventions.
**Action:** Enforce short array syntax `[]` and standard WPCS spacing (spaces inside parentheses) in all modified files. Use strict comparisons (`===`) and Yoda conditions (`'value' === $var`) where possible to prevent bugs.

## 2024-10-25 - Duplicate Function Definitions in Includes
**Learning:** `includes/functions.php` contained a duplicate definition of `jobus_rtl` (one unconditional at the top, one conditional at the bottom). This poses a risk of fatal errors if the file is loaded multiple times or if another plugin defines it.
**Action:** Always check for existing function definitions (grep) before modifying or adding utility functions. Use `function_exists` wrappers for pluggable functions.
