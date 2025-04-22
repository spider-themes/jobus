<?php
/**
 * Candidate Archive - Template for displaying candidate archive
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/candidate-archive-1.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="candidates-profile pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-candidate/sidebar-classic-filters'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">

                        <?php include ( 'loop/result-count.php' ); ?>

                        <?php include ( 'loop/sortby.php' ); ?>

                    </div>

                    <?php
                    if ( $current_view == 'grid' ) {
                        ?>
                        <div class="accordion-box grid-style show">
                            <div class="row">
                                <?php
                                while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
                                    $meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
                                    $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                    $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                    ?>
                                    <div class="col-xxl-4 col-sm-6 d-flex">

                                        <div class="candidate-profile-card<?php echo esc_attr($is_favourite) ?> text-center grid-layout mb-25">

                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <div class="cadidate-avatar online position-relative d-block m-auto">
                                                    <a href="<?php the_permalink() ?>" class="rounded-circle">
                                                        <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                                                    </a>
                                                </div>
                                            <?php endif ?>

                                            <h4 class="candidate-name mt-15 mb-0">
                                                <a href="<?php the_permalink() ?>" class="tran3s">
                                                    <?php the_title() ?>
                                                </a>
                                            </h4>

                                            <?php
                                            if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                                ?>
                                                <div class="candidate-post text-capitalize">
                                                    <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1')) ?>
                                                </div>
                                                <?php
                                            }

                                            $skills = get_the_terms(get_the_ID(), 'jobus_candidate_skill');
                                            $max_skills = 2;

                                            if ( $skills && count($skills) > $max_skills ) {
                                                // Shuffle the skills to get a random order
                                                shuffle($skills);

                                                // Display the first 2 skills
                                                $displayed_skills = array_slice( $skills, 0, $max_skills );
                                                echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center pt-30 sm-pt-20 pb-10">';
                                                    foreach ( $displayed_skills as $skill ) {
                                                        echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                    }

                                                    // Display the count of remaining skills
                                                    $remaining_count = count($skills) - $max_skills;
                                                    echo '<li class="more">' . esc_html($remaining_count) . '+</li>';
                                                echo '</ul>';
                                            } else {
                                                if ( !empty($skill) ) {
                                                    // Display all skills
                                                    echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">';
                                                    foreach ($skills as $skill) {
                                                        echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                    }
                                                    echo '</ul>';
                                                }
                                            }
                                            ?>
                                            <div class="row gx-1">
                                                <?php
                                                if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                    ?>

                                                    <div class="col-md-6">
                                                        <div class="candidate-info mt-10">
                                                            <span> <?php echo esc_html(jobus_meta_candidate_spec_name(2)); ?> </span>
                                                            <div class="text-capitalize">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2')) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

	                                            $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
	                                            if ( !empty($locations) ) { ?>
                                                    <div class="col-md-6">
                                                        <div class="candidate-info mt-10">
                                                            <span><?php esc_html_e('Location', 'jobus'); ?></span>
                                                            <?php
                                                            foreach ( $locations as $location ) { ?>
                                                                <div class="text-capitalize"><?php echo esc_html($location->name) ?></div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
		                                            <?php
	                                            }
	                                            ?>
                                            </div>

                                            <div class="row gx-2 pt-25 sm-pt-10">
                                                <div class="col-md-6">
                                                    <a href="<?php the_permalink() ?>" class="profile-btn tran3s w-100 mt-5">
                                                        <?php esc_html_e('View Profile', 'jobus') ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-6">
                                                    <a href="javascript:void(0)" class="msg-btn tran3s w-100 mt-5">
                                                        <?php esc_html_e('Message', 'jobus') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>

                            </div>
                        </div>
                        <?php
                    }

                    elseif ( $current_view == 'list' ) {
                        ?>
                        <div class="accordion-box list-style">
                            <?php
                            while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
                                $meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
                                $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                ?>
                                <div class="candidate-profile-card<?php echo esc_attr($is_favourite) ?> list-layout mb-25">
                                    <div class="d-flex">
                                        <div class="cadidate-avatar online position-relative d-block me-auto ms-auto">
                                            <a href="<?php the_permalink() ?>" class="rounded-circle">
                                                <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                                            </a>
                                        </div>
                                        <div class="right-side">
                                            <div class="row gx-1 align-items-center">
                                                <div class="col-xl-4">
                                                    <div class="position-relative">
                                                        <h4 class="candidate-name mb-0">
                                                            <a href="<?php the_permalink() ?>" class="tran3s">
                                                                <?php the_title() ?>
                                                            </a>
                                                        </h4>

                                                        <?php
                                                        if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                                            ?>
                                                            <div class="candidate-post text-capitalize">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1')) ?>
                                                            </div>
                                                            <?php
                                                        }

                                                        $skills = get_the_terms(get_the_ID(), 'jobus_candidate_skill');
                                                        $max_skills = 2;

                                                        if ($skills && count($skills) > $max_skills) {
                                                            // Shuffle the skills to get a random order
                                                            shuffle($skills);

                                                            // Display the first 2 skills
                                                            $displayed_skills = array_slice($skills, 0, $max_skills);
                                                            echo '<ul class="cadidate-skills style-none d-flex align-items-center">';
                                                            foreach ($displayed_skills as $skill) {
                                                                echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                            }

                                                            // Display the count of remaining skills
                                                            $remaining_count = count($skills) - $max_skills;
                                                            echo '<li class="more">' . esc_html($remaining_count) . '+</li>';
                                                            echo '</ul>';
                                                        } else {
                                                            // Display all skills
                                                            if ( !empty($skills) ) {
	                                                            echo '<ul class="cadidate-skills style-none d-flex align-items-center">';
	                                                            foreach ($skills as $skill) {
		                                                            echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
	                                                            }
	                                                            echo '</ul>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <?php
                                                if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                    ?>
                                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                                        <div class="candidate-info">
                                                            <span><?php echo esc_html(jobus_meta_candidate_spec_name(2)); ?></span>
                                                            <div class="text-capitalize">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2') ) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

                                                $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
                                                if (!empty($locations )) {
                                                    ?>
                                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                                        <div class="candidate-info">
                                                            <span><?php esc_html_e('Location', 'jobus'); ?></span>
                                                            <?php
                                                            foreach ($locations as $location ) { ?>
                                                                <div class="text-capitalize"><?php echo esc_html($location->name) ?></div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="col-xl-2 col-md-4">
                                                    <div class="d-flex justify-content-lg-end">
                                                        <a href="<?php the_permalink() ?>" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">
                                                            <?php esc_html_e('View Profile', 'jobus') ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    }

                    // Pagination
                    include ( 'loop/pagination.php' );
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>