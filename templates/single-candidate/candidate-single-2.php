<?php
wp_enqueue_style('lightbox');
wp_enqueue_script('lightbox');
?>

<div class="inner-banner-one position-relative">
    <div class="container">
        <div class="candidate-profile-card candidate-profile-two list-layout">
            <div class="d-flex align-items-start align-items-xl-center">
                <div class="cadidate-avatar position-relative d-block me-auto ms-auto">
                    <a href="<?php the_permalink() ?>" class="rounded-circle">
		                <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                    </a>
                </div>
                <div class="right-side">
                    <div class="row gx-1 align-items-center">
                        <div class="col-xl-2 order-xl-0">
                            <div class="position-relative">
                                <h4 class="candidate-name text-white mb-0"><?php the_title() ?></h4>
                                <div class="andidate-post"><?php esc_html('Intro') ?></div>
                                <?php if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1' ) ) : ?>
                                    <div class="candidate-post"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-xl-3 order-xl-3">
                            <?php
                            $skills = get_the_terms(get_the_ID(), 'candidate_skill');
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
                                echo '<li class="more">' . $remaining_count . '+</li>';
                                echo '</ul>';
                            } else {
                            // Display all skills
                            echo '<ul class="cadidate-skills style-none d-flex align-items-center">';
                                foreach ($skills as $skill) {
                                echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </div>

                        <?php
                        if (jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2') ) {
                            ?>
                            <div class="col-xl-2 col-md-4 order-xl-1">
                                <div class="candidate-info">
                                    <span><?php echo jobly_meta_candidate_spec_name(2); ?></span>
                                    <div class="text-capitalize"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2') ?></div>
                                </div>
                            </div>
                            <?php
                        }
                        if (jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_3') ) {
                            ?>
                            <div class="col-xl-2 col-md-4 order-xl-2">
                                <div class="candidate-info">
                                    <span><?php echo jobly_meta_candidate_spec_name(3); ?></span>
                                    <div class="text-capitalize"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_3') ?></div>
                                </div>
                            </div>
                            <?php
                        }

                        if ( $cv_attachment) {
                            ?>
                            <div class="col-xl-3 col-md-4 order-xl-4">
                                <div class="d-flex justify-content-md-end">
                                    <a href="<?php echo esc_url($cv_attachment) ?>" class="cv-download-btn fw-500 tran3s ms-md-3 sm-mt-20" target="_blank">
                                        <?php esc_html_e('Download CV', 'jobly') ?>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <img src="images/shape/shape_02.svg" alt="" class="lazy-img shapes shape_01" style="">
    <img src="images/shape/shape_03.svg" alt="" class="lazy-img shapes shape_02" style="">
</div>

<section class="candidates-profile-2 bg-color pt-100 lg-pt-70 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-lg-8">
                <div class="candidates-profile-details-2 me-xxl-5 pe-xxl-4">
                    <div class="inner-card border-style mb-65 lg-mb-40">
		                <?php the_content() ?>
                    </div>
                    <?php
                    if ( !empty($meta['video_url']) ) {
                        if ( !empty($meta['video_title']) ) { ?>
                            <h3 class="title"><?php echo esc_html($meta['video_title']) ?></h3>
                            <?php
                        }
                        ?>
                        <div class="video-post d-flex align-items-center justify-content-center mt-25 lg-mt-20 mb-75 lg-mb-50" style="background-image: url(<?php echo esc_url($meta['bg_img']['url']) ?>)">
                            <a class="fancybox rounded-circle video-icon tran3s text-center" data-fancybox="" href="<?php echo esc_url($meta['video_url']) ?>">
                                <i class="bi bi-play"></i>
                            </a>
                        </div>
                        <?php
                    }

                    if ( $educations ) {
                        ?>
                        <div class="inner-card border-style mb-75 lg-mb-50">
                            <?php
                            if ( !empty($meta['education_title']) ) {?>
                                <h3 class="title"><?php echo esc_html($meta['education_title']) ?></h3>
                                <?php
                            }
                            ?>
                            <div class="time-line-data position-relative pt-15">
                                <?php
                                foreach ( $educations as $item ) {
                                    ?>
                                    <div class="info position-relative">
                                        <?php
                                        if ( !empty($item['sl_num']) ) { ?>
                                            <div class="numb fw-500 rounded-circle d-flex align-items-center justify-content-center"><?php echo esc_html($item['sl_num']) ?></div>
                                            <?php
                                        }
                                        if ( !empty($item['title']) ) { ?>
                                            <div class="text_1 fw-500"><?php echo esc_html($item['title']) ?></div>
                                            <?php
                                        }
                                        if ( !empty($item['academy']) ) { ?>
                                            <h4><?php echo esc_html($item['academy']) ?></h4>
                                            <?php
                                        }
                                        if ( !empty($item['description']) ) { ?>
                                            <?php echo wp_kses_post(wpautop($item['description'])) ?>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }

                    if ( is_array($skills )) {
                    ?>
                    <div class="inner-card border-style mb-75 lg-mb-50">
                        <h3 class="title"><?php esc_html_e('Skills', 'jobly') ?></h3>
                        <ul class="style-none skill-tags d-flex flex-wrap pb-25">
                            <?php
                            foreach( $skills as $skill ) {
                                echo '<li>'.esc_html($skill->name).'</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                    }

                    if ( $experience ) {
                    ?>
                    <div class="inner-card border-style mb-60 lg-mb-50">
                        <?php
                        if ( !empty($meta['experience_title']) ) {?>
                            <h3 class="title"><?php echo esc_html($meta['experience_title']) ?></h3>
                            <?php
                        }
                        ?>
                        <div class="time-line-data position-relative pt-15">
                            <?php
                            foreach ( $experience as $item ) {
                                ?>
                                <div class="info position-relative">
                                    <?php
                                    if ( !empty($item['sl_num']) ) { ?>
                                        <div class="numb fw-500 rounded-circle d-flex align-items-center justify-content-center"><?php echo esc_html($item['sl_num']) ?></div>
                                        <?php
                                    }
                                    if ( !empty($item['start_date']) ) { ?>
                                        <div class="text_1 fw-500"><?php echo esc_html($item['start_date']) ?> - <?php echo esc_html($item['end_date']) ?></div>
                                        <?php
                                    }
                                    if ( !empty($item['title']) ) { ?>
                                        <h4><?php echo esc_html($item['title']) ?></h4>
                                        <?php
                                    }
                                    if ( !empty($item['description']) ) { ?>
                                        <?php echo wp_kses_post(wpautop($item['description'])) ?>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    }

                    if ( $portfolio_ids ) {
                        if ( !empty($meta['portfolio_title']) ) {?>
                            <h3 class="title"><?php echo esc_html($meta['portfolio_title']) ?></h3>
                            <?php
                        }
	                    ?>
                        <div class="candidate-portfolio-slider" data-rtl="<?php echo esc_attr(spel_rtl()) ?>">
		                    <?php
		                    foreach ( $portfolio_ids as $item ) {
			                    $image_url = wp_get_attachment_image_url($item, 'full')
			                    ?>
                                <div class="item">
                                    <a href="<?php echo esc_url($image_url) ?> " class="example-image-link w-100 d-blok" data-lightbox="example-set">
					                    <?php echo wp_get_attachment_image($item, 'jobly_280x268') ?>
                                    </a>
                                </div>
			                    <?php
		                    }
		                    ?>
                        </div>
	                    <?php
                    }
                    ?>
                </div>
            </div>
            <!-- /.candidates-profile-details -->

            <div class="col-xxl-3 col-lg-4">
                <div class="cadidate-profile-2-sidebar ms-xl-5 ms-xxl-0 md-mt-60">
                    <div class="cadidate-bio bg-wrapper bg-color mb-60 md-mb-40">
				        <?php
				        $specifications = jobly_opt('candidate_specifications');
				        if ($specifications) {
					        ?>
                            <ul class="style-none">
						        <?php
						        foreach ( $specifications as $field ) {
							        $meta_name = $field[ 'meta_name' ] ?? '';
							        $meta_key = $field[ 'meta_key' ] ?? '';

							        // Get the stored meta-values
							        $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);



							        if ( isset($meta_options[ $meta_key ]) && !empty($meta_options[ $meta_key ]) ) {
								        ?>
                                        <li>
									        <?php
									        if (isset($meta_options[ $meta_key ]) && !empty($meta_options[ $meta_key ])) {
										        echo '<span>' . esc_html($meta_name) . ':</span>';
									        }
									        if (!empty($meta_options[ $meta_key ] && is_array($meta_options[ $meta_key ]))) {
										        echo '<div class="text-capitalize">';
										        foreach ( $meta_options[ $meta_key ] as $value ) {
											        $trim_value = str_replace('@space@', ' ', $value);
											        echo esc_html($trim_value);
										        }
										        echo '</div>';
									        }
									        ?>
                                        </li>
								        <?php
							        }
						        }

						        $social_icons = !empty($meta['social_icons']) ? $meta['social_icons'] : '';
						        if (is_array($social_icons)) {
							        ?>
                                    <li>
                                        <span><?php esc_html_e('Social: ', 'jobly'); ?></span>
                                        <div>
									        <?php
									        foreach ( $social_icons as $item ) {
										        if (!empty($item[ 'url' ])) { ?>
                                                    <a href="<?php echo esc_url($item[ 'url' ]) ?>" class="me-3">
                                                        <i class="<?php echo esc_attr($item[ 'icon' ]) ?>"></i>
                                                    </a>
											        <?php
										        }
									        }
									        ?>
                                        </div>
                                    </li>
							        <?php
						        }
						        ?>
                            </ul>
                            <a href="<?php echo esc_url($cv_attachment) ?>" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-15" target="_blank">
						        <?php esc_html_e('Download CV', 'jobly') ?>
                            </a>
					        <?php
				        }
				        ?>
                    </div>

			        <?php
                    $location = $meta['candidate_location'] ?? '';

                    if ( is_array($location) ) {
                        $latitude = $location['latitude'] ?? '';
                        $longitude = $location['longitude'] ?? '';
                        $address_encoded = urlencode($location['address']); // URL encode the address for safety

                        $is_http = is_ssl() ? 'https://' : 'http://';
                        $iframe_url = "{$is_http}maps.google.com/maps?q={$address_encoded}, {$latitude}, {$longitude}&z=12&output=embed";
				        ?>
                        <h4 class="sidebar-title"><?php esc_html_e('Location', 'jobly') ?></h4>
                        <div class="map-area mb-60 md-mb-40">
                            <div class="gmap_canvas h-100 w-100">
                                <iframe class="gmap_iframe h-100 w-100" src="<?php echo esc_url($iframe_url); ?>"></iframe>
                            </div>
                        </div>
				        <?php
			        }
			        ?>

                    <h4 class="sidebar-title"><?php esc_html_e('Email', 'jobly') ?> <?php the_title() ?></h4>
                    <div class="email-form bg-wrapper bg-color">
                        <p><?php esc_html_e('Your email address & profile will be shown to the recipient.', 'jobly') ?></p>

                        <form action="javascript:void(0)" name="candidate_email_from" id="candidate_email_from" method="post">

                            <?php wp_nonce_field( 'jobly_candidate_contact_mail_form', 'security' ); ?>
                            <input type="hidden" id="candidate_id" name="candidate_id" value="<?php echo get_the_ID(); ?>">

                            <div class="d-sm-flex mb-25">
                                <input type="text" name="sender_name" id="sender_name" placeholder="<?php esc_attr_e('Name*', 'jobly') ?>" required>
                            </div>

                            <div class="d-sm-flex mb-25">
                                <input type="email" name="sender_email" id="sender_email" placeholder="<?php esc_attr_e('Email*', 'jobly') ?>" required>
                            </div>

                            <div class="d-sm-flex mb-25">
                                <input type="text" name="sender_subject" id="sender_subject" placeholder="<?php esc_attr_e('Subject', 'jobly') ?>">
                            </div>

                            <div class="d-sm-flex mb-25 xs-mb-10">
                                <textarea name="message" id="message" placeholder="<?php esc_attr_e('Message', 'jobly') ?>"></textarea>
                            </div>

                            <div class="d-sm-flex">
                                <button type="submit" name="send_message" class="btn-ten fw-500 text-white flex-fill text-center tran3s">
							        <?php esc_html_e('Send Message', 'jobly') ?>
                                </button>
                            </div>

                            <div id="email-form-message" class="email-form-message"></div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</section>