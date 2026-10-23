<?php
/**
 * System Status & Governance Diagnostics Hub.
 *
 * Provides administrators and support engineers with a comprehensive environment report,
 * table indexing counts, cron health checks, and 1-click diagnostic tools.
 *
 * @package Jobus\Admin
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Status
 */
class Status {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 30 );
		add_action( 'wp_ajax_jobus_run_search_reindex', [ $this, 'ajax_run_reindex' ] );
	}

	/**
	 * Register System Status submenu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_submenu_page(
			'edit.php?post_type=jobus_job',
			esc_html__( 'System Status', 'jobus' ),
			esc_html__( 'System Status', 'jobus' ),
			'manage_options',
			'jobus-status',
			[ $this, 'render_page' ]
		);
	}

	/**
	 * AJAX endpoint to run search table re-indexing on demand.
	 *
	 * @return void
	 */
	public function ajax_run_reindex(): void {
		if ( ! check_ajax_referer( 'jobus_status_action', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized.', 'jobus' ) ] );
		}

		if ( class_exists( '\jobus\includes\Classes\Search_Index' ) ) {
			\jobus\includes\Classes\Search_Index::update_schema();
			$count = \jobus\includes\Classes\Search_Index::reindex_all( 200 );
			wp_send_json_success( [
				'message' => sprintf(
					/* translators: %d: number of indexed jobs */
					esc_html__( 'Search index successfully rebuilt! %d jobs indexed.', 'jobus' ),
					$count
				),
				'count'   => $count,
			] );
		}

		wp_send_json_error( [ 'message' => esc_html__( 'Search Index engine is not available.', 'jobus' ) ] );
	}

	/**
	 * Gather system report metrics into a structured array.
	 *
	 * @return array
	 */
	public static function get_system_report(): array {
		global $wpdb;

		// Database table counts.
		$table_search_index = $wpdb->prefix . 'jobus_search_index';
		$search_index_count = 0;
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_search_index ) ) === $table_search_index ) {
			$search_index_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_search_index}" );
		}

		$table_matches = $wpdb->prefix . 'jobus_job_matches';
		$matches_count = 0;
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_matches ) ) === $table_matches ) {
			$matches_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_matches}" );
		}

		// Cron schedules.
		$expiry_cron = wp_next_scheduled( 'jobus_daily_job_expiry_check' );
		$alerts_cron = wp_next_scheduled( 'jobus_daily_job_alerts_digest' );

		// Template overrides.
		$template_overrides = [];
		$theme_jobus_dir    = get_stylesheet_directory() . '/jobus';
		if ( is_dir( $theme_jobus_dir ) ) {
			$files = scandir( $theme_jobus_dir );
			if ( is_array( $files ) ) {
				foreach ( $files as $file ) {
					if ( '.' !== $file && '..' !== $file ) {
						$template_overrides[] = 'jobus/' . $file;
					}
				}
			}
		}

		return [
			'jobus_version'       => defined( 'JOBUS_VERSION' ) ? JOBUS_VERSION : 'Unknown',
			'jobus_pro_version'   => defined( 'JOBUS_PRO_VERSION' ) ? JOBUS_PRO_VERSION : esc_html__( 'Not Active', 'jobus' ),
			'wp_version'          => get_bloginfo( 'version' ),
			'php_version'         => phpversion(),
			'memory_limit'        => ini_get( 'memory_limit' ),
			'max_execution_time'  => ini_get( 'max_execution_time' ),
			'search_index_rows'   => $search_index_count,
			'matches_rows'        => $matches_count,
			'expiry_cron_next'    => $expiry_cron ? date_i18n( 'Y-m-d H:i:s', $expiry_cron ) : esc_html__( 'Not Scheduled', 'jobus' ),
			'alerts_cron_next'    => $alerts_cron ? date_i18n( 'Y-m-d H:i:s', $alerts_cron ) : esc_html__( 'Not Scheduled', 'jobus' ),
			'template_overrides'  => ! empty( $template_overrides ) ? implode( ', ', $template_overrides ) : esc_html__( 'None (using core templates)', 'jobus' ),
			'zip_archive'         => class_exists( '\ZipArchive' ) ? esc_html__( 'Available (DOCX parsing supported)', 'jobus' ) : esc_html__( 'Missing', 'jobus' ),
			'gzuncompress'        => function_exists( 'gzuncompress' ) ? esc_html__( 'Available (PDF stream decompression supported)', 'jobus' ) : esc_html__( 'Missing', 'jobus' ),
		];
	}

	/**
	 * Render System Status page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$report = self::get_system_report();
		$nonce  = wp_create_nonce( 'jobus_status_action' );

		// Format plain text markdown version for copying.
		$copy_text = "### Jobus System Status Report\n"
			. "- Jobus Core: " . $report['jobus_version'] . "\n"
			. "- Jobus Pro: " . $report['jobus_pro_version'] . "\n"
			. "- WordPress: " . $report['wp_version'] . "\n"
			. "- PHP Version: " . $report['php_version'] . "\n"
			. "- Memory Limit: " . $report['memory_limit'] . "\n"
			. "- Search Index Rows: " . $report['search_index_rows'] . "\n"
			. "- Job Matches Rows: " . $report['matches_rows'] . "\n"
			. "- Next Expiry Cron: " . $report['expiry_cron_next'] . "\n"
			. "- Next Job Alerts Cron: " . $report['alerts_cron_next'] . "\n"
			. "- Template Overrides: " . $report['template_overrides'] . "\n";
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Jobus System Status & Diagnostics', 'jobus' ); ?></h1>
			<p><?php esc_html_e( 'Review your recruitment platform environment, custom database indexes, scheduled crons, and diagnostic tools.', 'jobus' ); ?></p>

			<div class="card">
				<h2><?php esc_html_e( 'Diagnostic Actions', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Use these maintenance utilities to rebuild database caches or prepare support tickets.', 'jobus' ); ?></p>
				<p>
					<button type="button" id="jobus-btn-reindex" class="button button-primary" data-nonce="<?php echo esc_attr( $nonce ); ?>">
						<?php esc_html_e( 'Re-Index All Jobs (Flattened Search Index)', 'jobus' ); ?>
					</button>
					<button type="button" id="jobus-btn-copy-report" class="button button-secondary">
						<?php esc_html_e( 'Copy System Report for Support', 'jobus' ); ?>
					</button>
					<span id="jobus-status-feedback"></span>
				</p>
				<textarea id="jobus-system-report-text" class="large-text code" rows="6" readonly><?php echo esc_textarea( $copy_text ); ?></textarea>
			</div>

			<div class="card">
				<h2><?php esc_html_e( 'Database & Index Scaling', 'jobus' ); ?></h2>
				<table class="widefat striped">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e( 'Search Index Table (wp_jobus_search_index)', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( number_format_i18n( $report['search_index_rows'] ) ); ?> <?php esc_html_e( 'indexed jobs', 'jobus' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Candidate Matches Table (wp_jobus_job_matches)', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( number_format_i18n( $report['matches_rows'] ) ); ?> <?php esc_html_e( 'ranked match pairs', 'jobus' ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="card">
				<h2><?php esc_html_e( 'Environment & Extension Health', 'jobus' ); ?></h2>
				<table class="widefat striped">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e( 'Jobus Core Version', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['jobus_version'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Jobus Pro Version', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['jobus_pro_version'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'WordPress Version', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['wp_version'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'PHP Version', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['php_version'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Memory Limit', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['memory_limit'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Next Job Expiry Cron', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['expiry_cron_next'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Next Job Alerts Cron', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['alerts_cron_next'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Active Template Overrides', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['template_overrides'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'PDF Stream Decompressor (gzuncompress)', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['gzuncompress'] ); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Word Document Extractor (ZipArchive)', 'jobus' ); ?></strong></td>
							<td><?php echo esc_html( $report['zip_archive'] ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<?php \jobus\Admin\Feedback::render_widget(); ?>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var reindexBtn = document.getElementById('jobus-btn-reindex');
			var copyBtn = document.getElementById('jobus-btn-copy-report');
			var feedback = document.getElementById('jobus-status-feedback');
			var reportText = document.getElementById('jobus-system-report-text');

			if (copyBtn && reportText) {
				copyBtn.addEventListener('click', function() {
					reportText.select();
					document.execCommand('copy');
					copyBtn.textContent = '<?php echo esc_js( __( 'Report Copied to Clipboard!', 'jobus' ) ); ?>';
					setTimeout(function() {
						copyBtn.textContent = '<?php echo esc_js( __( 'Copy System Report for Support', 'jobus' ) ); ?>';
					}, 2500);
				});
			}

			if (reindexBtn) {
				reindexBtn.addEventListener('click', function() {
					var nonce = reindexBtn.getAttribute('data-nonce');
					reindexBtn.disabled = true;
					reindexBtn.textContent = '<?php echo esc_js( __( 'Re-indexing... Please wait', 'jobus' ) ); ?>';
					if (feedback) feedback.textContent = '';

					var formData = new FormData();
					formData.append('action', 'jobus_run_search_reindex');
					formData.append('security', nonce);

					fetch(ajaxurl, {
						method: 'POST',
						body: formData
					})
					.then(function(res) { return res.json(); })
					.then(function(res) {
						reindexBtn.disabled = false;
						reindexBtn.textContent = '<?php echo esc_js( __( 'Re-Index All Jobs (Flattened Search Index)', 'jobus' ) ); ?>';
						if (feedback) {
							feedback.textContent = res.success ? (' ' + res.data.message) : (' ' + res.data.message);
						}
					})
					.catch(function() {
						reindexBtn.disabled = false;
						reindexBtn.textContent = '<?php echo esc_js( __( 'Re-Index All Jobs (Flattened Search Index)', 'jobus' ) ); ?>';
						if (feedback) feedback.textContent = ' <?php echo esc_js( __( 'Network error occurred.', 'jobus' ) ); ?>';
					});
				});
			}
		});
		</script>
		<?php
	}
}
