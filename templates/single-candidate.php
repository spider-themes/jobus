<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
$experience = !empty($meta['experience']) ? $meta['experience'] : '';
$educations = !empty($meta['education']) ? $meta['education'] : '';
$cv_attachment = !empty($meta['cv_attachment']) ? $meta['cv_attachment'] : '';

$portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
$portfolio_ids = explode(',', $portfolio);

$skills = get_terms( array(
        'taxonomy' => 'candidate_skill'
    )
);
?>

    <section class="candidates-profile pt-100 lg-pt-70 pb-150 lg-pb-80">
        <div class="container">
            <div class="row">
                <div class="col-xxl-9 col-lg-8">
                    <div class="candidates-profile-details me-xxl-5 pe-xxl-4">
                        <div class="inner-card border-style mb-65 lg-mb-40">
                            <?php the_content() ?>
                        </div>
                        <?php
                        if ( !empty($meta['video_url']) ) { ?>
                            <h3 class="title"><?php esc_html_e('Intro', 'jobly') ?></h3>
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
                                <h3 class="title"><?php esc_html_e('Education', 'jobly') ?></h3>
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
                                <h3 class="title"><?php esc_html_e('Work Experience', 'jobly') ?></h3>
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
                            ?>
                            <h3 class="title"><?php esc_html_e('Portfolio', 'jobly') ?></h3>
                            <div class="candidate-portfolio-slider">
                                <?php
                                foreach ( $portfolio_ids as $item ) {
                                    ?>
                                    <div class="item">
                                        <a href="#" class="w-100 d-blok">
                                            <?php echo wp_get_attachment_image($item, 'full', '', ['class' => 'w-100']) ?>
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
                    <div class="cadidate-profile-sidebar ms-xl-5 ms-xxl-0 md-mt-60">
                        <div class="cadidate-bio bg-wrapper bg-color mb-60 md-mb-40">
                            <div class="pt-25">
                                <div class="cadidate-avatar m-auto">
                                    <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle w-100']) ?>
                                </div>
                            </div>
                            <h3 class="cadidate-name text-center"><?php the_title() ?></h3>
                            <div class="text-center pb-25">
                                <a href="#" class="invite-btn fw-500">
                                    <?php esc_html_e( 'Invite', 'jobly' ) ?>
                                </a>
                            </div>

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

                                        if (isset($meta_options[ $meta_key ])) {
                                            ?>
                                            <li>
                                                <?php
                                                if (isset($meta_options[ $meta_key ]) && !empty($meta_options[ $meta_key ])) {
                                                    echo '<span>' . esc_html($meta_name) . ':</span>';
                                                }
                                                if (!empty($meta_options[ $meta_key ] && is_array($meta_options[ $meta_key ]))) {
                                                    echo '<div>';
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
                        $location = !empty($meta['candidate_location']) ? $meta['candidate_location'] : '';
                        $is_http = is_ssl() ? 'https://' : 'http://';
                        $iframe_url = "{$is_http}maps.google.com/maps?q={$location['latitude']},{$location['longitude']}&z=12&output=embed";

                        if ( $location ) {
                            ?>
                            <h4 class="sidebar-title"><?php esc_html_e('Location', 'jobly') ?></h4>
                            <div class="map-area mb-60 md-mb-40">
                                <div class="gmap_canvas h-100 w-100">
                                    <iframe class="gmap_iframe h-100 w-100" src="<?php echo esc_url($iframe_url); ?>"></iframe>
                                </div>
                            </div>
                            <?php
                        }

                        // Handle form submission
                        if (isset($_POST['send_message'])) {


                            // Retrieve form field values
                            $sender_name = !empty($_POST['sender_name']) ? sanitize_text_field($_POST['sender_name']) : '';
                            $sender_email = !empty($_POST['sender_email']) ? sanitize_email($_POST['sender_email']) : '';
                            $sender_subject = !empty($_POST['sender_subject']) ? sanitize_text_field($_POST['sender_subject']) : '';
                            $message = !empty($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

                            // Set email subject
                            $subject = !empty($sender_subject) ? $sender_subject : esc_html__('New Message', 'jobly');

                            // Set email headers
                            $headers[] = "From: $sender_name <$sender_email>";
                            $headers[] = "Reply-To: $sender_email";

                            $candidate_mail = !empty($meta['candidate_mail']) ? $meta['candidate_mail'] : '';

                            // Send email using SMTP
                            $sent = wp_mail($candidate_mail, $subject, $message, $headers);

                            if ($sent) {
                                // Email sent successfully
                                $mail_text = 'Email sent successfully.';
                            } else {
                                // Email sending failed
                                $mail_text = 'Failed to send email.';
                                // Log error details
                                if (isset($GLOBALS['phpmailer']->ErrorInfo)) {
                                    $mail_text .= ' Error: ' . $GLOBALS['phpmailer']->ErrorInfo;
                                }
                            }

                            // Output debugging information
                            echo '<pre>';
                            print_r($mail_text);
                            echo '</pre>';
                        }
                        ?>
                        <h4 class="sidebar-title"><?php esc_html_e('Email', 'jobly') ?> <?php the_title() ?></h4>
                        <div class="email-form bg-wrapper bg-color">
                            <p><?php esc_html_e('Your email address & profile will be shown to the recipient.', 'jobly') ?></p>

                            <form action="<?php echo esc_url(get_the_permalink()) ?>" method="post">
                                <div class="d-sm-flex mb-25">
                                    <input type="text" name="sender_name" id="sender_name" placeholder="<?php esc_attr_e('Your Name*', 'jobly') ?>" required>
                                </div>
                                <div class="d-sm-flex mb-25">
                                    <input type="email" name="sender_email" id="sender_email" placeholder="<?php esc_attr_e('Your Email*', 'jobly') ?>" required>
                                </div>

                                <div class="d-sm-flex mb-25">
                                    <input type="text" name="sender_subject" id="sender_subject" placeholder="<?php esc_attr_e('Your Subject', 'jobly') ?>">
                                </div>

                                <div class="d-sm-flex mb-25 xs-mb-10">
                                    <textarea name="message" id="message" placeholder="<?php esc_attr_e('Your Message', 'jobly') ?>"></textarea>
                                </div>
                                <div class="d-sm-flex">
                                    <button type="submit" name="send_message" class="btn-ten fw-500 text-white flex-fill text-center tran3s">
                                        <?php esc_html_e('Send Message', 'jobly') ?>
                                    </button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

<?php

get_footer();