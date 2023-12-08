<?php
get_header();

$job_cat = isset($_GET['job_cat']) ? sanitize_text_field($_GET['job_cat']) : '';

$args = array(
    'post_type'      => 'job',
    'posts_per_page' => -1,
    'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
);

$args['tax_query'] = array(
    array(
        'taxonomy' => 'job_cat',
        'field'    => 'slug',
        'terms'    => $job_cat,
    ),
);

echo '<pre>';
print_r($args);
echo '</pre>';

$job_post = new \WP_Query($args);

// ================= Archive Banner ================//
jobly_get_template_part('banner/banner-search');

echo '<div class="container">';

?>

    <form role="search" method="get" id="searchform" action="<?php echo esc_url(get_post_type_archive_link('job')); ?>">
        <select name="job_cat">
            <option value="">All Categories</option>
            <?php
            $categories = get_terms('job_cat');
            foreach ($categories as $category) {
                $selected = ($job_cat === $category->slug) ? 'selected' : '';
                echo '<option value="' . $category->slug . '" ' . $selected . '>' . $category->name . '<span>' . $category->count . '</span></option>';
            }
            ?>
        </select>
        <input type="hidden" name="post_type" value="job">
        <input type="submit" id="searchsubmit" value="Search">
    </form>

<?php

if ($job_post->have_posts()) :
    while ($job_post->have_posts()) : $job_post->the_post();

        echo '<h3>' . get_the_title() . '</h3>';

    endwhile;
else :
    echo 'No results found';
endif;

echo '</div>';

get_footer();
