<?php

/**
 * Template for displaying the "Saved Candidate" section in the employer dashboard.
 *
 * This template is used to show the list of candidates saved by the employer in their dashboard.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user id
$user_id = get_current_user_id();

// Check if this is the dashboard view or full view
$is_dashboard = $args['is_dashboard'] ?? true;
$limit        = $is_dashboard ? 4 : - 1; // Limit to 4 items in dashboard, no limit in full view
$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );

if ( ! is_array( $saved_candidates ) ) {
	$saved_candidates = ! empty( $saved_candidates ) ? [ $saved_candidates ] : [];
}
$saved_candidates = array_filter( array_map( 'intval', $saved_candidates ) );

// If in dashboard mode, limit the jobs to display
$display_candidates = $is_dashboard ? array_slice( $saved_candidates, 0, $limit ) : $saved_candidates;
?>
<div class="wrapper">

	<?php
	if ( ! $is_dashboard ) { ?>
		<h2 class="main-title"><?php esc_html_e( 'Saved Candidate', 'jobus' ); ?></h2>
		<?php
	}

	if ( ! empty( $display_candidates ) ) {
		foreach ( $display_candidates as $candidate_id ) {
            $category = get_the_terms( $candidate_id, 'jobus_candidate_cat' );
            $location = get_the_terms( $candidate_id, 'jobus_candidate_location' );
            $designation = get_post_meta( $candidate_id, 'jobus_candidate_designation', true );
            $skills = get_the_terms( $candidate_id, 'jobus_candidate_skill' );
			?>
			<div class="candidate-profile-card list-layout border-0 mb-25">
				<div class="d-flex">
					<div class="candidate-avatar online position-relative d-block me-auto ms-auto">
                        <a href="<?php echo esc_url( get_permalink( $candidate_id ) ); ?>" class="rounded-circle">
                            <?php echo get_the_post_thumbnail( $candidate_id, 'full', [ 'class' => 'lazy-img rounded-circle' ] ); ?>
                        </a>
                    </div>
					<div class="right-side">
						<div class="row gx-1 align-items-center">
							<div class="col-lg-4">
								<div class="position-relative">
									<h4 class="candidate-name mb-0">
                                        <a href="<?php echo esc_url(get_the_permalink($candidate_id)) ?>" class="tran3s">
                                            <?php echo esc_html(get_the_title($candidate_id)) ?>
                                        </a>
                                    </h4>
                                    <?php
                                    $max_skills = 3; // Maximum number of skills to display

                                    if ( $skills && count( $skills ) > $max_skills ) {
                                        // Shuffle the skills to get a random order
                                        shuffle( $skills );

                                        // Display the first 2 skills
                                        $displayed_skills = array_slice( $skills, 0, $max_skills );
                                        echo '<ul class="candidate-skills style-none d-flex align-items-center">';
                                        foreach ( $displayed_skills as $skill ) {
                                            echo '<li class="text-capitalize"><a href="' . esc_url( get_term_link($skill) ) . '">' . esc_html( $skill->name ) . '</a></li>';
                                        }

                                        // Display the count of remaining skills
                                        $remaining_count = count( $skills ) - $max_skills;
                                        echo '<li class="more">' . esc_html( $remaining_count ) . '+</li>';
                                        echo '</ul>';
                                    } else {
                                        // Display all skills
                                        if ( ! empty( $skills ) ) {
                                            echo '<ul class="candidate-skills style-none d-flex align-items-center">';
                                            foreach ( $skills as $skill ) {
                                                echo '<li class="text-capitalize"><a href="' . esc_url( get_term_link($skill) ) . '">' . esc_html( $skill->name ) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
								</div>
							</div>
                            <?php
                            if ( ! empty( $category ) && count( $category ) > 0 ) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="candidate-info">
                                        <span><?php esc_html_e( 'Category', 'jobus' ); ?></span>
                                        <a href="<?php echo esc_url(get_term_link( $category[0]->term_id )) ?>">
                                            <?php echo esc_html($category[0]->name) ?>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                            if ( ! empty( $location ) && count( $location ) > 0 ) { ?>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="candidate-info">
                                        <span><?php esc_html_e( 'Location', 'jobus' ) ?></span>
                                        <a href="<?php echo esc_url(get_term_link( $location[0]->term_id )) ?>">
                                            <?php echo esc_html($location[0]->name) ?>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col-lg-2 xs-mt-10">
                                <div class="action-button">
                                    <a href="javascript:void(0)"
                                       class="save-btn text-center rounded-circle tran3s jobus-dashboard-remove-saved-post"
                                       data-post_id="<?php echo esc_attr( $candidate_id ); ?>"
                                       data-post_type="jobus_candidate"
                                       data-nonce="<?php echo esc_attr( wp_create_nonce( 'jobus_employer_saved_candidate' ) ); ?>"
                                       title="<?php esc_attr_e( 'Remove', 'jobus' ); ?>">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </a>
                                    <a href="<?php echo esc_url( get_permalink( $candidate_id ) ); ?>"
                                       target="_blank"
                                       class="jobus-dashboard-post-view-more text-center rounded-circle tran3s"
                                       title="<?php esc_attr_e( 'View Job', 'jobus' ); ?>">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	} else {

        ?>
        <div class="bg-white card-box border-20 text-center p-5">
            <div class="no-applications-found">
                <i class="bi bi-clipboard-x fs-1 mb-3 text-muted"></i>
                <h4><?php esc_html_e( 'No saved candidates', 'jobus' ); ?></h4>
                <p class="text-muted"><?php esc_html_e( 'You haven\'t saved any candidates yet.', 'jobus' ); ?></p>
                <a href="<?php echo esc_url(get_post_type_archive_link('jobus_candidate')) ?>" class="btn btn-sm btn-primary" target="_blank">
                    <?php esc_html_e( 'Browse Candidates', 'jobus' ); ?>
                </a>
            </div>
        </div>
        <?php
	}

	?>
</div>