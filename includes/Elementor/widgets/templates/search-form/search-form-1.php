<?php
if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}
?>

<div class="job-search-one jbs-position-relative">
    <form action="<?php echo esc_url(get_post_type_archive_link($search_result_form)) ?>" role="search" method="post">
        <input type="hidden" name="post_type" value="<?php echo esc_attr($search_result_form) ?>"/>

        <div class="jbs-row">
            <?php
            if (!empty($settings[ 'job_search_form' ])) {
                foreach ( $settings[ 'job_search_form' ] as $index => $item ) {

                    $border_left = $index > 0 ? ' border-left' : '';
                    $select_job_attr = $item[ 'select_job_attr' ] ?? '';
                    $job_specifications = jobus_get_specs_options();
                    $job_specifications = $job_specifications[ $select_job_attr ] ?? '';
                    ?>
                    <div class="jbs-col-md-<?php echo esc_attr($item[ 'column' ]) ?>">
                        <div class="input-box<?php echo esc_attr($border_left) ?>">
                            <?php
                            if (!empty($item[ 'attr_title' ])) { ?>
                                <div class="label"><?php echo esc_html($item[ 'attr_title' ]) ?></div>
                                <?php
                            }

                            // Select job category or job tag
                            if ($select_job_attr == 'jobus_job_cat' || $select_job_attr == 'jobus_job_location' || $select_job_attr == 'jobus_job_tag') {
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

                                if ( $item['layout_type'] == 'dropdown' ) {
                                    ?>
                                    <select class="nice-select lg" name="<?php echo esc_attr($select_job_attr) ?>" id="<?php echo esc_attr($select_job_attr) ?>">
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
                                } elseif ($item['layout_type'] == 'text' ) {
                                    ?>
                                    <input type="text" name="s" id="searchInput" placeholder="<?php echo esc_attr($item['text_placeholder']); ?>" class="keyword">
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="jbs-col-md-3 job-search-btn-wrapper">
                <button type="submit" class="job-search-one-btn fw-500 jbs-text-uppercase tran3s search-btn"><?php echo esc_html($settings[ 'submit_btn' ]) ?></button>
            </div>
        </div>
    </form>
    <?php
    if ($settings[ 'is_keyword' ] == 'yes' ) {
        ?>
        <ul class="tags jbs-d-flex jbs-flex-wrap jbs-style-none mt-20">
            <?php
            if ( !empty($settings['keyword_label']) ) { ?>
                <li class="fw-500 jbs-text-white jbs-me-1"><?php echo esc_html($settings[ 'keyword_label' ]) ?></li>
                <?php
            }
            if (!empty($settings[ 'keywords' ])) {
                foreach ( $settings[ 'keywords' ] as $keyword ) {
                    if ( !empty($keyword['title']) ) { ?>
                        <li class="jbs-mt-0">
                            <a <?php jobus_button_link($keyword['link']); ?>>
                                <?php echo esc_html($keyword[ 'title' ]) ?>
                            </a>
                        </li>
                        <?php
                    }
                }
            }
            ?>
        </ul>
        <?php
    }
    ?>
</div>