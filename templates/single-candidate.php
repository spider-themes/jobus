<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
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

                        $educations = !empty($meta['education']) ? $meta['education'] : '';
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
                        ?>

                        <!-- /.inner-card -->
                        <div class="inner-card border-style mb-75 lg-mb-50">
                            <h3 class="title"><?php esc_html_e('Skills', 'jobly') ?></h3>
                            <ul class="style-none skill-tags d-flex flex-wrap pb-25">

                                <?php
                                $meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
                                ?>


                                <li>Figma</li>
                                <li>HTML5</li>
                                <li>Illustrator</li>
                                <li>Adobe Photoshop</li>
                                <li>WordPress</li>
                                <li>jQuery</li>
                                <li>Web Design</li>
                                <li>Adobe XD</li>
                                <li>CSS</li>
                                <li class="more">3+</li>
                            </ul>
                        </div>


                        <?php
                        $experience = !empty($meta['experience']) ? $meta['experience'] : '';
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


                        $portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
                        $portfolio_ids = explode(',', $portfolio);

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
                                    <img src="images/lazy.svg" data-src="images/candidates/img_01.jpg" alt="" class="lazy-img rounded-circle w-100">
                                </div>
                            </div>
                            <h3 class="cadidate-name text-center"><?php the_title() ?></h3>
                            <div class="text-center pb-25">
                                <a href="#" class="invite-btn fw-500"><?php esc_html_e( 'Invite', 'jobly' ) ?></a>
                            </div>
                            <ul class="style-none">
                                <?php
                                $specifications = jobly_opt('candidate_specifications');
                                if ($specifications) {
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
                            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-15">Download CV</a>
                        </div>

                        <!-- /.cadidate-bio -->
                        <h4 class="sidebar-title">Location</h4>
                        <div class="map-area mb-60 md-mb-40">
                            <div class="gmap_canvas h-100 w-100">
                                <iframe class="gmap_iframe h-100 w-100" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=dhaka collage&amp;t=&amp;z=12&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                            </div>
                        </div>
                        <h4 class="sidebar-title">Email Rashed Kabir.</h4>
                        <div class="email-form bg-wrapper bg-color">
                            <p>Your email address & profile will be shown to the recipient.</p>
                            <form action="#">
                                <div class="d-sm-flex mb-25">
                                    <label for="">Name</label>
                                    <input type="text">
                                </div>
                                <div class="d-sm-flex mb-25">
                                    <label for="">Email</label>
                                    <input type="email">
                                </div>
                                <div class="d-sm-flex mb-25 xs-mb-10">
                                    <label for="">Message</label>
                                    <textarea></textarea>
                                </div>
                                <div class="d-sm-flex">
                                    <label for=""></label>
                                    <button class="btn-ten fw-500 text-white flex-fill text-center tran3s">Send </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.row -->
        </div>
    </section>

<?php

get_footer();