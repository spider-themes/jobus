<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

wp_enqueue_style( 'lightbox' );
wp_enqueue_script( 'lightbox' );
?>

<div class="inner-banner-one">
    <div class="jbs-container">
        <div class="candidate-profile-card candidate-profile-two list-layout">
            <div class="jbs-d-flex jbs-align-items-start jbs-align-items-xl-center">
                <div class="candidate-avatar jbs-position-relative jbs-d-block jbs-me-auto jbs-ms-auto">
                    <a href="<?php the_permalink() ?>" class="jbs-rounded-circle">
                        <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-rounded-circle' ] ) ?>
                    </a>
                </div>
                <div class="right-side">
                    <div class="jbs-row jbs-gx-1 jbs-align-items-center">
                        <div class="jbs-col-xl-2 jbs-order-xl-0">
                            <div class="jbs-position-relative">
                                <h4 class="candidate-name jbs-text-white mb-0"><?php the_title() ?></h4>
                                <div class="andidate-post"><?php esc_html( 'Intro' ) ?></div>
                                <?php if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_1' ) ) : ?>
                                    <div class="candidate-post">
                                        <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_1' ) ) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="jbs-col-xl-3 jbs-order-xl-3">
                            <?php
                            $max_skills = 2;

                            if ( $skills && count( $skills ) > $max_skills ) {
                                // Shuffle the skills to get a random order
                                shuffle( $skills );

                                // Display the first 2 skills
                                $displayed_skills = array_slice( $skills, 0, $max_skills );
                                echo '<ul class="candidate-skills style-none jbs-d-flex jbs-align-items-center">';
                                foreach ( $displayed_skills as $skill ) {
                                    echo '<li class="jbs-text-capitalize">' . esc_html( $skill->name ) . '</li>';
                                }

                                // Display the count of remaining skills
                                $remaining_count = count( $skills ) - $max_skills;
                                echo '<li class="more">' . esc_html( $remaining_count ) . '+</li>';
                                echo '</ul>';
                            } else {
                                // Display all skills
                                if ( ! empty( $skills ) ) {
                                    echo '<ul class="candidate-skills style-none jbs-d-flex jbs-align-items-center">';
                                    foreach ( $skills as $skill ) {
                                        echo '<li class="jbs-text-capitalize">' . esc_html( $skill->name ) . '</li>';
                                    }
                                    echo '</ul>';
                                }
                            }
                            ?>
                        </div>

                        <?php
                        if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) {
                            ?>
                            <div class="jbs-col-xl-2 jbs-col-md-4 jbs-order-xl-1">
                                <div class="candidate-info">
                                    <span><?php echo esc_html( jobus_meta_candidate_spec_name( 2 ) ); ?></span>
                                    <div class="jbs-text-capitalize">
                                        <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_2' ) ) ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        if ( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_3' ) ) {
                            ?>
                            <div class="jbs-col-xl-2 jbs-col-md-4 jbs-order-xl-2">
                                <div class="candidate-info">
                                    <span><?php echo esc_html( jobus_meta_candidate_spec_name( 3 ) ); ?></span>
                                    <div class="jbs-text-capitalize">
                                        <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_candidate_options', 'candidate_archive_meta_3' ) ) ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        if ( $cv_attachment ) {
                            ?>
                            <div class="jbs-col-xl-3 jbs-col-md-4 jbs-order-xl-4">
                                <div class="jbs-d-flex jbs-justify-content-md-end">
                                    <a href="<?php echo esc_url( $cv_attachment ) ?>" class="cv-download-btn fw-500 tran3s ms-md-3 sm-mt-20" target="_blank">
                                        <?php esc_html_e( 'Download CV', 'jobus' ) ?>
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
</div>

<section class="candidates-profile-2 bg-color pt-100 lg-pt-70 pb-130 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-xxl-9 jbs-col-lg-8">
                <div class="candidates-profile-details-2 jbs-me-xxl-5 jbs-pe-xxl-4">
                    <?php
                    if ( ! empty( get_the_content() ) ) {
                        ?>
                        <div class="inner-card border-style mb-65 lg-mb-40">
                            <?php the_content() ?>
                        </div>
                        <?php
                    }

                    if ( ! empty( $meta['video_url'] ) ) {
                        if ( ! empty( $meta['video_title'] ) ) { ?>
                            <h3 class="title"><?php echo esc_html( $meta['video_title'] ) ?></h3>
                            <?php
                        }
                        ?>
                        <div class="video-post jbs-d-flex jbs-align-items-center jbs-justify-content-center mt-25 lg-mt-20 mb-75 lg-mb-50"
                             style="background-image: url(<?php echo esc_url( $meta['video_bg_img']['url'] ) ?>)">
                            <a class="fancybox jbs-rounded-circle video-icon tran3s jbs-text-center" data-fancybox=""
                               href="<?php echo esc_url( $meta['video_url'] ) ?>">
                                <i class="bi bi-play"></i>
                            </a>
                        </div>
                        <?php
                    }

                    if ( $educations ) {
                        ?>
                        <div class="inner-card border-style mb-75 lg-mb-50">
                            <?php
                            if ( ! empty( $meta['education_title'] ) ) { ?>
                                <h3 class="title"><?php echo esc_html( $meta['education_title'] ) ?></h3>
                                <?php
                            }
                            ?>
                            <div class="time-line-data jbs-position-relative pt-15">
                                <?php
                                foreach ( $educations as $item ) {
                                    ?>
                                    <div class="info jbs-position-relative">
                                        <?php
                                        if ( ! empty( $item['sl_num'] ) ) { ?>
                                            <div class="numb fw-500 jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center"><?php echo esc_html( $item['sl_num'] ) ?></div>
                                            <?php
                                        }
                                        if ( ! empty( $item['title'] ) ) { ?>
                                            <div class="text_1 fw-500"><?php echo esc_html( $item['title'] ) ?></div>
                                            <?php
                                        }
                                        if ( ! empty( $item['academy'] ) ) { ?>
                                            <h4><?php echo esc_html( $item['academy'] ) ?></h4>
                                            <?php
                                        }
                                        if ( ! empty( $item['description'] ) ) { ?>
                                            <?php echo wp_kses_post( wpautop( $item['description'] ) ) ?>
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

                    if ( is_array( $skills ) ) {
                        ?>
                        <div class="inner-card border-style mb-75 lg-mb-50">
                            <h3 class="title"><?php esc_html_e( 'Skills', 'jobus' ) ?></h3>
                            <ul class="style-none skill-tags jbs-d-flex jbs-flex-wrap pb-25">
                                <?php
                                foreach ( $skills as $skill ) {
                                    echo '<li>' . esc_html( $skill->name ) . '</li>';
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
                            if ( ! empty( $meta['experience_title'] ) ) { ?>
                                <h3 class="title"><?php echo esc_html( $meta['experience_title'] ) ?></h3>
                                <?php
                            }
                            ?>
                            <div class="time-line-data jbs-position-relative pt-15">
                                <?php
                                foreach ( $experience as $item ) {
                                    ?>
                                    <div class="info jbs-position-relative">
                                        <?php
                                        if ( ! empty( $item['sl_num'] ) ) { ?>
                                            <div class="numb fw-500 jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center"><?php echo esc_html( $item['sl_num'] ) ?></div>
                                            <?php
                                        }
                                        if ( ! empty( $item['start_date'] ) ) { ?>
                                            <div class="text_1 fw-500"><?php echo esc_html( $item['start_date'] ) ?>
                                                - <?php echo esc_html( $item['end_date'] ) ?></div>
                                            <?php
                                        }
                                        if ( ! empty( $item['title'] ) ) { ?>
                                            <h4><?php echo esc_html( $item['title'] ) ?></h4>
                                            <?php
                                        }
                                        if ( ! empty( $item['description'] ) ) { ?>
                                            <?php echo wp_kses_post( wpautop( $item['description'] ) ) ?>
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
                        if ( ! empty( $meta['portfolio_title'] ) ) { ?>
                            <h3 class="title"><?php echo esc_html( $meta['portfolio_title'] ) ?></h3>
                            <?php
                        }
                        ?>
                        <div class="candidate-portfolio-slider" data-rtl="<?php echo esc_attr( jobus_rtl() ) ?>">
                            <?php
                            foreach ( $portfolio_ids as $item ) {
                                $image_url = wp_get_attachment_image_url( $item, 'full' )
                                ?>
                                <div class="item">
                                    <a href="<?php echo esc_url( $image_url ) ?> " class="example-image-link jbs-w-100 jbs-d-block"
                                       data-lightbox="example-set">
                                        <?php echo wp_get_attachment_image( $item, 'jobus_280x268' ) ?>
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

            <div class="jbs-col-xxl-3 jbs-col-lg-4">
                <div class="candidate-profile-2-sidebar jbs-ms-xl-5 jbs-ms-xxl-0 md-mt-60">
                    <div class="candidate-bio bg-wrapper bg-color mb-60 md-mb-40">
                        <ul class="style-none">
                            <?php
                            if ( ! empty( $location ) ) { ?>
                                <li class="jbs-border-0">
                                    <span><?php esc_html_e( 'Location: ', 'jobus' ); ?></span>
                                    <div><?php echo esc_html( $location ) ?></div>
                                </li>
                                <?php
                            }

                            if ( $candidate_age ) { ?>
                                <li>
                                    <span><?php esc_html_e( 'Age: ', 'jobus' ); ?></span>
                                    <div>
                                        <?php
                                        $birthDate = DateTime::createFromFormat( 'd/m/Y', $candidate_age );
                                        if ( $birthDate ) {
                                            $today = new DateTime( 'now' );
                                            $age   = $today->diff( $birthDate )->y;
                                            echo esc_html( $age );
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?php
                            }

                            if ( $candidate_mail ) { ?>
                                <li>
                                    <span><?php esc_html_e( 'Email: ', 'jobus' ); ?></span>
                                    <div>
                                        <a href="mailto:<?php echo esc_attr( $meta['candidate_mail'] ) ?>">
                                            <?php echo esc_html( $meta['candidate_mail'] ) ?>
                                        </a>
                                    </div>
                                </li>
                                <?php
                            }

                            if ( ! empty( $specifications ) ) {
                                foreach ( $specifications as $field ) {
                                    $meta_name = $field['meta_name'] ?? '';
                                    $meta_key  = $field['meta_key'] ?? '';

                                    // Get the stored meta-values
                                    $meta_options = get_post_meta( get_the_ID(), 'jobus_meta_candidate_options', true );

                                    if ( ! empty( $meta_options[ $meta_key ] ) ) {
                                        ?>
                                        <li>
                                            <?php
                                            if ( ! empty( $meta_options[ $meta_key ] ) ) {
                                                echo '<span>' . esc_html( $meta_name ) . ':</span>';
                                            }
                                            if ( ! empty( is_array( $meta_options[ $meta_key ] ) ) ) {
                                                echo '<div class="jbs-text-capitalize">';
                                                foreach ( $meta_options[ $meta_key ] as $value ) {
                                                    $trim_value = str_replace( '@space@', ' ', $value );
                                                    echo esc_html( $trim_value );
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                }
                            }

                            if ( $candidate_specifications ) {
                                foreach ( $candidate_specifications as $specification ) {
                                    if ( ! empty( $specification['title'] ) && ! empty( $specification['value'] ) ) { ?>
                                        <li>
                                            <span><?php echo esc_html( $specification['title'] ); ?>: </span>
                                            <div><?php echo esc_html( $specification['value'] ); ?></div>
                                        </li>
                                        <?php
                                    }
                                }
                            }

                            if ( $social_icons ) {
                                ?>
                                <li>
                                    <span><?php esc_html_e( 'Social: ', 'jobus' ); ?></span>
                                    <div>
                                        <?php
                                        foreach ( $social_icons as $item ) {
                                            if ( ! empty( $item['url'] ) ) { ?>
                                                <a href="<?php echo esc_url( $item['url'] ) ?>" class="jbs-me-3">
                                                    <i class="<?php echo esc_attr( $item['icon'] ) ?>"></i>
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
                    </div>

                    <?php
                    $location = $meta['jobus_candidate_location'] ?? '';

                    if ( is_array( $location ) ) {
                        $latitude        = $location['latitude'] ?? '';
                        $longitude       = $location['longitude'] ?? '';
                        $address_encoded = urlencode( $location['address'] ); // URL encode the address for safety

                        $is_http    = is_ssl() ? 'https://' : 'http://';
                        $iframe_url = "{$is_http}maps.google.com/maps?q={$address_encoded}, {$latitude}, {$longitude}&z=12&output=embed";
                        ?>
                        <h4 class="sidebar-title"><?php esc_html_e( 'Location', 'jobus' ) ?></h4>
                        <div class="map-area mb-60 md-mb-40">
                            <div class="gmap_canvas jbs-h-100 jbs-w-100">
                                <iframe class="gmap_iframe jbs-h-100 jbs-w-100" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <h4 class="sidebar-title"><?php esc_html_e( 'Email', 'jobus' ) ?><?php the_title() ?></h4>
                    <div class="email-form bg-wrapper bg-color">
                        <p><?php esc_html_e( 'Your email address & profile will be shown to the recipient.', 'jobus' ) ?></p>

                        <form action="javascript:void(0)" name="candidate_email_from" id="candidate-email-from" method="post">

                            <?php wp_nonce_field( 'jobus_candidate_contact_mail_form', 'security' ); ?>
                            <input type="hidden" id="candidate-id" name="candidate_id" value="<?php echo esc_attr( get_the_ID() ); ?>">

                            <div class="jbs-d-sm-flex mb-25">
                                <input type="text" name="sender_name" id="sender_name" placeholder="<?php esc_attr_e( 'Name*', 'jobus' ) ?>" required>
                            </div>

                            <div class="jbs-d-sm-flex mb-25">
                                <input type="email" name="sender_email" id="sender_email" placeholder="<?php esc_attr_e( 'Email*', 'jobus' ) ?>" required>
                            </div>

                            <div class="jbs-d-sm-flex mb-25">
                                <input type="text" name="sender_subject" id="sender_subject" placeholder="<?php esc_attr_e( 'Subject', 'jobus' ) ?>">
                            </div>

                            <div class="jbs-d-sm-flex mb-25 xs-mb-10">
                                <textarea name="message" id="message" placeholder="<?php esc_attr_e( 'Message', 'jobus' ) ?>" required></textarea>
                            </div>

                            <div class="jbs-d-sm-flex">
                                <button type="submit" name="send_message" class="jbs-btn-ten fw-500 jbs-text-white jbs-flex-fill jbs-text-center tran3s">
									<?php esc_html_e( 'Send Message', 'jobus' ) ?>
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