<?php
get_header();
jobly_get_template_part( 'banner/banner-single' );

$meta = get_post_meta(get_the_ID(), 'jobly_meta', true);
$company_logo = !empty($meta['company_logo']) ? $meta['company_logo'] : '';
$company_name = !empty($meta['company_name']) ? $meta['company_name'] : '';
$company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';

$args = array(
	'post_type' => 'job',
	'posts_per_page' => 2,
	'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
	'orderby' => 'date',
	'order' => 'DESC',
	'post_status' => 'publish',
	's' => get_query_var('s'),
);

$job_post = new \WP_Query($args);



function jobly_disply_job_specification () {

    $meta = get_post_meta(get_the_ID(), 'jobly_meta', true);




}


$specifications = !empty($meta['specifications']) ? $meta['specifications'] : [];






$job_types = !empty($meta['job-type']) ? $meta['job-type'] : [];

foreach ( $job_types as $job_type ) {
    echo $job_type;
}
?>


	<section class="job-details pt-100 lg-pt-80 pb-130 lg-pb-80">
		<div class="container">
			<div class="row">
				<div class="col-xxl-9 col-xl-8">
					<div class="details-post-data me-xxl-5 pe-xxl-4">

                        <?php the_content(); ?>

					</div>
					<!-- /.details-post-data -->
				</div>


				<div class="col-xxl-3 col-xl-4">

                    <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mt-50">
                        <?php
                        if ( $company_logo['id'] ) {
                            echo wp_get_attachment_image($company_logo['id'], 'full', '', [ 'class' => 'lazy-img m-auto logo'] );
                        }
                        if ( $company_name ) {
                            echo '<div class="text-md text-dark text-center mt-15 mb-20">' . esc_html($company_name) . '</div>';
                        }
                        if ( $company_website['url'] ) {
                            $target = $company_website['target'] ? 'target="' . esc_attr($company_website['target']) . '"' : '';
                            echo '<a href="' . esc_url($company_website['url']) . '" class="website-btn tran3s" '.$target.'>' . esc_html($company_website['text']) . '</a>';
                        }
                        ?>

						<div class="border-top mt-40 pt-40">
							<ul class="job-meta-data row style-none">
								<li class="col-xl-7 col-md-4 col-sm-6">
									<span>Salary</span>
									<div>50k-60k/year</div>
								</li>
								<li class="col-xl-5 col-md-4 col-sm-6">
									<span>Expertise</span>
									<div>Intermediate</div>
								</li>
								<li class="col-xl-7 col-md-4 col-sm-6">
									<span>Location</span>
									<div>Spain, Barcelona</div>
								</li>
								<li class="col-xl-5 col-md-4 col-sm-6">
									<span>Job Type</span>
                                    <?php
                                    foreach ($job_types as $job_type) {
                                        echo '<div>' . esc_html($job_type) . '</div>';
                                    }
                                    ?>
								</li>
								<li class="col-xl-7 col-md-4 col-sm-6">
									<span>Date</span>
									<div>12 jun, 2022  </div>
								</li>
								<li class="col-xl-5 col-md-4 col-sm-6">
									<span>Experience</span>
									<div>2 Years</div>
								</li>
							</ul>

							<div class="job-tags d-flex flex-wrap pt-15">
								<a href="#">Design</a>
								<a href="#">Product Design</a>
								<a href="#">Brands</a>
								<a href="#">Application</a>
								<a href="#">UI/UX</a>
							</div>
							<a href="#" class="btn-one w-100 mt-25">Apply Now</a>
						</div>
					</div>
					<!-- /.job-company-info -->
				</div>


			</div>
		</div>
	</section>

<?php
get_footer();


