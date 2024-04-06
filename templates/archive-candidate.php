<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobly
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$args = [
    'post_type' => 'candidate',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
];

?>



    <section class="candidates-profile pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
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
                                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseSemploye" role="button" aria-expanded="false">Name or Keyword</a>
                                <div class="collapse show" id="collapseSemploye">
                                    <div class="main-body">
                                        <form action="#" class="input-box position-relative">
                                            <input type="text" placeholder="Name or keyword">
                                            <button><i class="bi bi-search"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="false">Category</a>
                                <div class="collapse show" id="collapseCategory">
                                    <div class="main-body">
                                        <select class="nice-select">
                                            <option value="0">Web Design</option>
                                            <option value="1">Design & Creative </option>
                                            <option value="2">It & Development</option>
                                            <option value="3">Web & Mobile Dev</option>
                                            <option value="4">Writing</option>
                                            <option value="5">Sales & Marketing</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse" href="#collapseLocation" role="button" aria-expanded="false">Location</a>
                                <div class="collapse show" id="collapseLocation">
                                    <div class="main-body">
                                        <select class="nice-select bg-white">
                                            <option value="0">All Location</option>
                                            <option value="1">California, CA</option>
                                            <option value="2">New York</option>
                                            <option value="3">Miami</option>
                                        </select>
                                        <div class="loccation-range-select mt-5">
                                            <div class="d-flex align-items-center">
                                                <span>Radius: &nbsp;</span>
                                                <div id="rangeValue">50</div>
                                                <span>&nbsp;miles</span>
                                            </div>
                                            <input type="range" id="locationRange" value="50" max="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseExp" role="button" aria-expanded="false">Expert Level</a>
                                <div class="collapse" id="collapseExp">
                                    <div class="main-body">
                                        <ul class="style-none filter-input">
                                            <li>
                                                <input type="checkbox" name="Experience" value="02">
                                                <label>Intermediate</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Experience" value="03">
                                                <label>No-Experience</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Experience" value="04">
                                                <label>Internship</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Experience" value="05">
                                                <label>Expert</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseQualification" role="button" aria-expanded="false">Qualification</a>
                                <div class="collapse" id="collapseQualification">
                                    <div class="main-body">
                                        <ul class="style-none filter-input">
                                            <li>
                                                <input type="checkbox" name="Qualification" value="01">
                                                <label>Master’s Degree</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Qualification" value="02">
                                                <label>Bachelor Degree</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Qualification" value="03">
                                                <label>None</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseCType" role="button" aria-expanded="false">Candidate Type</a>
                                <div class="collapse" id="collapseCType">
                                    <div class="main-body">
                                        <ul class="style-none filter-input">
                                            <li>
                                                <input type="checkbox" name="Gender" value="01">
                                                <label>Male</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="Gender" value="02">
                                                <label>Female</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.filter-block -->

                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseSalary" role="button" aria-expanded="false">Salary Range</a>
                                <div class="collapse" id="collapseSalary">
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
                                <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseFluency" role="button" aria-expanded="false">English Fluency</a>
                                <div class="collapse" id="collapseFluency">
                                    <div class="main-body">
                                        <select class="nice-select">
                                            <option value="0">Basic</option>
                                            <option value="1">Conversational</option>
                                            <option value="2" selected>Fluent</option>
                                            <option value="3">Native/Bilingual</option>
                                        </select>
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
                    <div class="ms-xxl-5 ms-xl-3">

                        <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                            <div class="total-job-found">All <span class="text-dark fw-500">1,270</span> candidates found</div>
                            <div class="d-flex align-items-center">
                                <div class="short-filter d-flex align-items-center">
                                    <div class="text-dark fw-500 me-2">Short:</div>
                                    <select class="nice-select">
                                        <option value="0">Latest</option>
                                        <option value="1">Category</option>
                                        <option value="2">Job Type</option>
                                    </select>
                                </div>
                                <button class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn active" title="Active List"><i class="bi bi-list"></i></button>
                                <button class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn" title="Active Grid"><i class="bi bi-grid"></i></button>
                            </div>
                        </div>
                        <!-- /.upper-filter -->

                        <div class="accordion-box grid-style show">
                            <div class="row">

                                <div class="col-xxl-4 col-sm-6 d-flex">

                                    <div class="candidate-profile-card favourite text-center grid-layout mb-25">

                                        <a href="candidate-profile-v1.html" class="save-btn tran3s"><i class="bi bi-heart"></i></a>

                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="cadidate-avatar online position-relative d-block m-auto">
                                                <a href="<?php the_permalink() ?>" class="rounded-circle">
                                                    <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                                                </a>
                                            </div>
                                        <?php endif ?>

                                        <h4 class="candidate-name mt-15 mb-0">
                                            <a href="<?php the_permalink() ?>" class="tran3s">
                                                <?php the_title() ?>
                                            </a>
                                        </h4>

                                        <?php

                                        echo '<pre>';
                                        print_r(jobly_get_specs('candidate_specifications'));
                                        echo '</pre>';

                                        if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                            ?>
                                            <div class="candidate-post"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1') ?></div>
                                            <?php
                                        }
                                        ?>

                                        <ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">
                                            <li>Design</li>
                                            <li>UI</li>
                                            <li>Digital</li>
                                            <li class="more">2+</li>
                                        </ul>

                                        <div class="row gx-1">
                                            <div class="col-md-6">
                                                <div class="candidate-info mt-10">
                                                    <span>Salary</span>
                                                    <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_3') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="candidate-info mt-10">
                                                    <span>Location</span>
                                                    <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_4') ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row gx-2 pt-25 sm-pt-10">

                                            <div class="col-md-6">
                                                <a href="<?php the_permalink() ?>" class="profile-btn tran3s w-100 mt-5">
                                                    <?php esc_html_e('View Profile', 'jobly') ?>
                                                </a>
                                            </div>

                                            <div class="col-md-6">
                                                <a href="candidate-profile-v1.html" class="msg-btn tran3s w-100 mt-5">
                                                    <?php esc_html_e('Message', 'jobly') ?>
                                                </a>
                                            </div>

                                        </div>

                                    </div>


                                </div>




                            </div>
                        </div>
                        <!-- /.accordion-box -->

                        <div class="accordion-box list-style d-none">
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_01.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Julia Ark</a></h4>
                                                    <div class="candidate-post">Graphic Designer</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>California, US</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_03.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Shofie Ana</a></h4>
                                                    <div class="candidate-post">Artist</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>New York, US</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_02.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Riad Mahfuz</a></h4>
                                                    <div class="candidate-post">Telemarketing & Sales</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>Manchester, UK</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_03.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Julia Ark</a></h4>
                                                    <div class="candidate-post">Graphic Designer</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>Milan, Italy</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_04.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Jannat Ka</a></h4>
                                                    <div class="candidate-post">Marketing Expert</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>California, US</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_05.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Mahmud Amin</a></h4>
                                                    <div class="candidate-post">App Designer</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>Bangalore, IN</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_06.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Zubayer Hasan</a></h4>
                                                    <div class="candidate-post">Graphic Designer</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>London, UK</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_07.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Maria Henna</a></h4>
                                                    <div class="candidate-post">Designer</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>Washington, US</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card favourite list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_08.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Sakil Islam</a></h4>
                                                    <div class="candidate-post">Marketing Expert</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$30k-$50k/yr</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>Dubai, UAE</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                            <div class="candidate-profile-card list-layout mb-25">
                                <div class="d-flex">
                                    <div class="cadidate-avatar online position-relative d-block me-auto ms-auto"><a href="candidate-profile-v2.html" class="rounded-circle"><img src="images/lazy.svg" data-src="images/candidates/img_09.jpg" alt="" class="lazy-img rounded-circle"></a></div>
                                    <div class="right-side">
                                        <div class="row gx-1 align-items-center">
                                            <div class="col-xl-3">
                                                <div class="position-relative">
                                                    <h4 class="candidate-name mb-0"><a href="candidate-profile-v2.html" class="tran3s">Shofie Elina</a></h4>
                                                    <div class="candidate-post">IT Specialist</div>
                                                    <ul class="cadidate-skills style-none d-flex align-items-center">
                                                        <li>Design</li>
                                                        <li>UI</li>
                                                        <li>Digital</li>
                                                        <li class="more">2+</li>
                                                    </ul>
                                                    <!-- /.cadidate-skills -->
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Salary</span>
                                                    <div>$250-$300/week</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-6">
                                                <div class="candidate-info">
                                                    <span>Location</span>
                                                    <div>California, US</div>
                                                </div>
                                                <!-- /.candidate-info -->
                                            </div>
                                            <div class="col-xl-3 col-md-4">
                                                <div class="d-flex justify-content-lg-end">
                                                    <a href="candidate-profile-v2.html" class="save-btn text-center rounded-circle tran3s mt-10"><i class="bi bi-heart"></i></a>
                                                    <a href="candidate-profile-v2.html" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">View Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.candidate-profile-card -->
                        </div>
                        <!-- /.accordion-box -->


                        <div class="pt-20 d-sm-flex align-items-center justify-content-between">
                            <p class="m0 order-sm-last text-center text-sm-start xs-pb-20">Showing <span class="text-dark fw-500">1 to 20</span> of <span class="text-dark fw-500">1,270</span></p>
                            <div class="d-flex justify-content-center">
                                <ul class="pagination-two d-flex align-items-center style-none">
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#"><i class="bi bi-chevron-right"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.-->
                </div>
                <!-- /.col- -->
            </div>
        </div>
    </section>
    <!-- ./candidates-profile -->


<?php



get_footer();