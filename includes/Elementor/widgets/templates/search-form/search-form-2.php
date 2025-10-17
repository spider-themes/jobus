<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>

<div class="job-search-two jbs-position-relative">

    <form action="<?php echo esc_url(get_post_type_archive_link($search_result_form)) ?>" class="jbs-d-flex jbs-align-items-center jbs-justify-content-between" id="searchform" method="get" autocomplete="on">
        <input type="hidden" name="post_type" value="<?php echo esc_attr($search_result_form) ?>"/>
        <input type="search" id="searchInput" name="s" placeholder="<?php esc_attr_e('Search job, title etc....', 'jobus'); ?>">
        <button type="submit" class="jbs-btn jbs-btn-success jbs-border-30 jbs-h-100"><?php echo esc_html($settings['submit_btn']) ?></button>
    </form>

    <?php
    if ($settings[ 'is_keyword' ] == 'yes' ) {
        ?>
        <ul class="filter-tags jbs-d-flex jbs-flex-wrap style-none mt-25">
            <?php
            if ( !empty($settings['keyword_label']) ) { ?>
                <li class="fw-500 text-dark jbs-me-1"><?php echo esc_html($settings[ 'keyword_label' ]) ?></li>
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