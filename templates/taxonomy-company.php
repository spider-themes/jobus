<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Get the current company category
$current_company_cat = get_term_by('slug', get_query_var('jobus_company_cat'), 'jobus_company_cat');
$current_company_location = get_term_by('slug', get_query_var('jobus_company_location'), 'jobus_company_location');

// These parameters are used to determine the sorting order of company posts
$selected_order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
$selected_order = sanitize_text_field($_GET['order']) ?? 'desc';

$args = array(
    'post_type'      => 'jobus_company',
    'post_status'    => 'publish',
    'posts_per_page' => jobus_opt('company_posts_per_page'),
    'orderby'        => $selected_order_by,
    'order'          => $selected_order,
);

if ( $current_company_cat || $current_company_location ) {
    $args['tax_query'] = array(
        'relation' => 'OR',//Must satisfy at least one taxonomy query
        array(
            'taxonomy' => 'jobus_company_cat',
            'field'    => 'slug',
            'terms'    => get_query_var('jobus_company_cat'),
        ),
        array(
            'taxonomy' => 'jobus_company_location',
            'field'    => 'slug',
            'terms'    => get_query_var('jobus_company_location'),
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
                            <?php esc_html_e('All', 'jobus'); ?>
                            <span class="text-dark fw-500"><?php echo esc_html($company_count) ?></span>
                            <?php
                            /* translators: 1: company found, 2: companies found */
                            echo esc_html(sprintf(_n('company found', 'companies found', $company_count, 'jobus'), $company_count));
                            ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php
                            $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
                            $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
                            $default = ! empty( $order_by ) ? 'selected' : '';

                            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                            ?>
                            <div class="short-filter d-flex align-items-center">
                                <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobus'); ?></div>
                                <form action="" method="get">
                                    <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                        <option <?php echo esc_attr($default); ?>><?php esc_html_e( 'Default', 'jobus' ); ?></option>
                                        <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old)  ?>><?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?></option>
                                        <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?></option>
                                        <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobus' ); ?></option>
                                        <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobus' ); ?></option>
                                    </select>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="accordion-box grid-style">
                        <div class="row">
                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();
                                $company_count  = jobus_get_selected_company_count(get_the_ID(), false);
                                $meta = get_post_meta(get_the_ID(), 'jobus_meta_company_options', true);
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
	                                    $locations=get_the_terms(get_the_ID(),'jobus_company_location');
	                                    if(!empty($locations)){ ?>
                                            <p class="text-center mb-auto text-capitalize">
			                                    <?php
                                                foreach ( $locations as $location ) {
                                                    echo esc_html($location->name);
			                                    }
			                                    ?>
                                            </p>
		                                    <?php
	                                    }

                                        if ($company_count > 0) {
                                            ?>
                                            <div class="bottom-line d-flex">
                                                <a href="<?php echo jobus_get_selected_company_count(get_the_ID(), true); ?>">
                                                    <?php
                                                    /* translators: 1: Vacancy, 2: Vacancies */
                                                    echo esc_html(sprintf(_n('%d Vacancy', '%d Vacancies', $company_count, 'jobus'), $company_count));
                                                    ?>
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

                        <?php jobus_pagination($company_query); ?>

                    </div>

                </div>

            </div>
        </div>
    </section>


<?php

get_footer();