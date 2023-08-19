<?php
$meta = get_post_meta( get_the_ID(), 'jobly_meta', true );
$company_name  = !empty($meta['company_name']) ? $meta['company_name'] : '';
$company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';
$shape1 = jobly_opt('shape1' );
$shape2 = jobly_opt('shape2' );
?>

<div class="inner-banner-one position-relative">
	<div class="container">
		<div class="position-relative">
			<div class="row">
				<div class="col-xl-8 m-auto text-center">
					<div class="post-date">
                        <?php the_time(get_option('date_format')); ?> <?php esc_html_e('by', 'jobly'); ?>
                        <?php
                        if ( $company_website['url']) {
                            $target = $company_website['target'] ? 'target="' . esc_attr($company_website['target']) . '"' : '';
                            echo '<a href="' . esc_url($company_website['url']) . '" class="fw-500 text-white" '.$target.'>' . esc_html($company_name) . '</a>';
                        }
                        ?>
                    </div>
					<div class="title-two">
						<h2 class="text-white"><?php the_title() ?></h2>
					</div>
					<ul class="share-buttons d-flex flex-wrap justify-content-center style-none mt-10">
						<li>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" class="d-flex align-items-center justify-content-center">
								<i class="bi bi-facebook"></i>
								<span><?php esc_html_e( 'Facebook', 'jobly' ); ?></span>
							</a>
                        </li>
						<li>
                            <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" class="d-flex align-items-center justify-content-center">
								<i class="bi bi-twitter"></i>
								<span><?php esc_html_e( 'Twitter', 'jobly' ); ?></span>
							</a>
                        </li>
						<li>
                            <a href="javascript:void(0)" class="d-flex align-items-center justify-content-center copy-url">
								<i class="bi bi-link-45deg"></i>
								<span><?php esc_html_e('Copy', 'jobly'); ?></span>
							</a>
                        </li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<img src="<?php echo esc_url($shape1['url']) ?>" alt="<?php esc_attr_e('Shape Image', 'jobly'); ?>" class="lazy-img shapes shape_01">
	<img src="<?php echo esc_url($shape2['url']) ?>" alt="<?php esc_attr_e('Shape Image', 'jobly'); ?>" class="lazy-img shapes shape_02">
</div>