# AGENTS.md — Jobus (Free)

Guide for AI coding assistants working in the `jobus` WordPress plugin repository.

## 1. Plugin Identity & Environment
- **Plugin Name:** Jobus (`jobus.php`)
- **Text Domain:** `jobus` (Domain Path: `/languages`)
- **Namespace:** `jobus\` (PSR-4 autoloaded in `composer.json` from `.`, `Admin/`, `includes/Classes/`, `includes/Elementor/`, `includes/Frontend/`)
- **Compatibility:** WordPress 6.0+, PHP 7.4+ (avoid PHP 8.0+ only syntax unless guarded).

## 2. Directory Map
- `jobus.php` — Main plugin bootstrap, constants, dependency loading, Freemius SDK init.
- `Blocks.php` — Gutenberg block registration (sources live in `src/`, compiled to `build/`).
- `Admin/` — Admin UI, CPTs (`Admin/cpt/`), Codestar Framework options (`Admin/csf/`), Onboarding, Demo Importer, Analytics, Messaging, Status.
- `includes/Classes/` — Core domain services (`Ajax_Actions.php`, `Ajax/` traits, `Cron_Manager.php`, `Job_Expiry.php`, `Job_Schema.php`, `Lifecycle.php`, `Search_Index.php`, `Geolocation.php`, `Emails/`, `OAuth/`, `submission/`).
- `includes/Frontend/` — Frontend assets, shortcodes, dashboards (`Dashboard.php`, `Dashboard_Candidate.php`, `Dashboard_Employer.php`), template loader.
- `includes/Elementor/` — Elementor widgets and integrations.
- `includes/functions.php` & `includes/helpers/` — Global helper loader (`functions.php`) and domain helper modules (`core.php`, `template.php`, `taxonomy-meta.php`, `job-query.php`), plus `includes/filters.php` for upload/redirect/security filters.
- `templates/` — Overridable frontend templates (`archive-*.php`, `single-*.php`, `dashboard/`, `emails/`, `filter-widgets/`).
- `src/` — Gutenberg block JS/JSX sources.
- `assets/` — Compiled CSS/JS/SCSS and images (`assets/vendors/` is third-party; do not edit).
- `docs/` — Architecture notes, plans, governance runbooks, and articles.

## 3. Companion Plugin (`jobus-pro`)
- `jobus-pro` requires `jobus` (`Requires Plugins: jobus`) and extends it via WordPress actions/filters and template overrides.
- Avoid breaking public helper functions in `includes/functions.php` or filter signatures used by `jobus-pro`.

## 4. Coding, Security & Localization Rules
- **Security:** Every PHP file must start with `defined( 'ABSPATH' ) || exit;`. Sanitize all inputs, escape all outputs at render time, verify nonces and capabilities on AJAX/REST/form handlers, and use `$wpdb->prepare()` for all dynamic SQL.
- **Performance:** No database queries inside loops, no `SELECT *`, limit query results, and enqueue assets only where needed.
- **i18n:** Whenever new translatable strings are added using text domain `'jobus'`, update `languages/jobus.pot`.
- **Standards:** Follow WordPress Coding Standards (`phpcs.xml.dist`) and static analysis (`phpstan.neon`).

## 5. Common Commands
- **PHP Syntax Check:** `find . -name "*.php" -not -path "./vendor/*" -not -path "./lib/*" -exec php -l {} +`
- **PHPCS Lint:** `composer lint` (after installing dev dependencies)
- **PHPStan Analysis:** `composer analyze`
- **JS/Blocks Build:** `npm run build` (or `npm start` for watch mode)
