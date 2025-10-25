<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

// Get employer user ID (author of current job post)
$employer_id = get_post_field('post_author', get_the_ID());
$company_args = array(
    'post_type' => 'jobus_company',
    'author' => $employer_id,
    'posts_per_page' => 1,
);
$company_query = new WP_Query($company_args);
$company_name = '';
$company_url = '';
if ($company_query->have_posts()) {
    $company_name = $company_query->posts[0]->post_title;
    $company_url = get_permalink($company_query->posts[0]->ID);
}
?>

<section class="job-details jobus_job_details jbs-lg-pt-80 jbs-pb-130 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-xxl-9 jbs-col-xl-8 ">
                <div class="details-post-data jbs-me-xxl-5 jbs-pe-xxl-4">
                    <div class="job-head">
                        <div class="post-date">
                            <?php echo get_the_date('d M, Y') . ', '; ?>
                            <?php esc_html_e('by', 'jobus') ?>
                            <a href="<?php echo esc_url($company_url) ?>" class="jbs-fw-500 jbs-text-dark">
                                <?php echo esc_html($company_name); ?> </a>
                        </div>
                        <?php the_title('<h1 class="post-title">', '</h1>') ?>
                        <ul class="share-buttons jbs-d-flex jbs-flex-wrap jbs-style-none">
                            <li><a class="share-item"
                                    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                                    target="_blank">
                                    <i class="bi bi-facebook"></i>
                                    <span>Facebook</span>
                                </a>
                            </li>
                            <li><a class="share-item"
                                    href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                                    target="_blank">
                                    <i class="bi bi-twitter"></i>
                                    <span>Twitter</span>
                                </a>
                            </li>
                            <li><button type="button" class="share-item share-copy-btn"
                                    data-copy-url="<?php echo esc_url(get_permalink()); ?>">
                                    <i class="bi bi-link-45deg"></i>
                                    <span class="copy-text">Copy</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="jbs-col-xxl-3 jbs-col-xl-4 ">
                <div class="job-company-info jbs-ms-xl-5 jbs-ms-xxl-0 jbs-lg-mt-50">
                    <?php
                    $website = $meta['company_website'] ?? '';
                    $website_target = $website['target'] ?? '_self';

                    if ($company_query->have_posts()) {
                        $company_query->the_post();
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('full', array('class' => 'lazy-img jbs-m-auto logo'));
                        }
                        ?>
                        <div class="text-md jbs-text-dark jbs-text-center jbs-mt-15 jbs-mb-20">
                            <?php the_title() ?>
                        </div>
                        <?php
                        // Website button logic
                        if ($meta['is_company_website'] == 'custom' && !empty($website['url'])) { ?>
                            <a href="<?php echo esc_url($website['url']) ?>"
                                target="<?php echo esc_attr($website_target) ?>" class="website-btn tran3s">
                                <?php echo esc_html($website['text']) ?>
                            </a>
                            <?php
                        } else {
                            ?>
                            <a href="<?php the_permalink(); ?>" class="website-btn tran3s jbs-w-160">
                                <?php esc_html_e('Company Profile', 'jobus'); ?>
                            </a>
                            <?php
                        }
                        wp_reset_postdata();
                    }
                    ?>
                    <div class="jbs-border-top jbs-mt-40 jbs-pt-40">
                        <ul class="job-meta-data jbs-row jbs-style-none">
                            <?php
                            // Retrieve the repeater field configurations from settings options
                            $specifications = jobus_opt('job_specifications');
                            if (is_array($specifications)) {
                                foreach ($specifications as $field) {

                                    $meta_name = $field['meta_name'] ?? '';
                                    $meta_key = $field['meta_key'] ?? '';

                                    // Get the stored meta-values
                                    $meta_options = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

                                    if (isset($meta_options[$meta_key])) {
                                        ?>
                                        <li class="jbs-col-xl-6 jbs-col-md-4 jbs-col-sm-6">
                                            <?php
                                            if (!empty($meta_options[$meta_key])) {
                                                echo '<span>' . esc_html($meta_name) . '</span>';
                                            }
                                            if (!empty($meta_options[$meta_key] && is_array($meta_options[$meta_key]))) {
                                                echo '<div>';
                                                foreach ($meta_options[$meta_key] as $value) {
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
                                <?php
                            }
                            if (jobus_get_first_taxonomy_name('jobus_job_location')) {
                                ?>
                                <li class="jbs-col-xl-6 jbs-col-md-4 jbs-col-sm-6">
                                    <span><?php esc_html_e('Location', 'jobus'); ?></span>
                                    <div><?php echo esc_html(jobus_get_first_taxonomy_name('jobus_job_location')); ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="jbs-col-xl-6 jbs-col-md-4 jbs-col-sm-6">
                                <span><?php esc_html_e('Date', 'jobus'); ?></span>
                                <div><?php echo esc_html(get_the_date('d M, Y')); ?></div>
                            </li>
                        </ul>
                        <?php
                        if (jobus_get_tag_list()) { ?>
                            <div class="job-tags jbs-d-flex jbs-flex-wrap jbs-pt-15">
                                <?php echo wp_kses_post(jobus_get_tag_list()) ?>
                            </div>
                            <?php
                        }
                        // Check if user is logged in
                        if (is_user_logged_in()) {

                            // Get the current user ID and current job ID
                            $user_id = get_current_user_id();
                            $job_id = get_the_ID();
                            $user = wp_get_current_user();

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
                                <a href="javascript:void(0)" class="btn-one jbs-w-100 jbs-mt-25 disabled">
                                    <?php esc_html_e('Already Applied', 'jobus'); ?>
                                </a>
                                <?php
                            } else {
                                // Show the apply button if the user has not applied yet
                                if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                    <a href="<?php echo esc_url($meta['apply_form_url']); ?>"
                                        class="btn-one jbs-w-100 jbs-mt-25 ">
                                        <?php esc_html_e('Apply Now', 'jobus'); ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="btn-one jbs-w-100 jbs-mt-25 jbs-open-modal" data-target="#filterPopUp">
                                        <?php esc_html_e('Apply Now', 'jobus'); ?>
                                    </a>
                                <?php }
                            }
                        } else {
                            if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one jbs-w-100 jbs-mt-25">
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                                <?php
                            } else { ?>
                                <a href="#" class="btn-one jbs-w-100 jbs-mt-25" data-jbs-toggle="modal"
                                    data-jbs-target="#applyJobModal">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('.share-copy-btn');

        copyButtons.forEach(button => {
            button.addEventListener('click', async function (e) {
                e.preventDefault();
                const url = this.getAttribute('data-copy-url');
                const textSpan = this.querySelector('.copy-text');
                const originalText = textSpan.textContent;

                try {
                    // Try using the modern Clipboard API
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(url);
                    } else {
                        // Fallback for older browsers or non-secure contexts
                        const textArea = document.createElement('textarea');
                        textArea.value = url;
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    }

                    // Show success feedback
                    textSpan.textContent = 'Copied!';
                    this.classList.add('copied');

                    // Reset after 2 seconds
                    setTimeout(() => {
                        textSpan.textContent = originalText;
                        this.classList.remove('copied');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                    textSpan.textContent = 'Failed';
                    setTimeout(() => {
                        textSpan.textContent = originalText;
                    }, 2000);
                }
            });
        });
    });
</script>