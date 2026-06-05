<?php
/**
 * Pro Feature Lock — reusable upsell preview.
 *
 * Renders a blurred, non-interactive screenshot-style preview of a Pro
 * dashboard section with a lock overlay and an upgrade CTA. Used by free
 * version sections (Applications, Saved Candidates, Messages…) that have
 * a Pro counterpart.
 *
 * Expected variables (passed via Template_Loader::get_template_part):
 *
 * @var string $title       Headline for the lock overlay.
 * @var string $description Short paragraph explaining the feature.
 * @var array  $features    Optional. List of bullet-style feature labels.
 * @var string $preview     Rendered HTML for the blurred preview behind the lock.
 * @var string $upgrade_url URL the CTA button points to.
 * @var string $cta_label   Optional. CTA button label.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Strings are kept unescaped here; escaping happens at the output call sites below.
$title       = isset( $title ) ? (string) $title : __( 'Pro Feature', 'jobus' );
$description = isset( $description ) ? (string) $description : '';
$features    = isset( $features ) && is_array( $features ) ? $features : array();
$preview     = isset( $preview ) ? (string) $preview : '';
$cta_label   = ( isset( $cta_label ) && '' !== $cta_label ) ? (string) $cta_label : __( 'Upgrade to Pro', 'jobus' );

if ( ! isset( $upgrade_url ) || '' === $upgrade_url ) {
    $upgrade_url = function_exists( 'jobus_fs' ) ? jobus_fs()->get_upgrade_url() : admin_url( 'admin.php?page=jobus-pricing' );
}
?>
<div class="jbs-dashboard-pro-lock" role="region" aria-label="<?php echo esc_attr( $title ); ?>">
    <div class="jbs-dashboard-pro-lock__preview" aria-hidden="true">
        <?php
        // $preview is server-rendered markup from a sibling template — already
        // sanitized at its source. Echoing as-is preserves layout fidelity.
        echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
    </div>

    <div class="jbs-dashboard-pro-lock__overlay">
        <div class="jbs-dashboard-pro-lock__card">
            <div class="jbs-dashboard-pro-lock__icon" aria-hidden="true">
                <i class="bi bi-lock-fill"></i>
            </div>
            <span class="jbs-dashboard-pro-lock__badge"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
            <h3 class="jbs-dashboard-pro-lock__title"><?php echo esc_html( $title ); ?></h3>
            <?php if ( $description !== '' ) : ?>
                <p class="jbs-dashboard-pro-lock__desc"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>

            <?php if ( ! empty( $features ) ) : ?>
                <ul class="jbs-dashboard-pro-lock__features">
                    <?php foreach ( $features as $feature ) : ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo esc_html( $feature ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-primary jbs-dashboard-pro-lock__cta" target="_blank" rel="noopener">
                <i class="bi bi-stars"></i> <?php echo esc_html( $cta_label ); ?>
            </a>
        </div>
    </div>
</div>
