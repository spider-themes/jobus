<?php
/**
 * Anonymous usage reporting for Jobus.
 *
 * Counts jobs, applications, candidates, and companies. Never stores job descriptions,
 * applicant resumes, candidate personal data, or employer contact info.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jobus_telemetry_sdk = __DIR__ . '/class-spider-telemetry.php';
if ( file_exists( $jobus_telemetry_sdk ) ) {
	require_once $jobus_telemetry_sdk;
}

/**
 * Whether Freemius usage sharing is on for Jobus.
 *
 * The opt-in click is the decision. Email confirmation only finishes the
 * Freemius account, so a pending activation still counts.
 *
 * @return bool
 */
function jobus_fs_usage_allowed() {
	if ( ! function_exists( 'jobus_fs' ) ) {
		return false;
	}
	$fs = jobus_fs();
	if ( method_exists( $fs, 'is_tracking_allowed' ) && $fs->is_tracking_allowed() ) {
		return true;
	}
	return method_exists( $fs, 'is_pending_activation' ) && $fs->is_pending_activation();
}

/**
 * Whether the settings switch should show usage sharing as on.
 *
 * A missing choice stays off. Freemius tracking counts only when the site
 * has not opted out.
 *
 * @return bool
 */
function jobus_share_usage_enabled() {
	if ( get_option( 'spiderthemes_telemetry_jobus_optout', false ) ) {
		return false;
	}
	if ( get_option( 'spiderthemes_telemetry_jobus_optin', false ) ) {
		return true;
	}
	return jobus_fs_usage_allowed();
}

/**
 * Whether a settings switch value is on.
 *
 * @param mixed $value Stored or posted value.
 * @return bool
 */
function jobus_telemetry_is_on( $value ) {
	return in_array( $value, array( true, 1, '1', 'true', 'on' ), true );
}

/**
 * Show the live consent choice on the settings screen without storing it
 * inside the Jobus settings array.
 *
 * @param mixed $value Settings option.
 * @return mixed
 */
function jobus_telemetry_settings_display( $value ) {
	if ( ! is_admin() || ! is_array( $value ) ) {
		return $value;
	}
	if ( empty( $_GET['page'] ) || 'jobus-settings' !== $_GET['page'] ) {
		return $value;
	}
	$value['share_anonymous_usage'] = jobus_share_usage_enabled() ? '1' : '0';
	return $value;
}
add_filter( 'option_jobus_opt', 'jobus_telemetry_settings_display' );

/**
 * Apply an explicit Share usage change, then keep it out of the settings array.
 *
 * Saving any other setting leaves consent untouched.
 *
 * @param mixed $data Settings being saved.
 * @return mixed
 */
function jobus_telemetry_on_settings_save( $data ) {
	if ( ! is_array( $data ) || ! array_key_exists( 'share_anonymous_usage', $data ) ) {
		return $data;
	}
	$posted = jobus_telemetry_is_on( $data['share_anonymous_usage'] );
	unset( $data['share_anonymous_usage'] );

	if ( $posted === jobus_share_usage_enabled() || ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return $data;
	}
	if ( $posted ) {
		delete_option( 'spiderthemes_telemetry_jobus_optout' );
		\SpiderThemes\Telemetry::grant_consent( 'jobus', 'user' );
	} else {
		\SpiderThemes\Telemetry::revoke_consent( 'jobus' );
	}
	return $data;
}
add_filter( 'csf_jobus_opt_save', 'jobus_telemetry_on_settings_save', 20 );

add_action( 'init', 'jobus_boot_telemetry', 1 );

/**
 * Register the telemetry client after translations are available.
 *
 * @return void
 */
function jobus_boot_telemetry() {
	if ( ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return;
	}

	\SpiderThemes\Telemetry::init(
		array(
			'plugin_slug'              => 'jobus',
			'plugin_name'              => 'Jobus',
			'plugin_version'           => defined( 'JOBUS_VERSION' ) ? JOBUS_VERSION : '1.15.0',
			'plugin_file'              => defined( 'JOBUS_FILE' ) ? JOBUS_FILE : dirname( __DIR__ ) . '/jobus.php',
			'optin_title'              => __( 'Help us improve Jobus', 'jobus' ),
			'optin_message'            => __( 'Help make Jobus better by sharing anonymous job and application counts. Job descriptions and applicant personal data are never collected.', 'jobus' ),
			'consent_callback'         => 'jobus_fs_usage_allowed',
			'consent_follows_callback' => true,
			'custom_metrics'           => 'jobus_telemetry_metrics',
		)
	);
}

/**
 * Count posts of one type in the publish status.
 *
 * @param string $post_type Post type.
 * @param string $since     GMT timestamp lower bound. Empty counts every published post.
 * @return int
 */
function jobus_telemetry_posts( $post_type, $since = '' ) {
	global $wpdb;
	$sql  = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'";
	$args = array( $post_type );
	if ( '' !== $since ) {
		$sql   .= ' AND post_date_gmt >= %s';
		$args[] = $since;
	}
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return (int) $wpdb->get_var( $wpdb->prepare( $sql, ...$args ) );
}

/**
 * Count terms in a taxonomy.
 *
 * @param string $taxonomy Taxonomy name.
 * @return int
 */
function jobus_telemetry_terms( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return 0;
	}
	$count = wp_count_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	return is_wp_error( $count ) ? 0 : (int) $count;
}

/**
 * Metrics payload reported on activation and weekly heartbeats.
 *
 * Counts only. Never includes job content, applicant resumes, personal data, or email addresses.
 *
 * @return array<string, int>
 */
function jobus_telemetry_metrics() {
	$now_30d = gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS );

	$jobs             = jobus_telemetry_posts( 'jobus_job' );
	$jobs_30d         = jobus_telemetry_posts( 'jobus_job', $now_30d );
	$applications     = jobus_telemetry_posts( 'jobus_applicant' );
	$applications_30d = jobus_telemetry_posts( 'jobus_applicant', $now_30d );
	$candidates       = jobus_telemetry_posts( 'jobus_candidate' );
	$companies        = jobus_telemetry_posts( 'jobus_company' );

	$job_categories   = jobus_telemetry_terms( 'jobus_job_cat' );
	$job_locations    = jobus_telemetry_terms( 'jobus_job_location' );
	$job_tags         = jobus_telemetry_terms( 'jobus_job_tag' );
	$candidate_skills = jobus_telemetry_terms( 'jobus_candidate_skill' );

	$options          = get_option( 'jobus_opt', array() );
	$candidate_module = ( ! isset( $options['enable_candidate'] ) || ! empty( $options['enable_candidate'] ) ) ? 1 : 0;
	$company_module   = ( ! isset( $options['enable_company'] ) || ! empty( $options['enable_company'] ) ) ? 1 : 0;

	return array(
		'jobs'             => $jobs,
		'jobs_30d'         => $jobs_30d,
		'applications'     => $applications,
		'applications_30d' => $applications_30d,
		'candidates'       => $candidates,
		'companies'        => $companies,
		'job_categories'   => $job_categories,
		'job_locations'    => $job_locations,
		'job_tags'         => $job_tags,
		'candidate_skills' => $candidate_skills,
		'candidate_module' => $candidate_module,
		'company_module'   => $company_module,
	);
}

/**
 * Record a single upgraded_to_pro event the first time Jobus Pro is active.
 *
 * @return void
 */
function jobus_track_pro_upgrade() {
	if ( ! function_exists( 'jobus_is_premium' ) || ! jobus_is_premium() ) {
		return;
	}
	if ( get_option( 'jobus_telemetry_tracked_pro_upgrade' ) ) {
		return;
	}
	if ( ! class_exists( 'SpiderThemes\\Telemetry' ) ) {
		return;
	}

	\SpiderThemes\Telemetry::track_event( 'jobus', 'upgraded_to_pro' );
	update_option( 'jobus_telemetry_tracked_pro_upgrade', time(), false );
}
add_action( 'admin_init', 'jobus_track_pro_upgrade' );
