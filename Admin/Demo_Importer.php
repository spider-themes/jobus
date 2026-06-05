<?php

/**
 * Demo Importer for Jobus Plugin
 * 
 * Handles the empty state UI and the automated demo data import process in a single file.
 *
 * @package Jobus\Admin
 * @since   1.8.0
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Demo_Importer class
 */
class Demo_Importer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Assets Hook
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_empty_state_assets' ] );
		
		// UI Hooks.
		add_action( 'admin_footer', [ $this, 'render_empty_state_card' ] );
		
		// AJAX Handler.
		add_action( 'wp_ajax_jobus_import_demo_data', [ $this, 'ajax_import_demo_data' ] );

		// Admin Notices.
		add_action( 'admin_notices', [ $this, 'display_import_success_notice' ] );
	}

	/**
	 * Check if we are on the Jobs, Candidates, or Companies list table and if it's empty.
	 * 
	 * @return bool
	 */
	private function is_empty_state(): bool {
		$screen = get_current_screen();
		
		$allowed_screens = [ 'edit-jobus_job' ];
		if ( function_exists( 'jobus_is_premium' ) && jobus_is_premium() ) {
			$allowed_screens[] = 'edit-jobus_candidate';
			$allowed_screens[] = 'edit-jobus_company';
		}

		if ( ! $screen || ! in_array( $screen->id, $allowed_screens, true ) ) {
			return false;
		}

		// Check if there are any posts for the current post type
		$post_type = str_replace( 'edit-', '', $screen->id );
		$counts = wp_count_posts( $post_type );
		$total  = (int) $counts->publish + (int) $counts->draft + (int) $counts->pending + (int) $counts->private;

		return $total === 0;
	}

	/**
	 * Enqueue assets only when the state is empty.
	 * 
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_empty_state_assets( $hook ): void {
		if ( ! $this->is_empty_state() ) {
			return;
		}

		// Enqueue CSS
		wp_enqueue_style( 'jobus-demo-import', JOBUS_URL . '/assets/css/demo-import.css', [], JOBUS_VERSION );

		// Enqueue JS
		wp_enqueue_script( 'jobus-demo-import', JOBUS_URL . '/assets/js/demo-import.js', [ 'jquery' ], JOBUS_VERSION, true );

		// Calculate REAL count from XML.
		$xml_file  = JOBUS_PATH . '/sample-data/demo.xml';
		$count_jobs = 0;
		$count_cands = 0;
		$count_comps = 0;
		
		if ( file_exists( $xml_file ) ) {
			$xml_content = file_get_contents( $xml_file );

			if ( preg_match_all( '/<wp:post_type>(?:<!\[CDATA\[)?jobus_job(?:\]\]>)?<\/wp:post_type>/i', $xml_content, $m ) ) {
				$count_jobs = count( $m[0] );
			}
			if ( preg_match_all( '/<wp:post_type>(?:<!\[CDATA\[)?jobus_candidate(?:\]\]>)?<\/wp:post_type>/i', $xml_content, $m ) ) {
				$count_cands = count( $m[0] );
			}
			if ( preg_match_all( '/<wp:post_type>(?:<!\[CDATA\[)?jobus_company(?:\]\]>)?<\/wp:post_type>/i', $xml_content, $m ) ) {
				$count_comps = count( $m[0] );
			}
		}

		$included_texts = [];
        $job_count_obj  = wp_count_posts( 'jobus_job' );

		// Only promise to import items if the user hasn't already created/imported them!
		if ( $count_jobs > 0 && ( ! isset( $job_count_obj->publish ) || $job_count_obj->publish == 0 ) ) {
			$included_texts[] = sprintf( __( '%d Jobs', 'jobus' ), $count_jobs );
		}

		if ( jobus_is_premium() ) {
			$cand_count_obj = wp_count_posts( 'jobus_candidate' );
			$comp_count_obj = wp_count_posts( 'jobus_company' );

			if ( $count_cands > 0 && ( ! isset( $cand_count_obj->publish ) || $cand_count_obj->publish == 0 ) ) {
				$included_texts[] = sprintf( __( '%d Candidates', 'jobus' ), $count_cands );
			}
			if ( $count_comps > 0 && ( ! isset( $comp_count_obj->publish ) || $comp_count_obj->publish == 0 ) ) {
				$included_texts[] = sprintf( __( '%d Companies', 'jobus' ), $count_comps );
			}
		}

		if ( empty( $included_texts ) ) {
			$count_text = __( 'Global Settings Update', 'jobus' );
		} else {
			$count_text = implode( ', ', $included_texts );
		}

		// Localize Script.
		wp_localize_script( 'jobus-demo-import', 'jobus_demo_import', [
			'nonce'          => wp_create_nonce( 'jobus_import_nonce' ),
			'job_count_text' => $count_text,
			'confirm_msg'    => __( 'Are you sure you want to import the demo data?', 'jobus' ),
		] );
	}

	/**
	 * Renders the custom empty state card HTML template and modal.
	 */
	public function render_empty_state_card(): void {
		if ( ! $this->is_empty_state() ) {
			return;
		}

		$screen         = get_current_screen();
		$post_type      = str_replace( 'edit-', '', $screen->id );
		$create_url     = admin_url( 'post-new.php?post_type=' . $post_type );
		$icon_url       = JOBUS_IMG . '/admin/animated-empty-state.svg';
		
		$labels = [
			'jobus_job' => [
				'not_found' => __( 'No jobs have been posted yet.', 'jobus' ),
				'create'    => __( 'Create Job', 'jobus' ),
			],
			'jobus_candidate' => [
				'not_found' => __( 'No candidates added yet.', 'jobus' ),
				'create'    => __( 'Add Candidate', 'jobus' ),
			],
			'jobus_company' => [
				'not_found' => __( 'No companies registered yet.', 'jobus' ),
				'create'    => __( 'Add Company', 'jobus' ),
			],
		];

		$not_found_text = $labels[$post_type]['not_found'] ?? __( 'No items found.', 'jobus' );
		$create_text    = $labels[$post_type]['create'] ?? __( 'Create Item', 'jobus' );
		
		?>
		<div id="jbs-empty-state-template" style="display: none;">
			<div class="jbs-empty-state-wrapper">
				<div class="jbs-empty-state-icon">
					<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php esc_attr_e( 'Empty State', 'jobus' ); ?>">
				</div>
				<div class="jbs-empty-state-content">
					<h2><?php echo esc_html( $not_found_text ); ?></h2>
					<p><?php esc_html_e( 'Start building your platform by creating a new entry manually, or instantly populate it with our premium demo data.', 'jobus' ); ?></p>
				</div>
				<div class="jbs-empty-state-actions">
					<a href="<?php echo esc_url( $create_url ); ?>" class="jbs-btn-import-ui jbs-btn-create">
						<span class="jbs-btn-icon"><span class="dashicons dashicons-plus"></span></span>
						<?php echo esc_html( $create_text ); ?>
					</a>
					<span class="jbs-or-separator"><?php esc_html_e( 'OR', 'jobus' ); ?></span>
					<button type="button" id="jbs-trigger-import" class="jbs-btn-import-ui jbs-btn-import">
						<span class="jbs-btn-icon jbs-icon-box"><span class="dashicons dashicons-download"></span></span>
						<?php esc_html_e( 'Import Demo Data', 'jobus' ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- Custom Import Modal -->
		<div id="jbs-modal-container" style="display: none;">
			<div class="jbs-modal-overlay" id="jbs-modal-popup">
				<div class="jbs-modal-card" id="jbs-modal-content">
					
					<!-- Initial State: Content -->
					<div id="jbs-modal-initial">
						<div class="jbs-modal-icon">
							<span class="dashicons dashicons-editor-help"></span>
						</div>
						<h3><?php esc_html_e( 'Import Demo Data?', 'jobus' ); ?></h3>
						<p class="jbs-modal-desc"><?php esc_html_e( 'This will import comprehensive sample jobs, taxonomies, and global settings to help you explore all Jobus features.', 'jobus' ); ?></p>
						
						<div class="jbs-modal-stats">
							<strong><?php esc_html_e( 'Includes:', 'jobus' ); ?></strong> 
							<span id="jbs-dynamic-count">...</span>
							<br>
							<strong><?php esc_html_e( 'Features:', 'jobus' ); ?></strong> 
							<?php esc_html_e( 'Full Metadata, Taxonomies, and Custom Dashboard Settings.', 'jobus' ); ?>
						</div>

						<div class="jbs-modal-footer">
							<button type="button" id="jbs-modal-yes" class="jbs-modal-btn jbs-modal-confirm">
								<?php esc_html_e( 'Yes, Import Demo Data', 'jobus' ); ?>
							</button>
							<button type="button" id="jbs-modal-no" class="jbs-modal-btn jbs-modal-cancel">
								<?php esc_html_e( 'Cancel', 'jobus' ); ?>
							</button>
						</div>
					</div>

					<!-- Step 2: Processing state -->
					<div id="jbs-modal-processing" class="jbs-modal-step">
						<div class="jbs-spinner"></div>
						<h3><?php esc_html_e( 'Importing Data...', 'jobus' ); ?></h3>
						<p id="jbs-import-status" class="jbs-status-text"><?php esc_html_e( 'Initializing environment...', 'jobus' ); ?></p>
					</div>

					<!-- Step 3: Success state -->
					<div id="jbs-modal-success" class="jbs-modal-step" style="text-align: center;">
						<div class="jbs-modal-success-icon" style="margin: 0 auto 20px; width: 68px; height: 68px; background: #e6f7ec; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1fb96a;">
							<span class="dashicons dashicons-yes" style="font-size: 36px; width: 36px; height: 36px; margin-top: -4px; margin-left: -4px;"></span>
						</div>
						<h3 style="color: #1a202c; font-size: 24px; font-weight: 700; margin-bottom: 12px;"><?php esc_html_e( "You're All Set!", 'jobus' ); ?></h3>
						<p style="color: #646970; font-size: 15px; line-height: 1.5; margin-bottom: 25px;"><?php esc_html_e( 'The demo data has been successfully imported. You can now start customizing your job board, replacing the sample content, and exploring the powerful settings of Jobus.', 'jobus' ); ?></p>
						<button type="button" class="jbs-btn-import-ui jbs-btn-import" onclick="location.reload();" style="width: 100%; border-radius: 8px; justify-content: center; height: 48px; font-size: 16px;">
							<?php esc_html_e( 'Start Exploring', 'jobus' ); ?>
						</button>
					</div>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Displays a success notice after the page reloads following a successful import.
	 */
	public function display_import_success_notice(): void {
		if ( get_transient( 'jobus_import_success' ) ) {
			delete_transient( 'jobus_import_success' );
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Demo data has been imported successfully! Your site is now ready.', 'jobus' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * AJAX Handler for importing demo data.
	 */
	public function ajax_import_demo_data(): void {
		// 1. Security Check: CSRF Prevention
		if ( ! check_ajax_referer( 'jobus_import_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		// 2. Security Check: Capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Permission denied.', 'jobus' ) ] );
		}

		// 3. Logic to process XML and Settings
		$result = $this->run_import();

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * Run the import process.
	 * 
	 * @return array Result message and status.
	 */
	private function run_import() {
		// 1. Prepare Environment
		@set_time_limit( 300 );
		@ini_set( 'memory_limit', '256M' );

		// 2. Import & Filter Global Settings first (Firewall)
		$this->apply_global_settings();

		// 3. Process XML Content.
		$xml_file = JOBUS_PATH . '/sample-data/demo.xml';

		if ( ! file_exists( $xml_file ) ) {
			return [
				'success' => false,
				'message' => esc_html__( 'Demo file (demo.xml) not found in sample-data folder.', 'jobus' ),
			];
		}

		try {
			$xml = simplexml_load_file( $xml_file, 'SimpleXMLElement', LIBXML_NOCDATA );
			
			if ( ! $xml ) {
				return [
					'success' => false,
					'message' => esc_html__( 'Failed to parse XML file.', 'jobus' )
				];
			}

			// Register XML namespaces
			$namespaces = $xml->getNamespaces( true );
			
			if ( ! isset( $xml->channel->item ) ) {
				return [
					'success' => false,
					'message' => esc_html__( 'No items found in the XML file.', 'jobus' ),
				];
			}

			$count = 0;
			foreach ( $xml->channel->item as $item ) {
				$this->process_item( $item, $namespaces );
				$count++;
			}

			// Trigger a rewrite rule flush after import
			update_option( 'jobus_flush_rewrite_rules_flag', '1' );

			// Set success transient for notice
			set_transient( 'jobus_import_success', true, 60 );

			return [
				'success' => true,
				'message' => sprintf( esc_html__( 'Successfully imported %d items!', 'jobus' ), $count )
			];

		} catch ( \Exception $e ) {
			return [
				'success' => false,
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Reads and applies global settings from settings.json with a "Firewall".
	 */
	private function apply_global_settings(): void {
		$settings_file = JOBUS_PATH . '/sample-data/settings.json';

		if ( ! file_exists( $settings_file ) ) {
			return;
		}

		$json_data     = file_get_contents( $settings_file );
		$demo_settings = json_decode( $json_data, true );

		if ( ! is_array( $demo_settings ) ) {
			return;
		}

		// Get existing user settings to prevent accidental overwrites
		$existing_settings = get_option( 'jobus_opt', [] );
		if ( ! is_array( $existing_settings ) ) {
			$existing_settings = [];
		}

		// Merge demo settings ON TOP of existing settings.
		// This ensures all the demo's job-related settings, taxonomy configs, and layouts are actually applied
		// rather than being permanently blocked by the framework's initial empty defaults.
		$settings = wp_parse_args( $demo_settings, $existing_settings );

		// --- SETTINGS FIREWALL ---
		if ( ! jobus_is_premium() ) {
			// Free Version: Force disable premium CPTs
			$settings['enable_candidate'] = '0';
			$settings['enable_company']   = '0';
			
			// Remove premium-only specifications
			unset( $settings['candidate_specifications'] );
			unset( $settings['company_specifications'] );
		} else {
			// Premium Version: Force enable premium CPTs to ensure demo works
			$settings['enable_candidate'] = '1';
			$settings['enable_company']   = '1';
			
			// If user's existing settings were completely missing the premium specs (from Free usage),
			// deliberately pull them from the demo settings to ensure the Pro features aren't blank.
			if ( empty( $existing_settings['candidate_specifications'] ) && ! empty( $demo_settings['candidate_specifications'] ) ) {
				$settings['candidate_specifications'] = $demo_settings['candidate_specifications'];
			}
			if ( empty( $existing_settings['company_specifications'] ) && ! empty( $demo_settings['company_specifications'] ) ) {
				$settings['company_specifications'] = $demo_settings['company_specifications'];
			}
		}

		// Update the global option safely
		update_option( 'jobus_opt', $settings );
	}

	/**
	 * Process a single XML item and insert it into the database.
	 * 
	 * @param \SimpleXMLElement $item       The item to process.
	 * @param array             $namespaces XML namespaces.
	 */
	private function process_item( $item, $namespaces ) {
		$wp = $item->children( $namespaces['wp'] ?? '' );
		$dc = $item->children( $namespaces['dc'] ?? '' );
		$content = $item->children( $namespaces['content'] ?? '' );

		$post_type = (string) $wp->post_type;

		// Allowlist of post types to process
		$allowed_types = [ 'jobus_job' ];
		if ( jobus_is_premium() ) {
			$allowed_types[] = 'jobus_candidate';
			$allowed_types[] = 'jobus_company';
		}

		if ( ! in_array( $post_type, $allowed_types, true ) ) {
			return;
		}

		// Prepare post data
		$post_data = [
			'post_title'   => (string) $item->title,
			'post_content' => (string) $content->encoded,
			'post_status'  => 'publish',
			'post_type'    => $post_type,
			'post_author'  => get_current_user_id(),
			'post_date'    => (string) $wp->post_date,
			'post_name'    => (string) $wp->post_name,
		];

		// Check if post already exists by slug to avoid duplicates
		$existing_post = get_page_by_path( $post_data['post_name'], OBJECT, $post_type );
		if ( $existing_post ) {
			return;
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return;
		}

		// Process Meta
		if ( isset( $wp->postmeta ) ) {
			foreach ( $wp->postmeta as $meta ) {
				$key   = (string) $meta->meta_key;
				$value = (string) $meta->meta_value;

				if ( 'jobus_meta_options' === $key ) {
					$unserialized = maybe_unserialize( $value );
					update_post_meta( $post_id, $key, $unserialized );
				} else {
					update_post_meta( $post_id, $key, maybe_unserialize( $value ) );
				}
			}
		}

		// Process Taxonomies
		if ( isset( $item->category ) ) {
			foreach ( $item->category as $cat ) {
				$taxonomy = (string) $cat['domain'];
				$term_name = (string) $cat;

				if ( $taxonomy && $term_name ) {
					wp_set_object_terms( $post_id, $term_name, $taxonomy, true );
				}
			}
		}

		// Handle Featured Image (Sideload)
		$featured_img_url = '';
		if ( isset( $wp->postmeta ) ) {
			foreach ( $wp->postmeta as $meta ) {
				if ( '_jobus_featured_image_url' === (string) $meta->meta_key ) {
					$featured_img_url = (string) $meta->meta_value;
					break;
				}
			}
		}

		if ( $featured_img_url ) {
			$attachment_id = $this->sideload_image( $featured_img_url, $post_id, (string) $item->title );
			if ( $attachment_id ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	/**
	 * Downloads a remote image and attaches it to the Media Library.
	 * 
	 * @param string $url     The remote image URL.
	 * @param int    $post_id The post ID to attach to.
	 * @param string $desc    Optional description.
	 * @return int|bool Attachment ID on success, false on failure.
	 */
	private function sideload_image( $url, $post_id, $desc = '' ) {
		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Download the file
		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return false;
		}

		$file_array = [
			'name'     => basename( $url ),
			'tmp_name' => $tmp,
		];

		// Sideload it
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// Check for errors
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return false;
		}

		return $id;
	}
}
