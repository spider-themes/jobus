<div class="job-search-two position-relative me-xxl-5">

    <form action="<?php echo esc_url(home_url('/')) ?>" class="d-flex align-items-center justify-content-between" id="searchform" target="_blank" method="get" autocomplete="on">
        <input type="search" id="searchInput" name="s" placeholder="Search job, title etc....">
        <input type="hidden" name="post_type" value="job" />
        <button type="submit" class="btn-five h-100">Search</button>
    </form>

    <?php
    if ( $settings['is_keyword'] == 'yes' ) {
        ?>
        <ul class="filter-tags d-flex flex-wrap style-none mt-25">
            <?php include 'keywords.php' ?>
        </ul>
        <?php
    }
    ?>
</div>