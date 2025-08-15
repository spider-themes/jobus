<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$logo = jobus_opt( 'dashboard_logo' );

if ( ! empty( $logo['url'] ) ) : ?>
	<div class="logo text-md-center d-md-block d-flex align-items-center justify-content-between">
		<a href="<?php esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( $logo['url'] ) ?>" alt="<?php get_bloginfo( 'name' ) ?>">
		</a>
		<button class="close-btn d-block d-md-none"><i class="bi bi-x-lg"></i></button>
	</div>
	<?php
endif;