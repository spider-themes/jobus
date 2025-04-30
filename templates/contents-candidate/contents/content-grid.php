<?php
/**
 * Candidate Grid Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/contents/content-grid.php.
 *
 * This template is used to display candidate listings in a grid format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="accordion-box grid-style show">
    <div class="row">
		<?php
		while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
			$meta                 = get_post_meta( get_the_ID(), 'jobus_meta_candidate_options', true );
			$post_favourite       = $meta['post_favorite'] ?? '';
			$is_favourite         = ( $post_favourite == '1' ) ? ' favourite' : '';
			$is_popup_border_none = $archive_layout == '2' ? ' border-0' : '';
			$column               = sanitize_html_class( jobus_opt( 'candidate_archive_grid_column' ) );
			?>
            <div class="col-lg-<?php echo esc_attr( $column ) ?> col-sm-6 d-flex">

                <div class="candidate-profile-card<?php echo esc_attr( $is_favourite ) ?> text-center grid-layout mb-25 <?php echo esc_attr( $is_popup_border_none ) ?>">

					<?php if ( has_post_thumbnail() ) : ?>
                        <div class="cadidate-avatar online position-relative d-block m-auto">
                            <a href="<?php the_permalink() ?>" class="rounded-circle">
								<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img rounded-circle' ] ) ?>
                            </a>
                        </div>
					<?php endif ?>

                    <h4 class="candidate-name mt-15 mb-0">
                        <a href="<?php the_permalink() ?>" class="tran3s">
							<?php the_title() ?>
                        </a>
                    </h4>

					<?php
					if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_1' ) ) {
						?>
                        <div class="candidate-post text-capitalize">
							<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_1' ) ) ?>
                        </div>
						<?php
					}

					$skills     = get_the_terms( get_the_ID(), 'jobus_candidate_skill' );
					$max_skills = 2;

					if ( $skills && count( $skills ) > $max_skills ) {
						// Shuffle the skills to get a random order
						shuffle( $skills );

						// Display the first 2 skills
						$displayed_skills = array_slice( $skills, 0, $max_skills );
						echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center pt-30 sm-pt-20 pb-10">';
						foreach ( $displayed_skills as $skill ) {
							echo '<li class="text-capitalize">' . esc_html( $skill->name ) . '</li>';
						}

						// Display the count of remaining skills
						$remaining_count = count( $skills ) - $max_skills;
						echo '<li class="more">' . esc_html( $remaining_count ) . '+</li>';
						echo '</ul>';
					} else {
						if ( ! empty( $skills ) ) {
							// Display all skills
							echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">';
							foreach ( $skills as $skill ) {
								echo '<li class="text-capitalize">' . esc_html( $skill->name ) . '</li>';
							}
							echo '</ul>';
						}
					}
					?>
                    <div class="row gx-1">
						<?php
						if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) {
							?>

                            <div class="col-md-6">
                                <div class="candidate-info mt-10">
                                    <span> <?php echo esc_html( jobus_meta_candidate_spec_name( 2 ) ); ?> </span>
                                    <div class="text-capitalize">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) ?>
                                    </div>
                                </div>
                            </div>
							<?php
						}

						$locations = get_the_terms( get_the_ID(), 'jobus_candidate_location' );
						if ( ! empty( $locations ) ) { ?>
                            <div class="col-md-6">
                                <div class="candidate-info mt-10">
                                    <span><?php esc_html_e( 'Location', 'jobus' ); ?></span>
									<?php
									foreach ( $locations as $location ) { ?>
                                        <div class="text-capitalize"><?php echo esc_html( $location->name ) ?></div>
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
								<?php esc_html_e( 'View Profile', 'jobus' ) ?>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="javascript:void(0)" class="msg-btn tran3s w-100 mt-5">
								<?php esc_html_e( 'Message', 'jobus' ) ?>
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