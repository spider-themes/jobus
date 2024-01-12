<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit(); // Exit if accessed directly.
}

?>

<div class="job-search-one position-relative me-xl-5">
    <form action="<?php echo esc_url(get_post_type_archive_link('job')) ?>" role="search" method="get">

        <input type="hidden" name="post_type" value="job"/>

        <div class="row">
            <?php
            if ( !empty($settings['job_search_form']) ) {
                foreach ( $settings['job_search_form'] as $index => $item ) {

                    $border_left = $index > 0 ? ' border-left' : '';
                    $select_search_form = $item['select_search_form'] ?? '';
                    $job_specifications = jobly_get_specs_options();
                    $job_specifications = $job_specifications[ $select_search_form ];
                    ?>
                    <div class="col-md-<?php echo esc_attr($item['column']) ?>">
                        <div class="input-box<?php echo esc_attr($border_left) ?>">
                            <div class="label"><?php echo esc_html($item['form_title']) ?></div>
                            <select class="nice-select lg" name="<?php echo esc_attr($select_search_form) ?>" id="<?php echo esc_attr($select_search_form) ?>">
                                <?php
                                if ( $job_specifications ) {
                                    foreach ( $job_specifications as $job_spec_value ) {
                                        $meta_value = $job_spec_value['meta_values'] ?? '';
                                        $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                        $modifiedVal = strtolower($modifiedSelect);
                                        ?>
                                        <option value="<?php echo esc_attr($modifiedVal) ?>"><?php echo esc_html($meta_value) ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="col-md-3">
                <button type="submit" class="fw-500 text-uppercase h-100 tran3s search-btn"><?php echo esc_html($settings['submit_btn']) ?></button>
            </div>
        </div>
    </form>
    <?php
    if ( !empty($settings['is_keyword']) ) {
        ?>
        <ul class="tags d-flex flex-wrap style-none mt-20">
            <li class="fw-500 text-white me-2"><?php echo esc_html($settings['keyword_label']) ?></li>
            <?php
            if ( !empty($settings['keywords']) ) {
                foreach ( $settings['keywords'] as $keyword ) {
                    ?>
                    <li><a href="#"><?php echo esc_html($keyword['title']) ?></a></li>
                    <?php
                }
            }

            ?>
        </ul>
        <?php
    }
    ?>
</div>