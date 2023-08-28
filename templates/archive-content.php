<?php
/**
 * Template Name: Job Search Results
 */
get_header();

//jobly_get_template_part('search-banner');
$args = array(
    'post_type' => 'job',
    'posts_per_page' => -1,
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    's' => get_query_var('s'),
);

$job_post = new \WP_Query($args);
$job_sidebar = is_active_sidebar('jobly_job_sidebar') ? 'col-lg-8' : 'col-lg-12';
?>

<section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
	<div class="container">
		<div class="row">


			<div class="col-xl-3 col-lg-4">
				<button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40" data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
					<i class="bi bi-funnel"></i>
					Filter
				</button>
				<div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
					<button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
					<div class="main-title fw-500 text-dark">Filter By</div>
					<div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">
						<div class="filter-block bottom-line pb-25">
							<a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseLocation" role="button" aria-expanded="false">Location</a>
							<div class="collapse show" id="collapseLocation">
								<div class="main-body">
									<select class="nice-select bg-white">
										<option value="0">Washington DC</option>
										<option value="1">California, CA</option>
										<option value="2">New York</option>
										<option value="3">Miami</option>
									</select>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->
						<div class="filter-block bottom-line pb-25 mt-25">
							<a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseJobType" role="button" aria-expanded="false">Job Type</a>
							<div class="collapse show" id="collapseJobType">
								<div class="main-body">
									<ul class="style-none filter-input">
										<li>
											<input type="checkbox" name="JobType" value="01">
											<label>Fixed-Price <span>7</span></label>
										</li>
										<li>
											<input type="checkbox" name="JobType" value="02">
											<label>Fulltime <span>3</span></label>
										</li>
										<li>
											<input type="checkbox" name="JobType" value="03">
											<label>Part-time (20hr/week) <span>0</span></label>
										</li>
										<li>
											<input type="checkbox" name="JobType" value="04">
											<label>Freelance <span>4</span></label>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->
						<div class="filter-block bottom-line pb-25 mt-25">
							<a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseExp" role="button" aria-expanded="false">Experience</a>
							<div class="collapse show" id="collapseExp">
								<div class="main-body">
									<ul class="style-none filter-input">
										<li>
											<input type="checkbox" name="Experience" value="01">
											<label>Fresher <span>5</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="02">
											<label>Intermediate <span>3</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="03">
											<label>No-Experience <span>1</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="04">
											<label>Internship <span>12</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="05">
											<label>Expert <span>17</span></label>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->
						<div class="filter-block bottom-line pb-25 mt-25">
							<a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseSalary" role="button" aria-expanded="false">Salary</a>
							<div class="collapse show" id="collapseSalary">
								<div class="main-body">
									<div class="salary-slider">
										<div class="price-input d-flex align-items-center pt-5">
											<div class="field d-flex align-items-center">
												<input type="number" class="input-min" value="0" readonly>
											</div>
											<div class="pe-1 ps-1">-</div>
											<div class="field d-flex align-items-center">
												<input type="number" class="input-max" value="300" readonly>
											</div>
											<div class="currency ps-1">USD</div>
										</div>
										<div class="slider">
											<div class="progress"></div>
										</div>
										<div class="range-input mb-10">
											<input type="range" class="range-min" min="0" max="950" value="0" step="10">
											<input type="range" class="range-max" min="0" max="1000" value="300" step="10">
										</div>
									</div>
									<ul class="style-none d-flex flex-wrap justify-content-between radio-filter mb-5">
										<li>
											<input type="radio" name="jobDuration" value="01">
											<label>Weekly</label>
										</li>
										<li>
											<input type="radio" name="jobDuration" value="02">
											<label>Monthly</label>
										</li>
										<li>
											<input type="radio" name="jobDuration" value="03">
											<label>Hourly</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->
						<div class="filter-block bottom-line pb-25 mt-25">
							<a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="false">Category</a>
							<div class="collapse" id="collapseCategory">
								<div class="main-body">
									<ul class="style-none filter-input">
										<li>
											<input type="checkbox" name="Experience" value="01">
											<label>Web Design <span>15</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="02">
											<label>Design & Creative <span>8</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="03">
											<label>It & Development <span>7</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="04">
											<label>Web & Mobile Dev <span>5</span></label>
										</li>
										<li>
											<input type="checkbox" name="Experience" value="05">
											<label>Writing <span>4</span></label>
										</li>
										<li class="hide">
											<input type="checkbox" name="Experience" value="06">
											<label>Sales & Marketing <span>25</span></label>
										</li>
										<li class="hide">
											<input type="checkbox" name="Experience" value="07">
											<label>Music & Audio <span>1</span></label>
										</li>
									</ul>
									<div class="more-btn"><i class="bi bi-plus"></i> Show More</div>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->
						<div class="filter-block bottom-line pb-25 mt-25">
							<a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseTag" role="button" aria-expanded="false">Tags</a>
							<div class="collapse" id="collapseTag">
								<div class="main-body">
									<ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
										<li>
											<input type="checkbox" name="tags" value="01">
											<label>Web Design</label>
										</li>
										<li>
											<input type="checkbox" name="tags" value="02">
											<label>Squarespace</label>
										</li>
										<li>
											<input type="checkbox" name="tags" value="03">
											<label>Layout Design</label>
										</li>
										<li>
											<input type="checkbox" name="tags" value="05">
											<label>Web Development</label>
										</li>
										<li>
											<input type="checkbox" name="tags" value="04">
											<label>React</label>
										</li>
										<li>
											<input type="checkbox" name="tags" value="06">
											<label>Full Stack</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /.filter-block -->

						<a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">Apply Filter</a>
					</div>
				</div>
				<!-- /.filter-area-tab -->
			</div>


			<div class="col-xl-9 col-lg-8">
				<div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
						<div class="total-job-found">All <span class="text-dark">7,096</span> jobs found</div>
						<div class="d-flex align-items-center">
							<div class="short-filter d-flex align-items-center">
								<div class="text-dark fw-500 me-2">Short:</div>
								<select class="nice-select">
									<option value="0">Latest</option>
									<option value="1">Category</option>
									<option value="2">Job Type</option>
								</select>
							</div>
							<button class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn" title="Active List"><i class="bi bi-list"></i></button>
							<button class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn active" title="Active Grid"><i class="bi bi-grid"></i></button>
						</div>
					</div>
					<!-- /.upper-filter -->

					<div class="accordion-box list-style show">
						<?php
						if ( $job_post->have_posts() ) {

                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();

                                jobly_get_template_part('contents/content');

                            }
                            wp_reset_postdata();

                        } else {
                            echo 'No jobs found.';
                        }
						?>

					</div>

					<div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <p class="m0 order-sm-last text-center text-sm-start xs-pb-20">Showing <span class="text-dark fw-500">1 to 20</span> of <span class="text-dark fw-500">7,096</span></p>

                        <?php jobly_pagination($job_post->max_num_pages, 'pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none'); ?>

					</div>



				</div>
				<!-- /.job-post-item-wrapper -->
			</div>
			<!-- /.col- -->
		</div>
	</div>
</section>


<?php
get_footer();