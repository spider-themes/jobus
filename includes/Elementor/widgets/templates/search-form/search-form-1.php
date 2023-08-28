<?php
$categories = get_terms(array(
    'taxonomy' => 'job_cat',
    'hide_empty' => false,
));

?>

<div class="job-search-one position-relative">

    <form action="<?php echo esc_url(home_url('/')) ?>" role="search" method="get">
        <div class="row">

            <div class="col-md-5">
                <div class="input-box">
                    <label for="searchInput" class="label">What are you looking for?</label>
                    <input type="search" id="searchInput" autocomplete="off" name="s" placeholder="Job title, keywords, or company">
                </div>
            </div>

            <?php //include 'search-spinner.php' ?>

            <input type="hidden" id="hidden_post_type" name="post_type" value="job" />

            <div class="col-md-4">
                <div class="input-box border-left">
                    <div class="label">Category</div>
                    <select class="nice-select lg" name="job_cat">
                        <?php
                        foreach ( $categories as $cat ) {
                            echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <button class="fw-500 text-uppercase h-100 tran3s search-btn" type="submit">Search</button>
            </div>

        </div>
    </form>


    <?php
    if ( $settings['is_keyword'] == 'yes' ) {
        ?>
        <ul class="tags d-flex flex-wrap style-none mt-20">
            <?php include 'keywords.php' ?>
        </ul>
        <?php
    }
    ?>
</div>