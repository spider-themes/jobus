<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
$website = $meta[ 'company_website' ] ?? '';
$website_target = $website[ 'target' ] ?? '_self';
?>
    <section class="company-details pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <div class="col-xxl-3 col-xl-4 order-xl-last">
                    <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mb-50">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('full', array( 'class' => 'lazy-img m-auto logo' ));
                        }
                        ?>
                        <div class="text-md text-dark text-center mt-15 mb-20 lg-mb-10"><?php the_title() ?></div>
                        <?php if (!empty($website[ 'url' ])) : ?>
                            <div class="text-center">
                                <a href="<?php echo esc_url($website[ 'url' ]) ?>" class="website-btn-two tran3s"
                                   target="<?php echo esc_attr($website_target) ?>">
                                    <?php echo esc_html($website[ 'text' ]) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="border-top mt-35 lg-mt-20 pt-25">
                            <ul class="job-meta-data row style-none">

                                <?php
                                // Retrieve the repeater field configurations from settings options
                                $specifications = jobly_opt('company_specifications');
                                if ( $specifications ) {

                                    foreach ( $specifications as $field ) {

                                        $meta_name = $field[ 'meta_name' ] ?? '';
                                        $meta_key = $field[ 'meta_key' ] ?? '';

                                        // Get the stored meta-values
                                        $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);

                                        if (isset($meta_options[ $meta_key ])) {
                                            ?>
                                            <li class="col-12">
                                                <?php
                                                if (isset($meta_options[ $meta_key ]) && !empty($meta_options[ $meta_key ])) {
                                                    echo '<span>' . esc_html($meta_name) . ':</span>';
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

                                }

                                $terms = wp_get_post_terms(get_the_ID(), 'company_cat', array( 'fields' => 'names' ));
                                if (is_array($terms)) {
                                    ?>
                                    <li class="col-12">
                                        <span><?php printf(_n('Category:', 'Categories:', count($terms), 'jobly')) ?></span>
                                        <?php echo '<div>' . implode(', ', $terms) . '</div>'; ?>
                                    </li>
                                    <?php
                                }

                                $social_icons = jobly_opt('jobly_social_icons');
                                if (is_array($social_icons)) {
                                    ?>
                                    <li class="col-12">
                                        <span><?php esc_html_e('Social: ', 'jobly'); ?></span>
                                        <div>
                                            <?php
                                            foreach ( $social_icons as $item ) {
                                                if (!empty($item[ 'url' ])) { ?>
                                                    <a href="<?php echo esc_url($item[ 'url' ]) ?>" class="me-3">
                                                        <i class="<?php echo esc_attr($item[ 'icon' ]) ?>"></i>
                                                    </a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>

                            <a href="<?php echo jobly_get_selected_company_count(get_the_ID(), true); ?>" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-25">
                                <?php esc_html_e('Posted Jobs', 'jobly'); ?>
                            </a>
                        </div>
                    </div>
                    <!-- /.job-company-info -->
                </div>

                <div class="col-xxl-9 col-xl-8 order-xl-first">
                    <div class="details-post-data me-xxl-5 pe-xxl-4">

                        <?php the_content(); ?>

                        <nav class="share-option mt-60">

                            <?php jobly_social_share_icons() ?>

                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php


get_footer();
