<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
?>
<section class="job-details jobus_job_details pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">

            <div class="col-xxl-9 col-xl-8">
                <div class="details-post-data me-xxl-5 pe-xxl-4">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4">
                <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mt-50">
                    <?php
                    $website = $meta[ 'company_website' ] ?? '';
                    $website_target = $website[ 'target' ] ?? '_self';
                    $select_company = $meta[ 'select_company' ] ?? '';
                    $select_company_ids = array_map('absint', (array) $select_company);

                    $args = [];
                    if (!empty($select_company)) {
                        $args = array(
                            'post_type' => 'jobus_company',
                            'post__in' => $select_company_ids,
                        );
                    }

                    $company_query = new WP_Query($args);

                    while ( $company_query->have_posts() ) {
                        $company_query->the_post();
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('full', array( 'class' => 'lazy-img m-auto logo' ));
                            ?>
                            <div class="text-md text-dark text-center mt-15 mb-20"><?php the_title() ?></div>
                            <?php
                        }
                        if ($meta[ 'is_company_website' ] == 'custom' && !empty($website[ 'url' ])) { ?>
                            <a href="<?php echo esc_url($website[ 'url' ]) ?>"
                               target="<?php echo esc_attr($website_target) ?>" class="website-btn tran3s">
                                <?php echo esc_html($website[ 'text' ]) ?>
                            </a>
                            <?php
                        } else { ?>
                            <a href="<?php the_permalink(); ?>" class="website-btn tran3s">
                                <?php esc_html_e('Company Profile', 'jobus'); ?>
                            </a>
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                    <div class="border-top mt-40 pt-40">
                        <?php
                        // Retrieve the repeater field configurations from settings options
                        $specifications = jobus_opt('job_specifications');
                        if (is_array($specifications)) {
                            ?>
                            <ul class="job-meta-data row style-none">
                                <?php
                                foreach ( $specifications as $field ) {

                                    $meta_name = $field[ 'meta_name' ] ?? '';
                                    $meta_key = $field[ 'meta_key' ] ?? '';

                                    // Get the stored meta-values
                                    $meta_options = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

                                    if (isset($meta_options[ $meta_key ])) {
                                        ?>
                                        <li class="col-xl-6 col-md-4 col-sm-6">
                                            <?php
                                            if (!empty($meta_options[ $meta_key ])) {
                                                echo '<span>' . esc_html($meta_name) . '</span>';
                                            }
                                            if (!empty($meta_options[ $meta_key ] && is_array($meta_options[ $meta_key ]))) {
                                                echo '<div>';
                                                foreach ( $meta_options[ $meta_key ] as $value ) {
                                                    $trim_value = str_replace('@space@', ' ', $value);
                                                    echo esc_html($trim_value);
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        if (jobus_get_tag_list()) { ?>
                            <div class="job-tags d-flex flex-wrap pt-15">
                                <?php echo wp_kses_post(jobus_get_tag_list()) ?>
                            </div>
                            <?php
                        }
                        // Check if user is logged in
                        if (is_user_logged_in()) {

                            // Get the current user ID and current job ID
                            $user_id = get_current_user_id();
                            $job_id = get_the_ID();
                            $user =  wp_get_current_user();

                            // Check if the user has already applied for this job
                            $has_applied = get_posts(array(
                                'post_type' => 'jobus_applicant',
                                'post_status' => 'publish',
                                'meta_query' => array(
                                    array(
                                        'key' => 'job_applied_for_id', // Meta-key for the job ID in the application post
                                        'value' => $job_id,
                                        'compare' => '='
                                    ),
                                    array(
                                        'key' => 'candidate_email', // Meta key for user email
                                        'value' => $user->user_email, // Compare with a logged-in user's email
                                        'compare' => '='
                                    )
                                )
                            ));

                            // If the user has already applied, show "Applied the Job" button
                            if (!empty($has_applied)) {
                                ?>
                                <a href="javascript:void(0)" class="btn-one w-100 mt-25 disabled">
                                    <?php esc_html_e('Already Applied', 'jobus'); ?>
                                </a>
                                <?php
                            } else {
                                // Show the apply button if the user has not applied yet
                                if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                    <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one w-100 mt-25">
                                        <?php esc_html_e('Apply Now', 'jobus'); ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="btn-one w-100 mt-25" data-bs-toggle="modal" data-bs-target="#applyJobModal">
                                        <?php esc_html_e('Apply Now', 'jobus'); ?>
                                    </a>
                                <?php }
                            }
                        } else {
                            if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one w-100 mt-25">
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                                <?php
                            } else { ?>
                                <a href="#" class="btn-one w-100 mt-25" data-bs-toggle="modal" data-bs-target="#applyJobModal">
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php jobus_get_template_part('single-job/related-job'); ?>