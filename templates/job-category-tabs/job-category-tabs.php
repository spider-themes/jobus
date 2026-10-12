<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Template variables passed from Blocks::job_category_tabs_block_render().
$list_id = $list_id ?? '';
$rows    = is_array( $rows ?? null ) ? $rows : [];
?>
<div class="jbs-job-category-tabs" id="<?php echo esc_attr( $list_id ); ?>">
	<?php foreach ( $rows as $row ) :
		$gradient_id = $row['panel_id'] . '-stroke';
		?>
		<div class="jbs-job-category-tabs__item">
			<button
				type="button"
				class="jbs-job-category-tabs__toggle jbs-collapsed"
				data-jbs-toggle="collapse"
				data-jbs-target="#<?php echo esc_attr( $row['panel_id'] ); ?>"
				data-jbs-parent="#<?php echo esc_attr( $list_id ); ?>"
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $row['panel_id'] ); ?>"
			>
				<span class="jbs-job-category-tabs__name">
					<?php echo esc_html( $row['name'] ); ?><span class="jbs-job-category-tabs__count"><?php echo esc_html( $row['count'] ); ?></span>
				</span>
				<span class="jbs-job-category-tabs__icon" aria-hidden="true">
					<svg class="jbs-job-category-tabs__icon-svg" viewBox="0 0 40 40" focusable="false">
						<defs>
							<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" x1="6" y1="4" x2="34" y2="36" gradientUnits="userSpaceOnUse">
								<stop stop-color="#F4A9C8"/>
								<stop offset="1" stop-color="#F0B48A"/>
							</linearGradient>
						</defs>
						<circle cx="20" cy="20" r="19" fill="#fff" stroke="url(#<?php echo esc_attr( $gradient_id ); ?>)" stroke-width="1.15"/>
						<path d="M20 13.25v13.5M13.25 20h13.5" fill="none" stroke="url(#<?php echo esc_attr( $gradient_id ); ?>)" stroke-width="1.35" stroke-linecap="round"/>
					</svg>
				</span>
			</button>
			<div id="<?php echo esc_attr( $row['panel_id'] ); ?>" class="jbs-collapse jbs-job-category-tabs__panel">
				<?php if ( ! empty( $row['jobs'] ) ) : ?>
					<div class="jbs-job-category-tabs__jobs">
						<?php foreach ( $row['jobs'] as $job ) : ?>
							<a class="jbs-job-category-tabs__job" href="<?php echo esc_url( $job['url'] ); ?>">
								<span class="jbs-job-category-tabs__location"><?php echo esc_html( $job['location'] ); ?></span>
								<span class="jbs-job-category-tabs__title"><?php echo esc_html( $job['title'] ); ?></span>
								<span class="jbs-job-category-tabs__apply">
									<?php esc_html_e( 'Apply Now', 'jobus' ); ?>
									<svg class="jbs-job-category-tabs__arrow" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
										<path d="M4 12L12 4M12 4H6.5M12 4V9.5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
