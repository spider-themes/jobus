<?php
/**
 * Jobus procedural helper loader.
 *
 * This file was once a ~1,800-line monolith. Its helpers are now split into
 * focused includes under includes/helpers/ for maintainability. This file stays
 * in Composer's `files` autoload and loads them in their original order, so every
 * function definition and file-scope hook registration runs exactly as before —
 * no behavioural change, just structure.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once __DIR__ . '/helpers/general.php';   // Premium gating, options, currency, basic meta.
require_once __DIR__ . '/helpers/template.php';  // Template loading, taxonomy, archive output.
require_once __DIR__ . '/helpers/query.php';     // Specs, meta-count queries, search, range/company data.
require_once __DIR__ . '/helpers/cache.php';     // Cache versioning, locks, invalidation, attachment ownership.
require_once __DIR__ . '/helpers/ui.php';        // Result counts, social/icon UI, spec-name helpers.
require_once __DIR__ . '/helpers/dashboard.php'; // Mail, view tracking, dashboard/application helpers.
