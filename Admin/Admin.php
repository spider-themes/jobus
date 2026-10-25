<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin class for Jobus plugin
 *
 * Adds custom classes to the WordPress admin body tag based on the active theme and Jobus-specific options.
 *
 * @package Jobus\Admin
 */
class Admin {
	/**
	 * Admin constructor.
	 * Adds the body_class filter for admin area.
	 */
	function __construct() {
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_action( 'admin_menu', [ $this, 'create_nested_submenus' ] );
		add_filter( 'parent_file', [ $this, 'active_top_level_menu' ] );
		add_filter( 'submenu_file', [ $this, 'active_sub_menus' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_meta_links' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'setup_radius_engine' ] );
		add_action( 'admin_init', [ $this, 'setup_matching_engine' ] );
		add_action( 'admin_notices', [ $this, 'radius_sync_notice' ] );
		add_action( 'admin_notices', [ $this, 'matching_sync_notice' ] );
		add_action( 'admin_notices', [ $this, 'version_update_notice' ] );
	}

	/**
	 * Adds custom classes to the admin body tag for Jobi theme.
	 *
	 * @param string $classes Existing body classes.
	 *
	 * @return string Modified body classes.
	 */
	public function body_class( string $classes ): string {
		$current_theme = get_template();
		$classes      .= ' ' . $current_theme;

		// Example: Add premium/pro class if needed for Jobus (update logic as needed)
		if ( function_exists('jobus_fs') && jobus_fs()->is_paying_or_trial() ) {
			$classes .= ' jbs-premium';
		}

		return $classes;
	}

	/**
	 * Setup Utility: Force Database Table Creation & Backfill Locations.
	 * Triggered by navigating to wp-admin/edit.php?post_type=jobus_job&jobus_setup_radius=true
	 *
	 * @return void
	 */
	public function setup_radius_engine(): void {
		if ( ! isset( $_GET['jobus_setup_radius'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Verify nonce for security.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'jobus_setup_radius_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'jobus' ) );
		}

		// Force Setup the required structural DB table
		if ( class_exists( '\jobus\includes\Classes\Lifecycle' ) ) {
			\jobus\includes\Classes\Lifecycle::create_tables();
		}

		// Setup / Backfill all existing Jobs with Coordinates
		if ( class_exists( '\jobus\includes\Classes\Geolocation' ) ) {
			$geo_engine = new \jobus\includes\Classes\Geolocation();

			$jobs = get_posts(
				[
					'post_type'      => 'jobus_job',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				]
			);

			$synced_count = 0;
			if ( ! empty( $jobs ) && ! is_wp_error( $jobs ) ) {
				foreach ( $jobs as $job_id ) {
					$post = get_post( $job_id );
					if ( $post ) {
						$geo_engine->sync_job_location( $job_id, $post );
						$synced_count++;
					}
				}
			}

			update_option( 'jobus_radius_setup_completed', true );
			
			// Output success and redirect securely
			$redirect_url = remove_query_arg( [ 'jobus_setup_radius', '_wpnonce' ] );
			$redirect_url = add_query_arg( [ 'jobus_radius_synced' => $synced_count ], $redirect_url );
			
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Flash an admin notice upon successful radius search setup.
	 */
	public function radius_sync_notice(): void {
		if ( isset( $_GET['jobus_radius_synced'] ) && current_user_can( 'manage_options' ) ) {
			$count   = absint( $_GET['jobus_radius_synced'] );
			$message = sprintf( 
				/* translators: %d: number of jobs synced */
				esc_html__( 'Radius Search Setup Complete. Database Table created and synced %d jobs with geolocation coordinates.', 'jobus' ), 
				$count 
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
		}
	}

	/**
	 * Create matching tables and sync candidate coordinates (requires Jobus Pro).
	 *
	 * @return void
	 */
	public function setup_matching_engine(): void {
		if ( ! isset( $_GET['jobus_setup_matching'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'jobus_setup_matching_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'jobus' ) );
		}

		$redirect_url = remove_query_arg( array( 'jobus_setup_matching', '_wpnonce' ) );

		if ( ! class_exists( '\Jobus_Pro\Matching\Candidate_Geo_Query' ) || ! class_exists( '\Jobus_Pro\Matching\Schema' ) ) {
			$redirect_url = add_query_arg( array( 'jobus_matching_error' => 'pro_required' ), $redirect_url );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		if ( class_exists( '\jobus\includes\Classes\Lifecycle' ) ) {
			\jobus\includes\Classes\Lifecycle::create_tables();
		}
		\Jobus_Pro\Matching\Schema::create_tables();

		$synced = 0;
		if ( class_exists( '\jobus\includes\Classes\Geolocation' ) ) {
			$geo = new \jobus\includes\Classes\Geolocation();
			$candidates = get_posts(
				array(
					'post_type'      => 'jobus_candidate',
					'posts_per_page' => -1,
					'post_status'    => 'any',
					'fields'         => 'ids',
				)
			);

			foreach ( $candidates as $candidate_id ) {
				$post = get_post( $candidate_id );
				if ( $post && $geo->sync_candidate_location( $candidate_id, $post ) ) {
					$synced++;
				}
			}
		}

		update_option( 'jobus_matching_setup_completed', true );

		$redirect_url = add_query_arg( array( 'jobus_matching_synced' => $synced ), $redirect_url );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Admin notice after matching setup.
	 *
	 * @return void
	 */
	public function matching_sync_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['jobus_matching_error'] ) && 'pro_required' === $_GET['jobus_matching_error'] ) {
			echo '<div class="notice notice-error is-dismissible"><p>'
				. esc_html__( 'Candidate matching sync requires Jobus Pro. Activate Jobus Pro, then try again.', 'jobus' )
				. '</p></div>';
			return;
		}

		if ( isset( $_GET['jobus_matching_synced'] ) ) {
			$count   = absint( $_GET['jobus_matching_synced'] );
			$message = sprintf(
				/* translators: %d: number of candidates synced */
				esc_html__( 'Matching setup complete. Tables created and %d candidate locations synced.', 'jobus' ),
				$count
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	public function create_nested_submenus() {
		// === COMPANY PAGES ===
		if ( jobus_opt('enable_company') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company', 'jobus' ), __( 'Company', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_company&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company' );
			
			// Add divider bottom if Candidate is not enabled
			$divider_bottom = jobus_opt('enable_candidate') != true ? '&has_jbs_divider_bottom=true' : '';
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company' . $divider_bottom );
		}

		// === CANDIDATE PAGES ===
		if ( jobus_opt('enable_candidate') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate', 'jobus' ), __( 'Candidate', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_candidate&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Skills', 'jobus' ), '&nbsp; Skills', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate&has_jbs_divider_bottom=true' );
		}

		// === FREE VS PRO COMPARISON PAGE ===
		add_submenu_page(
			'edit.php?post_type=jobus_job',
			__( 'Free vs Pro', 'jobus' ),
			__( 'Free vs Pro', 'jobus' ),
			'manage_options',
			'jobus-free-vs-pro',
			[ $this, 'render_free_vs_pro_page' ]
		);
	}

	/**
	 * Highlight the correct parent menu item for post types and taxonomies.
	 *
	 * @param string $parent_file The current parent file.
	 * @return string Modified parent file.
	 */
	public function active_top_level_menu( $parent_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $parent_file;

		// Keep the Jobus top menu expanded for all Jobus post types and taxonomies.
		if (
			in_array( $screen->post_type, [ 'jobus_job', 'jobus_company', 'jobus_candidate' ] ) ||
			in_array( $screen->taxonomy, [
				'jobus_company_cat', 'jobus_company_location',
				'jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'
			]) ||
			( isset( $_GET['page'] ) && sanitize_key( $_GET['page'] ) === 'jobus-social-login' )
		) {
			$parent_file = 'edit.php?post_type=jobus_job';
		}

		return $parent_file;
	}

	/**
	 * Highlight the correct submenu item for post types and taxonomies.
	 *
	 * @param string $submenu_file The current submenu file.
	 * @return string Modified submenu file.
	 */
	public function active_sub_menus( $submenu_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $submenu_file;

		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
		$taxonomy  = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';

		// ===== Company menu =====
		if ( $post_type === 'jobus_company' || in_array( $taxonomy, ['jobus_company_cat','jobus_company_location'] ) ) {
			if ( $taxonomy === 'jobus_company_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company';
			} elseif ( $taxonomy === 'jobus_company_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_company&has_jbs_divider=true';
			}
		}

		// ===== Candidate menu =====
		if ( $post_type === 'jobus_candidate' || in_array( $taxonomy, ['jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'] ) ) {
			if ( $taxonomy === 'jobus_candidate_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_skill' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_candidate&has_jbs_divider=true';
			}
		}

		return $submenu_file;
	}


	/**
	 * Add custom meta links to the plugin row in the plugins list.
	 *
	 * @param array  $links Existing plugin row meta links.
	 * @param string $file  The plugin file path.
	 *
	 * @return array Modified plugin row meta links.
	 */
	public function add_meta_links( array $links, string $file ): array {

		if ( JOBUS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$links[] = '<a href="https://helpdesk.spiderthemes.net/docs/jobus-wordpress-plugin/" target="_blank">'.esc_html__( 'Docs & FAQs', 'jobus' ).'</a>';

		return $links;
	}

	/**
	 * Check for major updates and display a friendly release update notice.
	 *
	 * @return void
	 */
	public function version_update_notice(): void {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$dismissed = get_option( 'jobus_dismissed_update_notice_' . JOBUS_VERSION );
		if ( $dismissed ) {
			return;
		}

		// Only show on Jobus screens
		$screen = get_current_screen();
		if ( ! $screen || false === strpos( $screen->id, 'jobus' ) ) {
			return;
		}

		// If notice dismissed via GET
		if ( isset( $_GET['jobus_dismiss_update_notice'] ) && check_admin_referer( 'jobus_dismiss_update' ) ) {
			update_option( 'jobus_dismissed_update_notice_' . JOBUS_VERSION, 1 );
			return;
		}

		$dismiss_url = wp_nonce_url( add_query_arg( 'jobus_dismiss_update_notice', '1' ), 'jobus_dismiss_update' );
		?>
		<div class="notice notice-info is-dismissible" style="border-left-color: #31795A; padding: 12px 16px;">
			<p style="margin: 0 0 6px;">
				<strong style="color: #31795A;">
					<span class="dashicons dashicons-megaphone" style="vertical-align: middle; margin-right: 4px;"></span>
					<?php printf( esc_html__( 'Jobus v%s is Active!', 'jobus' ), esc_html( JOBUS_VERSION ) ); ?>
				</strong>
			</p>
			<p style="margin: 0; color: #475569; font-size: 13px;">
				<?php esc_html_e( 'Enjoy improved theme styling compatibility, dark mode support, and seamless first-run setup. Make sure to keep your installation updated for maximum performance.', 'jobus' ); ?>
				<a href="<?php echo esc_url( $dismiss_url ); ?>" style="margin-left: 8px; color: #31795A; text-decoration: underline;">
					<?php esc_html_e( 'Dismiss', 'jobus' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Render Free vs Pro comparison screen.
	 *
	 * @return void
	 */
	public function render_free_vs_pro_page(): void {
		$upgrade_url = function_exists( 'jobus_fs' ) ? jobus_fs()->get_upgrade_url() : 'https://spiderthemes.net/jobus';
		?>
		<div class="wrap jbs-free-vs-pro-wrap" style="max-width: 1000px; margin: 25px auto;">
			<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
				<div style="text-align: center; margin-bottom: 30px;">
					<span style="background: #e6f7ec; color: #31795A; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 12px; border-radius: 9999px;">
						<?php esc_html_e( 'Feature Matrix', 'jobus' ); ?>
					</span>
					<h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 12px 0 8px;">
						<?php esc_html_e( 'Jobus Free vs. Jobus Pro', 'jobus' ); ?>
					</h1>
					<p style="font-size: 15px; color: #64748b; max-width: 600px; margin: 0 auto;">
						<?php esc_html_e( 'Compare features and choose the best plan for your job portal. Upgrade anytime with a 14-day free trial or 30-day money-back guarantee.', 'jobus' ); ?>
					</p>
				</div>

				<table class="widefat striped" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
					<thead>
						<tr style="background: #f8fafc;">
							<th style="padding: 16px; font-size: 15px; width: 50%;"><?php esc_html_e( 'Feature', 'jobus' ); ?></th>
							<th style="padding: 16px; font-size: 15px; text-align: center; width: 25%;"><?php esc_html_e( 'Free (Active)', 'jobus' ); ?></th>
							<th style="padding: 16px; font-size: 15px; text-align: center; width: 25%; color: #31795A;"><?php esc_html_e( 'Jobus Pro', 'jobus' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 14px 16px;"><strong><?php esc_html_e( 'Job Board & Public Archive Listings', 'jobus' ); ?></strong></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;"><strong><?php esc_html_e( 'Candidate & Company Profiles', 'jobus' ); ?></strong></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;"><strong><?php esc_html_e( 'Faceted Radius & Geolocation Search', 'jobus' ); ?></strong></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;"><strong><?php esc_html_e( 'One-Click Demo Content Importer', 'jobus' ); ?></strong></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'WooCommerce Monetization & Job Packages', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Charge employers for listings, package subscriptions, and featured jobs with instant credits.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'Smart Candidate-Job Matching Engine', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Multi-factor match scoring by availability, travel distance, subjects, and teaching level.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'Automated Match Alert Emails', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Instantly notify matched candidates the moment an employer publishes a relevant job.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'PDF & DOCX Resume Parser', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Extract names, emails, phones, and matching skills directly from uploaded candidate CVs.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'Job Screening Questions', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Add custom pre-screening questions (text & yes/no) to filter out unqualified applicants.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'Direct In-Platform Messaging', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Real-time threaded chat between employers and candidates directly from their dashboard.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( '7-Dimension Recruitment Analytics', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Interactive Chart.js visualizers, conversion funnels, location demand heatmaps, and search logs.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; font-size: 12px; color: #64748b;"><?php esc_html_e( 'Basic Counters', 'jobus' ); ?></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td style="padding: 14px 16px;">
								<strong><?php esc_html_e( 'Applicant CSV / Excel Export', 'jobus' ); ?></strong>
								<div style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Download and sync applicant data with external HR and payroll systems.', 'jobus' ); ?></div>
							</td>
							<td style="text-align: center; color: #94a3b8;"><span class="dashicons dashicons-no-alt"></span></td>
							<td style="text-align: center; color: #16a34a;"><span class="dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>
				</table>

				<div style="margin-top: 25px; text-align: center;">
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="button button-primary button-hero" style="background: #31795A; border-color: #244034; box-shadow: none; font-size: 16px; padding: 6px 32px;">
						<span class="dashicons dashicons-unlock" style="vertical-align: middle; margin-right: 6px;"></span>
						<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
					</a>
					<p style="font-size: 13px; color: #94a3b8; margin-top: 10px;">
						<?php esc_html_e( '* Prices exclude local VAT / sales tax where applicable, calculated automatically at checkout based on your country.', 'jobus' ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

}
