<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobly
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Get the current company category
$current_company_cat = get_term_by('slug', get_query_var('company_cat'), 'company_cat');

// These parameters are used to determine the sorting order of company posts
$selected_order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$selected_order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';

$args = array(
    'post_type'      => 'company',
    'post_status'    => 'publish',
    'posts_per_page' => jobly_opt('company_posts_per_page'),
    'orderby'        => $selected_order_by,
    'order'          => $selected_order,
);

if ($current_company_cat ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'company_cat',
            'field'    => 'slug',
            'terms'    => get_query_var('company_cat'),
        ),
    );
}

$company_query = new \WP_Query($args);

// Get the count of posts for the current term
$company_count = $company_query->found_posts;

?>

    <section class="company-profiles pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <div class="col-lg-12">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e('All', 'jobly'); ?>
                            <span class="text-dark fw-500"><?php echo $company_count ?></span>
                            <?php printf(_n('company found', 'companies found', $company_count, 'jobly'), $company_count); ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php
                            $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
                            $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
                            $default = !empty($_GET['orderby']) ? 'selected' : '';

                            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                            ?>
                            <div class="short-filter d-flex align-items-center">
                                <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
                                <form action="" method="get">
                                    <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                        <option <?php echo esc_attr($default); ?>><?php esc_html_e( 'Default', 'jobly' ); ?></option>
                                        <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old)  ?>><?php esc_html_e( 'Newest to Oldest', 'jobly' ); ?></option>
                                        <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                                        <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                                        <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
                                    </select>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="accordion-box grid-style">
                        <div class="row">
                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();
                                $company_count  = jobly_get_selected_company_count(get_the_ID(), false);
                                $meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
                                $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                                    <div class="company-grid-layout mb-30<?php echo esc_attr($is_favourite) ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>"
                                               class="company-logo me-auto ms-auto rounded-circle">
                                                <?php the_post_thumbnail('full', [ 'class' => 'lazy-img rounded-circle' ]); ?>
                                            </a>
                                        <?php endif; ?>
                                        <h5 class="text-center">
                                            <a href="<?php the_permalink(); ?>" class="company-name tran3s">
                                                <?php the_title(); ?>
                                            </a>
                                        </h5>

	                                    <?php
	                                    $locations=get_the_terms(get_the_ID(),'company_location');
	                                    if(!empty($locations)){ ?>
                                            <p class="text-center mb-auto text-capitalize">
			                                    <?php foreach ( $locations as $location ) {echo $location->name ?>
				                                    <?php
			                                    }
			                                    ?>
                                            </p>
		                                    <?php
	                                    }

                                        if ($company_count > 0) {
                                            ?>
                                            <div class="bottom-line d-flex">
                                                <a href="<?php echo jobly_get_selected_company_count(get_the_ID(), true); ?>">
                                                    <?php echo sprintf(_n('%d Vacancy', '%d Vacancies', $company_count, 'jobly'), $company_count); ?>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>

                    <div class="pt-50 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobly_pagination($company_query); ?>

                    </div>

                </div>


            </div>
        </div>
    </section>


<?php

get_footer();