<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $job_post;

the_posts_pagination(array(
    'screen_reader_text' => ' ',
    'prev_text'          => '<i class="arrow_left"></i>',
    'next_text'          => '<i class="arrow_right"></i>'
));


?>
<ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
    <?php
    $big = 999999999; // need an unlikely integer
    echo paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' =>  $job_post->max_num_pages,
        'prev_text' => '<i class="fas fa-chevron-left"></i>',
        'next_text' => '<i class="fas fa-chevron-right"></i>',
    ));
    ?>
</ul>
