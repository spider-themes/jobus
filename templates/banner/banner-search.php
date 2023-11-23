<?php
$meta = get_post_meta( get_the_ID(), 'jobly_meta_options', true );
$company_name  = !empty($meta['company_name']) ? $meta['company_name'] : '';
$company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';
$shape1 = jobly_opt('shape1' );
$shape2 = jobly_opt('shape2' );

$categories = get_terms(array(
    'taxonomy' => 'job_cat',
    'hide_empty' => false,
));


?>



<div class="inner-banner-one position-relative">
	<div class="container">
		<div class="position-relative">

            <div class="row">
				<div class="col-xl-6 m-auto text-center">
					<div class="title-two">
						<h2 class="text-white">Job Listing </h2>
					</div>
					<p class="text-lg text-white mt-30 lg-mt-20 mb-35 lg-mb-20">We delivered blazing fast & striking work solution</p>
				</div>
			</div>

			<div class="position-relative">
				<div class="row">
					<div class="col-xl-9 col-lg-8 m-auto">
						<div class="job-search-one position-relative">
							<form action="<?php esc_url(home_url('/')) ?>" role="search" method="get">
								<div class="row">
									
                                    <div class="col-md-5">
										<div class="input-box">
											<div class="label">What are you looking for?</div>
											<select class="nice-select lg">
												<option value="1">UI Designer</option>
												<option value="2">Content creator</option>
												<option value="3">Web Developer</option>
												<option value="4">SEO Guru</option>
												<option value="5">Digital marketer</option>
											</select>
										</div>
									</div>
                                    
									<div class="col-md-4">
										<div class="input-box border-left">
											<div class="label"><?php esc_html_e('Category', 'jobly'); ?></div>
											<select class="nice-select lg" name="job_cat">
                                                <?php
                                                foreach ( $categories as $cat ) {
                                                    echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
                                                }
                                                ?>
											</select>
										</div>
									</div>
                                    
									<div class="col-md-3">
										<button class="fw-500 text-uppercase h-100 tran3s search-btn" type="submit"><?php esc_html_e('Search', 'jobly'); ?></button>
									</div>
                                    
								</div>
							</form>
						</div>
						<!-- /.job-search-one -->
					</div>
				</div>
			</div>

		</div>
	</div>

    <img src="<?php echo esc_url($shape1['url']) ?>" alt="<?php esc_attr_e('Shape Image', 'jobly'); ?>" class="lazy-img shapes shape_01">
    <img src="<?php echo esc_url($shape2['url']) ?>" alt="<?php esc_attr_e('Shape Image', 'jobly'); ?>" class="lazy-img shapes shape_02">

</div>