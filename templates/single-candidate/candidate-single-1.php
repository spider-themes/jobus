<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * The template for displaying titlebar
 *
 * @package jobi
 */
$meta           = get_post_meta( get_the_ID(), 'jobus_meta_candidate_options', true );
$post_author_id = get_post_field( 'post_author', get_the_ID() );
$banner_shape_1 = jobus_opt( 'banner_shape_1' );
$banner_shape_2 = jobus_opt( 'banner_shape_2' );

wp_enqueue_style( 'lightbox' );
wp_enqueue_script( 'lightbox' );
?>
<div class="inner-banner-one position-relative jobi-single-banner">
    <div class="container">
        <div class="position-relative">
            <div class="row">
                <div class="col-xl-8 m-auto text-center">
                    <h1 class="blog-heading"><?php the_title() ?></h1>
                    <div class="blog-pubish-date text-white mt-30 lg-mt-20">
                        <?php
                        if ( has_category() ) {
                            echo wp_kses_post( get_the_category_list( ', ' ) ) . ' . ';
                        }
                        ?>
                        <?php the_time( get_option( 'date_format' ) ) ?> .
                        <?php esc_html_e( 'By', 'jobus' ); ?>
                        <a href="<?php echo esc_url( get_author_posts_url( $post_author_id ) ) ?>">
                            <?php echo esc_html( get_the_author_meta( 'display_name', $post_author_id ) ) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ( jobus_opt( 'is_banner_shapes' ) ) {
        if ( ! empty( $banner_shape_1['id'] ) ) {
            echo wp_get_attachment_image( $banner_shape_1['id'], 'full', false, array( 'class' => 'lazy-img shapes shape_01' ) );
        }
        if ( ! empty( $banner_shape_2['id'] ) ) {
            echo wp_get_attachment_image( $banner_shape_2['id'], 'full', false, array( 'class' => 'lazy-img shapes shape_02' ) );
        }
    }
    ?>
</div>

<section class="candidates-profile pt-100 lg-pt-70 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">

            <div class="col-xxl-9 col-lg-8">
                <div class="candidates-profile-details me-xxl-5 pe-xxl-4">
                    <div class="inner-card border-style mb-65 lg-mb-40">
                        <?php the_content() ?>
                    </div>
                    <?php
                    if ( $video_url && $video_title && $video_bg_img ) { ?>
                        <h3 class="title"><?php echo esc_html( $video_title ) ?></h3>
                        <div class="video-post d-flex align-items-center justify-content-center mt-25 lg-mt-20 mb-75 lg-mb-50"
                             style="background-image: url(<?php echo esc_url( $video_bg_img ) ?>)">
                            <a class="fancybox rounded-circle video-icon tran3s text-center" data-fancybox=""
                               href="<?php echo esc_url( $video_url ) ?>">
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
                            <div class="time-line-data position-relative pt-15">
                                <?php
                                foreach ( $educations as $item ) {
                                    ?>
                                    <div class="info position-relative">
                                        <?php
                                        if ( ! empty( $item['sl_num'] ) ) { ?>
                                            <div class="numb fw-500 rounded-circle d-flex align-items-center justify-content-center"><?php echo esc_html( $item['sl_num'] ) ?></div>
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
                            <ul class="style-none skill-tags d-flex flex-wrap pb-25">
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
                            <div class="time-line-data position-relative pt-15">
                                <?php
                                foreach ( $experience as $item ) {
                                    ?>
                                    <div class="info position-relative">
                                        <?php
                                        if ( ! empty( $item['sl_num'] ) ) { ?>
                                            <div class="numb fw-500 rounded-circle d-flex align-items-center justify-content-center"><?php echo esc_html( $item['sl_num'] ) ?></div>
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
                                    <a href="<?php echo esc_url( $image_url ) ?> "
                                       class="example-image-link w-100 d-blok" data-lightbox="example-set">
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

            <div class="col-xxl-3 col-lg-4">
                <div class="candidate-profile-sidebar ms-xl-5 ms-xxl-0 md-mt-60">

                    <div class="candidate-bio bg-wrapper bg-color mb-60 md-mb-40">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="pt-25">
                                <div class="candidate-avatar m-auto">
                                    <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img rounded-circle w-100' ] ); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <h3 class="candidate-name text-center"><?php the_title() ?></h3>
                        <div class="text-center pb-25">
                            <a href="#" class="invite-btn fw-500">
                                <?php esc_html_e( 'Invite', 'jobus' ) ?>
                            </a>
                        </div>

                        <ul class="style-none">
                            <?php
                            if ( ! empty( $location ) ) { ?>
                                <li>
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
                                        $birthDate = DateTime::createFromFormat( 'Y-m-d', $candidate_age );
                                        if ( ! $birthDate ) {
                                            $birthDate = DateTime::createFromFormat( 'd/m/Y', $candidate_age );
                                        }
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
                                                echo '<div class="text-capitalize">';
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
                                                <a href="<?php echo esc_url( $item['url'] ) ?>" class="me-3">
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
                        <?php
                        if ( $cv_attachment ) {
                            $attachment_url = wp_get_attachment_url( $cv_attachment );
                            $clean_url      = preg_replace( '/\?.*/', '', $attachment_url );
                            ?>
                            <a href="<?php echo esc_url( $clean_url ); ?>"
                               class="btn-ten fw-500 text-white w-100 text-center tran3s mt-15" target="_blank">
                                <?php esc_html_e( 'Download CV', 'jobus' ); ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    $google_map = $meta['jobus_candidate_location'] ?? '';
                    if ( is_array( $google_map ) ) {
                        $latitude        = $google_map['latitude'] ?? '';
                        $longitude       = $google_map['longitude'] ?? '';
                        $address_encoded = urlencode( $google_map['address'] ); // URL encode the address for safety

                        $is_http    = is_ssl() ? 'https://' : 'http://';
                        $iframe_url = "{$is_http}maps.google.com/maps?q={$address_encoded}, {$latitude}, {$longitude}&z=12&output=embed";
                        ?>
                        <h4 class="sidebar-title"><?php esc_html_e( 'Location', 'jobus' ) ?></h4>
                        <div class="map-area mb-60 md-mb-40">
                            <div class="gmap_canvas h-100 w-100">
                                <iframe class="gmap_iframe h-100 w-100" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <h4 class="sidebar-title"><?php esc_html_e( 'Email to ', 'jobus' ) ?><?php the_title() ?></h4>
                    <div class="email-form bg-wrapper bg-color">
                        <p><?php esc_html_e( 'Your email address & profile will be shown to the recipient.', 'jobus' ) ?></p>
                        <form action="javascript:void(0)" name="candidate_email_from" id="candidate-email-from" method="post">
                            <?php wp_nonce_field( 'jobus_candidate_contact_mail_form', 'security' ); ?>
                            <input type="hidden" id="candidate-id" name="candidate_id" value="<?php echo esc_attr( get_the_ID() ); ?>">

                            <div class="d-sm-flex mb-25">
                                <input type="text" name="sender_name" id="sender-name" autocomplete="on"
                                       placeholder="<?php esc_attr_e( 'Name*', 'jobus' ) ?>" required>
                            </div>
                            <div class="d-sm-flex mb-25">
                                <input type="email" name="sender_email" id="sender-email"
                                       placeholder="<?php esc_attr_e( 'Email*', 'jobus' ) ?>" required>
                            </div>

                            <div class="d-sm-flex mb-25">
                                <input type="text" name="sender_subject" id="sender_subject"
                                       placeholder="<?php esc_attr_e( 'Subject', 'jobus' ) ?>">
                            </div>

                            <div class="d-sm-flex mb-25 xs-mb-10">
                                <textarea name="message" id="message" placeholder="<?php esc_attr_e( 'Message', 'jobus' ) ?>" required></textarea>
                            </div>

                            <div class="d-sm-flex">
                                <button type="submit" name="send_message" id="send_message"
                                        class="btn-ten fw-500 text-white flex-fill text-center tran3s">
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