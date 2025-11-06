<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

wp_enqueue_style( 'lightbox' );
wp_enqueue_script( 'lightbox' );
?>

<section class="candidates-profile-2 bg-color jbs-pt-180 jbs-lg-pt-70 jbs-pb-130 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-xxl-9 jbs-col-lg-8">
                <div class="candidates-profile-details-2 jbs-me-xxl-5 jbs-pe-xxl-4">
                    <?php
                    if ( ! empty( get_the_content() ) ) {
                        ?>
                        <div class="inner-card border-style jbs-mb-65 lg-jbs-mb-40">
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
                        <div class="video-post jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-mt-25 jbs-lg-mt-20 jbs-mb-75 jbs-lg-mb-50"
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
                        <div class="inner-card border-style jbs-mb-75 jbs-lg-mb-50">
                            <?php
                            if ( ! empty( $meta['education_title'] ) ) { ?>
                                <h3 class="title"><?php echo esc_html( $meta['education_title'] ) ?></h3>
                                <?php
                            }
                            ?>
                            <div class="time-line-data jbs-position-relative jbs-pt-15">
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
                        <div class="inner-card border-style jbs-mb-75 jbs-lg-mb-50">
                            <h3 class="title"><?php esc_html_e( 'Skills', 'jobus' ) ?></h3>
                            <ul class="jbs-style-none skill-tags jbs-d-flex jbs-flex-wrap jbs-pb-25">
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
                        <div class="inner-card border-style jbs-mb-60 jbs-lg-mb-50">
                            <?php
                            if ( ! empty( $meta['experience_title'] ) ) { ?>
                                <h3 class="title"><?php echo esc_html( $meta['experience_title'] ) ?></h3>
                                <?php
                            }
                            ?>
                            <div class="time-line-data jbs-position-relative jbs-pt-15">
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
                                            <div class="text_1 jbs-fw-500"><?php echo esc_html( $item['start_date'] ) ?>
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
?>
                    <div class="inner-card">
                    <?php
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
            </div>
            <!-- /.candidates-profile-details -->

            <div class="jbs-col-xxl-3 jbs-col-lg-4">
                <div class="candidate-profile-2-sidebar jbs-ms-xl-5 jbs-ms-xxl-0 md-mt-60">
                    <div class="candidate-bio bg-wrapper jbs-mb-60 jbs-md-mb-40">
                        <ul class="jbs-style-none">
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
                        <div class="map-area jbs-mb-60 md-jbs-mb-40">
                            <div class="gmap_canvas jbs-h-100 jbs-w-100">
                                <iframe class="gmap_iframe jbs-h-100 jbs-w-100 jbs-border-0" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <h4 class="sidebar-title"><?php esc_html_e( 'Email', 'jobus' ) ?><?php the_title() ?></h4>
                    <div class="email-form bg-wrapper">
                        <p><?php esc_html_e( 'Your email address & profile will be shown to the recipient.', 'jobus' ) ?></p>

                        <form action="javascript:void(0)" name="candidate_email_from" id="candidate-email-from" method="post">

                            <?php wp_nonce_field( 'jobus_candidate_contact_mail_form', 'security' ); ?>
                            <input type="hidden" id="candidate-id" name="candidate_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <div class="jbs-d-sm-flex jbs-mb-25 xs-jbs-mb-10">
                                <input type="text" name="sender_name" id="sender_name" placeholder="<?php esc_attr_e( 'Name*', 'jobus' ) ?>" required>
                            </div>

                            <div class="jbs-d-sm-flex jbs-mb-25">
                                <input type="email" name="sender_email" id="sender_email" placeholder="<?php esc_attr_e( 'Email*', 'jobus' ) ?>" required>
                            </div>

                            <div class="jbs-d-sm-flex jbs-mb-25">
                                <input type="text" name="sender_subject" id="sender_subject" placeholder="<?php esc_attr_e( 'Subject', 'jobus' ) ?>">
                            </div>

                            <div class="jbs-d-sm-flex jbs-mb-25 xs-mb-10">
                                <textarea name="message" id="message" placeholder="<?php esc_attr_e( 'Message', 'jobus' ) ?>" required></textarea>
                            </div>

                            <div class="jbs-d-sm-flex">
                                <button type="submit" name="send_message" class="jbs-btn-ten jbs-pointer jbs-fw-500 jbs-text-white jbs-flex-fill jbs-text-center tran3s">
                                    <?php esc_html_e( 'Send Message', 'jobus' ) ?>
                                </button>
                            </div>

                            <div id="email-form-message" class="email-form-message"></div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>