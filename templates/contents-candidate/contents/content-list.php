<?php
/**
 * Job List Five Column Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/contents/content-list-5-col.php.
 *
 * This template is used to display candidate listings in a list format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="accordion-box list-style">
	<?php
	while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
		$meta           = get_post_meta( get_the_ID(), 'jobus_meta_candidate_options', true );
		$post_favourite = $meta['post_favorite'] ?? '';
		$is_favourite   = ( $post_favourite == '1' ) ? ' favourite' : '';
        $save_candidate_status = jobus_get_save_status('', 'jobus_saved_candidates');
		?>
        <div class="candidate-profile-card<?php echo esc_attr( $is_favourite ) ?> list-layout jbs-mb-25">
            <div class="jbs-d-flex">
                <div class="candidate-avatar online jbs-position-relative jbs-d-block jbs-me-auto jbs-ms-auto">
                    <a href="<?php the_permalink() ?>" class="jbs-rounded-circle">
						<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-rounded-circle' ] ) ?>
                    </a>
                </div>
                <div class="right-side">
                    <div class="jbs-row  jbs-align-items-center">
                        <div class="jbs-col-lg-3">
                            <div class="jbs-position-relative">
                                <h4 class="candidate-name mb-0">
                                    <a href="<?php the_permalink() ?>" class="tran3s">
										<?php the_title() ?>
                                    </a>
                                </h4>

								<?php
								if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_1' ) ) {
									?>
                                    <div class="candidate-post jbs-text-capitalize">
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
									echo '<ul class="candidate-skills jbs-style-none jbs-d-flex jbs-align-items-center">';
									foreach ( $displayed_skills as $skill ) {
										echo '<li class="jbs-text-capitalize"><a href="' . esc_url( get_term_link($skill) ) . '">' . esc_html( $skill->name ) . '</a></li>';
									}

									// Display the count of remaining skills
									$remaining_count = count( $skills ) - $max_skills;
									echo '<li class="more">' . esc_html( $remaining_count ) . '+</li>';
									echo '</ul>';
								} else {
									// Display all skills
									if ( ! empty( $skills ) ) {
										echo '<ul class="candidate-skills jbs-style-none jbs-d-flex jbs-align-items-center">';
										foreach ( $skills as $skill ) {
											echo '<li class="jbs-text-capitalize"><a href="' . esc_url( get_term_link($skill) ) . '">' . esc_html( $skill->name ) . '</a></li>';
										}
										echo '</ul>';
									}
								}
								?>
                            </div>
                        </div>

						<?php
						if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) {
							?>
                            <div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
                                <div class="candidate-info">
                                    <span><?php echo esc_html( jobus_meta_candidate_spec_name( 2 ) ); ?></span>
                                    <div class="jbs-text-capitalize">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) ?>
                                    </div>
                                </div>
                            </div>
							<?php
						}
						if ( jobus_get_first_taxonomy_name( 'jobus_candidate_location' ) ) { ?>
                            <div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
                                <div class="candidate-info">
                                    <span><?php esc_html_e( 'Location', 'jobus' ); ?></span>
                                    <a href="<?php echo esc_url( jobus_get_first_taxonomy_link( 'jobus_candidate_location' ) ) ?>">
		                                <?php echo esc_html( jobus_get_first_taxonomy_name( 'jobus_candidate_location' ) ); ?>
                                    </a>
                                </div>
                            </div>
							<?php
						}
						?>
                        <div class="jbs-col-lg-3 jbs-col-md-4">
                            <div class="jbs-d-flex jbs-justify-content-lg-end">
                                <?php
                                if ( is_array($save_candidate_status) && isset($save_candidate_status['post_id']) ) {
                                    jobus_render_post_save_button( [
                                        'post_id'    => $save_candidate_status['post_id'],
                                        'post_type'  => 'jobus_candidate',
                                        'meta_key'   => 'jobus_saved_candidates',
                                        'is_saved'   => $save_candidate_status['is_saved'],
                                        'button_title' => !empty($save_candidate_status['is_saved']) ? esc_html__('Saved Candidate', 'jobus') : esc_html__('Save Candidate', 'jobus'),
                                        'class' => 'save-btn jbs-text-center jbs-rounded-circle tran3s mt-10 jobus-saved-post'
                                    ] );
                                }
                                ?>
                                <a href="<?php the_permalink() ?>" class="profile-btn tran3s jbs-ms-md-2 jbs-mt-10 sm-mt-20">
									<?php esc_html_e( 'View Profile', 'jobus' ) ?>
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