<?php
/**
 * Jobus Free vs Pro comparison page.
 *
 * @package Jobus
 * @subpackage Admin/Templates
 */

defined( 'ABSPATH' ) || exit;

$catalog     = include JOBUS_PATH . '/Admin/templates/free-vs-pro-data.php';
$categories  = isset( $catalog['categories'] ) ? $catalog['categories'] : array();
$features    = isset( $catalog['features'] ) ? $catalog['features'] : array();
$spotlight   = isset( $catalog['spotlight'] ) ? $catalog['spotlight'] : array();
$faqs        = isset( $catalog['faqs'] ) ? $catalog['faqs'] : array();
$upgrade_url = function_exists( 'jobus_fs' ) && jobus_fs() ? jobus_fs()->get_upgrade_url() : 'https://spiderthemes.net/jobus';

if ( ! function_exists( 'jobus_fvp_render_cell' ) ) {
	/**
	 * Render a Free or Pro availability cell.
	 *
	 * @param bool|string $value Feature availability.
	 * @param bool        $is_pro Whether this is the Pro column.
	 */
	function jobus_fvp_render_cell( $value, $is_pro = false ) {
		$pro_class = $is_pro ? ' jbs-val-pro' : '';

		if ( true === $value ) {
			echo '<td class="jbs-val-cell' . esc_attr( $pro_class ) . '"><span class="jbs-check" aria-label="' . esc_attr__( 'Included', 'jobus' ) . '"><span class="dashicons dashicons-yes"></span></span></td>';
			return;
		}

		if ( false === $value ) {
			echo '<td class="jbs-val-cell' . esc_attr( $pro_class ) . '"><span class="jbs-cross" aria-label="' . esc_attr__( 'Not included', 'jobus' ) . '"><span class="dashicons dashicons-minus"></span></span></td>';
			return;
		}

		$pill_class = $is_pro ? 'pill-green' : 'pill-gray';
		echo '<td class="jbs-val-cell' . esc_attr( $pro_class ) . '"><span class="jbs-text-pill ' . esc_attr( $pill_class ) . '">' . esc_html( (string) $value ) . '</span></td>';
	}
}

$total_count     = count( $features );
$free_count      = 0;
$pro_only_count  = 0;
$features_by_cat = array();

foreach ( $features as $feature ) {
	$cat = isset( $feature['cat'] ) ? $feature['cat'] : 'platform';
	if ( ! isset( $features_by_cat[ $cat ] ) ) {
		$features_by_cat[ $cat ] = array();
	}
	$features_by_cat[ $cat ][] = $feature;

	$free_val = $feature['free'] ?? false;
	$pro_val  = $feature['pro'] ?? false;
	if ( true === $free_val || is_string( $free_val ) ) {
		++$free_count;
	}
	if ( false === $free_val && ( true === $pro_val || is_string( $pro_val ) ) ) {
		++$pro_only_count;
	}
}

$free_highlights = array(
	__( 'Unlimited job listings, taxonomies, and custom fields', 'jobus' ),
	__( 'Guest applications with CV upload', 'jobus' ),
	__( 'AJAX search, filters, and radius geolocation', 'jobus' ),
	__( 'Google for Jobs schema', 'jobus' ),
	__( 'Social login (Google, Facebook, LinkedIn)', 'jobus' ),
	__( 'Elementor + Gutenberg job widgets', 'jobus' ),
	__( 'Frontend job submission for employers', 'jobus' ),
	__( 'Brand colors, email templates, and demo jobs', 'jobus' ),
);

$pro_highlights = array(
	__( 'Candidate & company directories', 'jobus' ),
	__( 'WooCommerce job packages and featured upsells', 'jobus' ),
	__( 'Smart matching engine + match emails', 'jobus' ),
	__( 'PDF / DOCX resume parser', 'jobus' ),
	__( 'Full ATS pipeline and CSV export', 'jobus' ),
	__( 'In-platform messaging', 'jobus' ),
	__( 'Job alerts, expiry, and screening questions', 'jobus' ),
	__( '7 analytics dashboards + search insights', 'jobus' ),
);
?>

<div class="wrap jbs-free-vs-pro-wrap">
	<div class="jbs-comparison-presentation">

		<div class="jbs-presentation-hero">
			<div class="jbs-hero-badge">
				<span class="dashicons dashicons-star-filled"></span>
				<?php esc_html_e( 'Free vs Pro', 'jobus' ); ?>
			</div>
			<h1 class="jbs-hero-title"><?php esc_html_e( 'See everything included in Jobus Free and Jobus Pro', 'jobus' ); ?></h1>
			<p class="jbs-hero-subtitle">
				<?php
				printf(
					/* translators: 1: total features, 2: free features, 3: pro-only features */
					esc_html__( '%1$d features compared side by side. %2$d available on Free, %3$d unlock only with Pro — so you know exactly what you get before you upgrade.', 'jobus' ),
					(int) $total_count,
					(int) $free_count,
					(int) $pro_only_count
				);
				?>
			</p>
			<div class="jbs-hero-actions">
				<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-primary jbs-btn-hero">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
				</a>
				<a href="#jbs-matrix-table" class="jbs-btn jbs-btn-secondary">
					<span class="dashicons dashicons-list-view"></span>
					<?php
					printf(
						/* translators: %d: number of features */
						esc_html__( 'Browse all %d features', 'jobus' ),
						(int) $total_count
					);
					?>
				</a>
			</div>
			<div class="jbs-hero-trust">
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e( '14-day free trial', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-shield"></span>
					<?php esc_html_e( '30-day money-back guarantee', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-database"></span>
					<?php esc_html_e( 'Keeps all existing data', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-sos"></span>
					<?php esc_html_e( 'Priority support on Pro', 'jobus' ); ?>
				</span>
			</div>
		</div>

		<div class="jbs-stat-row" aria-hidden="false">
			<div class="jbs-stat-chip">
				<strong><?php echo esc_html( (string) $total_count ); ?></strong>
				<span><?php esc_html_e( 'Features compared', 'jobus' ); ?></span>
			</div>
			<div class="jbs-stat-chip">
				<strong><?php echo esc_html( (string) $free_count ); ?></strong>
				<span><?php esc_html_e( 'On Free', 'jobus' ); ?></span>
			</div>
			<div class="jbs-stat-chip jbs-stat-chip-pro">
				<strong><?php echo esc_html( (string) $pro_only_count ); ?></strong>
				<span><?php esc_html_e( 'Pro-only unlocks', 'jobus' ); ?></span>
			</div>
			<div class="jbs-stat-chip">
				<strong><?php echo esc_html( (string) count( $categories ) ); ?></strong>
				<span><?php esc_html_e( 'Modules', 'jobus' ); ?></span>
			</div>
		</div>

		<div class="jbs-plans-grid">
			<div class="jbs-plan-card">
				<span class="jbs-plan-badge"><?php esc_html_e( 'Your current plan', 'jobus' ); ?></span>
				<div class="jbs-plan-header">
					<h3><?php esc_html_e( 'Jobus Free', 'jobus' ); ?></h3>
					<p><?php esc_html_e( 'A complete job board: unlimited listings, search, applications, schema, and page builders.', 'jobus' ); ?></p>
					<div class="jbs-plan-tier">
						<span class="jbs-tier-badge jbs-tier-free"><?php esc_html_e( 'Community', 'jobus' ); ?></span>
						<span class="jbs-tier-caption"><?php esc_html_e( 'Free forever', 'jobus' ); ?></span>
					</div>
				</div>
				<ul class="jbs-plan-features">
					<?php foreach ( $free_highlights as $item ) : ?>
						<li>
							<span class="dashicons dashicons-yes"></span>
							<span><?php echo esc_html( $item ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="jbs-plan-footer">
					<span class="jbs-btn jbs-btn-disabled">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'Active on this site', 'jobus' ); ?>
					</span>
				</div>
			</div>

			<div class="jbs-plan-card is-pro">
				<span class="jbs-plan-badge badge-pro">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Recommended', 'jobus' ); ?>
				</span>
				<div class="jbs-plan-header">
					<h3><?php esc_html_e( 'Jobus Pro', 'jobus' ); ?></h3>
					<p><?php esc_html_e( 'Everything in Free, plus monetization, matching, ATS, messaging, resume parsing, and analytics.', 'jobus' ); ?></p>
					<div class="jbs-plan-tier">
						<span class="jbs-tier-badge jbs-tier-pro"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
						<span class="jbs-tier-caption"><?php esc_html_e( 'All modules included', 'jobus' ); ?></span>
					</div>
				</div>
				<ul class="jbs-plan-features">
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><strong><?php esc_html_e( 'Everything in Free', 'jobus' ); ?></strong></span>
					</li>
					<?php foreach ( $pro_highlights as $item ) : ?>
						<li>
							<span class="dashicons dashicons-star-filled"></span>
							<span><?php echo esc_html( $item ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="jbs-plan-footer">
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-upgrade">
						<span class="dashicons dashicons-star-filled"></span>
						<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
					</a>
					<p class="jbs-plan-guarantee">
						<span class="dashicons dashicons-shield"></span>
						<?php esc_html_e( '14-day trial · Cancel anytime', 'jobus' ); ?>
					</p>
				</div>
			</div>
		</div>

		<div class="jbs-spotlight-section">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'Pro modules', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'What Pro adds on top of Free', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'These are the modules that turn a job listing site into a recruitment product you can run and monetize.', 'jobus' ); ?></p>
			</div>
			<div class="jbs-features-grid">
				<?php foreach ( $spotlight as $card ) : ?>
					<div class="jbs-feature-card">
						<div class="jbs-feature-icon">
							<span class="dashicons <?php echo esc_attr( $card['icon'] ); ?>"></span>
						</div>
						<h4><?php echo esc_html( $card['title'] ); ?></h4>
						<p><?php echo esc_html( $card['desc'] ); ?></p>
						<span class="jbs-feature-pill"><?php echo esc_html( $card['pill'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="jbs-matrix-section" id="jbs-matrix-table">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'Full comparison', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'Every feature, Free vs Pro', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Filter by module or search. A green check is included. A dash is not. A label means a limited or theme-unlocked version.', 'jobus' ); ?></p>
			</div>

			<div class="jbs-matrix-toolbar">
				<div class="jbs-matrix-filters" role="tablist">
					<button type="button" class="jbs-matrix-filter-btn is-active" data-category="all">
						<?php
						printf(
							/* translators: %d: number of features */
							esc_html__( 'All (%d)', 'jobus' ),
							(int) $total_count
						);
						?>
					</button>
					<?php foreach ( $categories as $cat_key => $cat ) : ?>
						<?php
						$cat_count = isset( $features_by_cat[ $cat_key ] ) ? count( $features_by_cat[ $cat_key ] ) : 0;
						if ( ! $cat_count ) {
							continue;
						}
						?>
						<button type="button" class="jbs-matrix-filter-btn" data-category="<?php echo esc_attr( $cat_key ); ?>">
							<?php echo esc_html( $cat['label'] ); ?>
							<span class="jbs-filter-count"><?php echo esc_html( (string) $cat_count ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="jbs-matrix-search">
					<span class="dashicons dashicons-search"></span>
					<input type="search" id="jbs-matrix-search" placeholder="<?php esc_attr_e( 'Search features…', 'jobus' ); ?>" autocomplete="off" />
				</div>
			</div>

			<div class="jbs-legend">
				<span><span class="jbs-check"><span class="dashicons dashicons-yes"></span></span> <?php esc_html_e( 'Included', 'jobus' ); ?></span>
				<span><span class="jbs-cross"><span class="dashicons dashicons-minus"></span></span> <?php esc_html_e( 'Not included', 'jobus' ); ?></span>
				<span><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Limited / Jobi theme', 'jobus' ); ?></span></span>
				<span><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Pro extra', 'jobus' ); ?></span></span>
			</div>

			<div class="jbs-table-wrap">
				<table class="jbs-matrix-table">
					<thead>
						<tr>
							<th class="col-feature"><?php esc_html_e( 'Feature', 'jobus' ); ?></th>
							<th class="col-plan"><?php esc_html_e( 'Free', 'jobus' ); ?></th>
							<th class="col-plan col-pro"><?php esc_html_e( 'Pro', 'jobus' ); ?></th>
						</tr>
					</thead>

					<?php foreach ( $categories as $cat_key => $cat ) : ?>
						<?php
						if ( empty( $features_by_cat[ $cat_key ] ) ) {
							continue;
						}
						?>
						<tbody class="jbs-matrix-category-group" data-category="<?php echo esc_attr( $cat_key ); ?>">
							<tr class="jbs-matrix-category-header">
								<td colspan="3">
									<span class="cat-icon dashicons <?php echo esc_attr( $cat['icon'] ); ?>"></span>
									<?php echo esc_html( $cat['title'] ); ?>
									<span class="jbs-cat-count"><?php echo esc_html( (string) count( $features_by_cat[ $cat_key ] ) ); ?></span>
								</td>
							</tr>
							<?php foreach ( $features_by_cat[ $cat_key ] as $row ) : ?>
								<tr class="jbs-feature-row">
									<td class="jbs-feature-info">
										<span class="jbs-feature-name"><?php echo esc_html( $row['name'] ); ?></span>
										<span class="jbs-feature-desc"><?php echo esc_html( $row['desc'] ); ?></span>
									</td>
									<?php jobus_fvp_render_cell( $row['free'] ?? false, false ); ?>
									<?php jobus_fvp_render_cell( $row['pro'] ?? false, true ); ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					<?php endforeach; ?>
				</table>
				<div class="jbs-matrix-no-results">
					<p><?php esc_html_e( 'No matching features. Try another search or choose All.', 'jobus' ); ?></p>
				</div>
			</div>
		</div>

		<div class="jbs-faq-section">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'FAQ', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'Before you upgrade', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Licensing, data, WooCommerce, and what stays after a downgrade.', 'jobus' ); ?></p>
			</div>
			<div class="jbs-faq-list">
				<?php foreach ( $faqs as $index => $faq ) : ?>
					<div class="jbs-faq-item<?php echo 0 === (int) $index ? ' is-open' : ''; ?>">
						<button type="button" class="jbs-faq-question">
							<span><?php echo esc_html( $faq['q'] ); ?></span>
							<span class="dashicons dashicons-arrow-down-alt2"></span>
						</button>
						<div class="jbs-faq-answer">
							<p><?php echo esc_html( $faq['a'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="jbs-final-cta-section">
			<div class="jbs-cta-card">
				<div class="jbs-cta-content">
					<h2><?php esc_html_e( 'Ready to unlock Jobus Pro?', 'jobus' ); ?></h2>
					<p><?php esc_html_e( 'Start a 14-day trial. Matching, ATS, messaging, packages, and analytics activate on this site without touching your existing jobs.', 'jobus' ); ?></p>
					<div class="jbs-cta-buttons">
						<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-primary jbs-btn-cta">
							<span class="dashicons dashicons-star-filled"></span>
							<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
						</a>
					</div>
					<div class="jbs-cta-note">
						<span><span class="dashicons dashicons-yes"></span><?php esc_html_e( '14-day free trial', 'jobus' ); ?></span>
						<span><span class="dashicons dashicons-shield"></span><?php esc_html_e( '30-day money-back', 'jobus' ); ?></span>
						<span><span class="dashicons dashicons-database"></span><?php esc_html_e( 'Keep 100% of your data', 'jobus' ); ?></span>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
