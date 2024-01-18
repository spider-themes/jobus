<div class="job-search-two position-relative">

    <form action="<?php echo esc_url(get_post_type_archive_link('job')) ?>" class="d-flex align-items-center justify-content-between" id="searchform" method="get" autocomplete="on">
        <input type="hidden" name="post_type" value="job"/>
        <input type="search" id="searchInput" name="s" placeholder="<?php esc_attr_e('Search job, title etc....', 'jobly'); ?>">
        <button type="submit" class="btn-five h-100"><?php echo esc_html($settings['submit_btn']) ?></button>
    </form>

    <?php
    if ( !empty($settings['is_keyword']) ) {
        ?>
        <ul class="filter-tags d-flex flex-wrap style-none mt-25">
            <li class="fw-500 text-dark me-1"><?php echo esc_html($settings['keyword_label']) ?></li>
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