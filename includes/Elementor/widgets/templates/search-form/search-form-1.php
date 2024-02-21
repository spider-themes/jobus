<?php
if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

?>

<div class="job-search-one position-relative me-xl-5">
    <form action="<?php echo esc_url(get_post_type_archive_link('job')) ?>" role="search" method="get">
        <input type="hidden" name="post_type" value="job"/>

        <div class="row">
            <?php
            if (!empty($settings[ 'job_search_form' ])) {
                foreach ( $settings[ 'job_search_form' ] as $index => $item ) {

                    $border_left = $index > 0 ? ' border-left' : '';
                    $select_job_attr = $item[ 'select_job_attr' ] ?? '';
                    $job_specifications = jobly_get_specs_options();
                    $job_specifications = $job_specifications[ $select_job_attr ] ?? '';
                    ?>
                    <div class="col-md-<?php echo esc_attr($item[ 'column' ]) ?>">
                        <div class="input-box<?php echo esc_attr($border_left) ?>">
                            <?php
                            if (!empty($item[ 'attr_title' ])) { ?>
                                <div class="label"><?php echo esc_html($item[ 'attr_title' ]) ?></div>
                                <?php
                            }

                            // Select job category or job tag
                            if ($select_job_attr == 'job_cat' || $select_job_attr == 'job_tag') {
                                ?>
                                <select class="nice-select lg" name="<?php echo esc_attr($select_job_attr) ?>"
                                        id="<?php echo esc_attr($select_job_attr) ?>">
                                    <?php
                                    $taxonomy_terms = get_terms($select_job_attr);
                                    foreach ($taxonomy_terms as $term) {
                                        ?>
                                        <option value="<?php echo esc_attr($term->slug) ?>"><?php echo esc_html($term->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            } elseif ($job_specifications) {
                                ?>
                                <select class="nice-select lg" name="<?php echo esc_attr($select_job_attr) ?>"
                                        id="<?php echo esc_attr($select_job_attr) ?>">
                                    <?php
                                    if ($job_specifications) {
                                        foreach ( $job_specifications as $job_spec_value ) {
                                            $meta_value = $job_spec_value[ 'meta_values' ] ?? '';
                                            $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                            $modifiedVal = strtolower($modifiedSelect);
                                            ?>
                                            <option value="<?php echo esc_attr($modifiedVal) ?>"><?php echo esc_html($meta_value) ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="col-md-3">
                <button type="submit" class="fw-500 text-uppercase h-100 tran3s search-btn"><?php echo esc_html($settings[ 'submit_btn' ]) ?></button>
            </div>
        </div>
    </form>
    <?php
    if ($settings[ 'is_keyword' ] == 'yes' ) {
        ?>
        <ul class="tags d-flex flex-wrap style-none mt-20">
            <?php
            if ( !empty($settings['keyword_label']) ) { ?>
                <li class="fw-500 text-white me-1"><?php echo esc_html($settings[ 'keyword_label' ]) ?></li>
                <?php
            }
            if (!empty($settings[ 'keywords' ])) {
                foreach ( $settings[ 'keywords' ] as $keyword ) {
                    ?>
                    <li><a href="#"><?php echo esc_html($keyword[ 'title' ]) ?> </a></li>
                    <?php
                }
            }
            ?>
        </ul>
        <?php
    }
    ?>
</div>