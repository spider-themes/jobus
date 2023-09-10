<?php
$meta = get_post_meta(get_the_ID(), 'jobly_meta', true);
$company_logo = !empty($meta[ 'company_logo' ]) ? $meta[ 'company_logo' ] : '';


//============== Job Specification Meta =================
$job_meta = get_post_meta(get_the_ID(), 'jobly_job_spec_meta', true);
$salary = !empty($job_meta[ 'salary' ]) ? $job_meta[ 'salary' ] : [];
$salary_value = !empty($salary[ 0 ]) ? $salary[ 0 ] : [];
$experience = !empty($job_meta[ 'experience' ]) ? $job_meta[ 'experience' ] : [];
$job_types = !empty($job_meta[ 'job-type' ]) ? $job_meta[ 'job-type' ] : [];
$job_types_value = !empty($job_types[ 0 ]) ? $job_types[ 0 ] : '';
$location = !empty($job_meta[ 'location' ]) ? $job_meta[ 'location' ] : [];
$location_value = !empty($location[ 0 ]) ? $location[ 0 ] : [];
$duration = !empty($job_meta[ 'duration' ]) ? $job_meta[ 'duration' ] : [];

?>

<div class="job-list-one style-two position-relative border-style mb-20">
    <div class="row justify-content-between align-items-center">
        <div class="col-md-5">
            <div class="job-title d-flex align-items-center">
                <a href="<?php the_permalink(); ?>" class="logo">
                    <?php
                    if ($company_logo[ 'id' ]) {
                        echo wp_get_attachment_image($company_logo[ 'id' ], 'full', '', [ 'class' => 'lazy-img m-auto' ]);
                    } else {
                        the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]);
                    }
                    ?>
                </a>
                <div class="split-box1">
                    <?php
                    if ($job_types_value) { ?>
                        <a href="job-details-v1.html"
                           class="job-duration fw-500"><?php echo esc_html($job_types_value) ?></a>
                        <?php
                    }
                    ?>
                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s"
                       title="<?php the_title_attribute() ?>">
                        <?php the_title() ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <?php
            if ($location_value) { ?>
                <div class="job-location">
                    <a href="job-details-v1.html"><?php echo esc_html($location_value) ?></a>
                </div>
                <?php
            }
            if ($salary_value) { ?>
                <div class="job-salary">
                    <span class="fw-500 text-dark"><?php echo esc_html($salary_value) ?></span>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s me-3" title="Save Job">
                    <i class="bi bi-bookmark-dash"></i>
                </a>
                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                    <?php esc_html_e('APPLY', 'jobly'); ?>
                </a>
            </div>
        </div>

    </div>
</div>