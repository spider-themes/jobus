<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$company_count  = jobly_get_selected_company_count(get_the_ID(), false);
$meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
$post_favourite = $meta[ 'post_favorite' ] ?? '';
$is_favourite = ($post_favourite == '1') ? ' favourite' : '';
?>
<div class="col-xl-4 col-lg-6 col-md-4 col-sm-6 d-flex">
    <div class="company-grid-layout mb-30<?php echo esc_attr($is_favourite) ?>">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>"
               class="company-logo me-auto ms-auto rounded-circle">
                <?php the_post_thumbnail('full', [ 'class' => 'lazy-img rounded-circle' ]); ?>
            </a>
        <?php endif; ?>
        <h5 class="text-center">
            <a href="<?php the_permalink(); ?>" class="company-name tran3s">
                <?php the_title(); ?>
            </a>
        </h5>

        <?php
            if (jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1')) {
                ?>
                <p class="text-center mb-auto"><?php echo jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1') ?></p>
                <?php
            }
        
        if ($company_count > 0) { 
            ?>
            <div class="bottom-line d-flex">
                <a href="<?php echo jobly_get_selected_company_count(get_the_ID(), true); ?>">
                <?php echo sprintf(_n('%d Vacancy', '%d Vacancies', $company_count, 'jobly'), $company_count); ?>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
