<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$company_count = jobly_get_selected_company_count(get_the_ID());
$meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
$post_favourite = $meta[ 'post_favorite' ] ?? '';
$is_favourite = ($post_favourite == '1') ? ' favourite' : '';

?>
<div class="company-list-layout mb-20<?php echo esc_attr($is_favourite) ?>">
    <div class="row justify-content-between align-items-center">
        <div class="col-xl-5">
            <div class="d-flex align-items-xl-center">
                <a href="<?php the_permalink(); ?>" class="company-logo rounded-circle">
                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img rounded-circle' ]); ?>
                </a>
                <div class="company-data">
                    <h5 class="m0">
                        <a href="<?php the_permalink(); ?>" class="company-name tran3s">
                            <?php the_title() ?>
                        </a>
                    </h5>
                    <?php
                    if (jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1')) { ?>
                        <p><?php echo jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1') ?></p>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-8">
            <div class="d-flex align-items-center ps-xxl-5 lg-mt-20">
                <div class="d-flex align-items-center">
                    <div class="team-text">
                        <?php
                        // Trim the content and get the first word
                        $company_archive_meta_2 = jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_2');

                        // Get the first word
                        $trim_content = explode(' ', wp_trim_words($company_archive_meta_2, 1, ''));
                        $first_trim_content = $trim_content[0];

                        // Get the remaining words after removing the first word
                        $remaining_words = implode(' ', array_slice(explode(' ', wp_trim_words($company_archive_meta_2, 9999, '')), 1));

                        // Check if the first word is numeric or ends with '+'
                        if (is_numeric($first_trim_content) || substr($first_trim_content, -1) === '+') {
                            ?>
                            <span class="text-md fw-500 text-dark d-block"><?php echo esc_html($first_trim_content) ?></span>
                            <?php echo esc_html($remaining_words) ?>
                            <?php
                        } else {
                            echo esc_html($company_archive_meta_2);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-4">
            <div class="btn-group d-flex align-items-center justify-content-md-end lg-mt-20">
                <?php
                if ($company_count > 0) { ?>
                    <a href="#" class="open-job-btn text-center fw-500 tran3s me-2">
                        <?php echo sprintf(_n('%d open job', '%d open jobs', $company_count, 'jobly'), $company_count); ?>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
