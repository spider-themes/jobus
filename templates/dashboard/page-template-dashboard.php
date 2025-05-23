<?php
/*
Template Name: Jobus Dashboard
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header('empty');
?>
	<div class="jobus-dashboard-template">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</div>
<?php
get_footer('empty');