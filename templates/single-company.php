<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
$website = $meta[ 'company_website' ] ?? '';
$website_target = $website[ 'target' ] ?? '_self';

$postUrl = 'http' . (isset($_SERVER[ 'HTTPS' ]) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}";
?>
    <section class="company-details pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <div class="col-xxl-3 col-xl-4 order-xl-last">
                    <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mb-50">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail('full', array( 'class' => 'lazy-img m-auto logo' ));
                        }
                        ?>
                        <div class="text-md text-dark text-center mt-15 mb-20 lg-mb-10"><?php the_title() ?></div>
                        <?php if ( !empty($website['url']) ) : ?>
                            <div class="text-center">
                                <a href="<?php echo esc_url($website[ 'url' ]) ?>" class="website-btn-two tran3s" target="<?php echo esc_attr($website_target) ?>">
                                    <?php echo esc_html($website['text']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="border-top mt-35 lg-mt-20 pt-25">
                            <ul class="job-meta-data row style-none">
                                <li class="col-12">
                                    <span>Location: </span>
                                    <div>Spain, Barcelona </div>
                                </li>
                                <li class="col-12">
                                    <span>Size:</span>
                                    <div>7000-8000, Worldwide</div>
                                </li>
                                <li class="col-12">
                                    <span>Email: </span>
                                    <div><a href="#">company@inquery.com</a></div>
                                </li>
                                <li class="col-12">
                                    <span>Founded: </span>
                                    <div>13 Jan, 1997</div>
                                </li>
                                <li class="col-12">
                                    <span>Phone:</span>
                                    <div><a href="#">(990) 234 112 779,</a> <a href="#">+770 723801870</a></div>
                                </li>
                                <li class="col-12">
                                    <span>Category: </span>
                                    <div>Technology, Product,  Agency</div>
                                </li>
                                <li class="col-12">
                                    <span>Social: </span>
                                    <div>
                                        <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                                        <a href="#" class="me-3"><i class="bi bi-instagram"></i></a>
                                        <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                                        <a href="#"><i class="bi bi-linkedin"></i></a>
                                    </div>
                                </li>
                            </ul>

                            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-25">Send Message</a>
                        </div>
                    </div>
                    <!-- /.job-company-info -->
                </div>

                <div class="col-xxl-9 col-xl-8 order-xl-first">
                    <div class="details-post-data me-xxl-5 pe-xxl-4">

                        <?php the_content(); ?>

                        <div class="position-relative">

                            <h3>Company Reviews</h3>

                            <div class="company-review-slider">
                                <div class="item">
                                    <div class="feedback-block-four">
                                        <div class="d-flex align-items-center">
                                            <ul class="style-none d-flex rating">
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                            </ul>
                                            <div class="review-score"><span class="fw-500 text-dark">4.7</span> out of 5</div>
                                        </div>
                                        <blockquote>It's a well created theme without much overhead. The created was very responsive and helpful.</blockquote>
                                        <div class="d-flex align-items-center">
                                            <img src="images/assets/img_14.jpg" alt="" class="author-img rounded-circle">
                                            <div class="ms-3">
                                                <div class="name fw-500 text-dark">Zubayer Al Hasan</div>
                                                <span class="opacity-50">Dhaka</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.feedback-block-four -->
                                </div>
                                <div class="item">
                                    <div class="feedback-block-four">
                                        <div class="d-flex align-items-center">
                                            <ul class="style-none d-flex rating">
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                            </ul>
                                            <div class="review-score"><span class="fw-500 text-dark">4.7</span> out of 5</div>
                                        </div>
                                        <blockquote>Very impressed with the jobi template. The designs/layouts are modern and professional with quaintly..</blockquote>
                                        <div class="d-flex align-items-center">
                                            <img src="images/assets/img_15.jpg" alt="" class="author-img rounded-circle">
                                            <div class="ms-3">
                                                <div class="name fw-500 text-dark">Rashed Ka</div>
                                                <span class="opacity-50">Italy</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.feedback-block-four -->
                                </div>
                                <div class="item">
                                    <div class="feedback-block-four">
                                        <div class="d-flex align-items-center">
                                            <ul class="style-none d-flex rating">
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                                <li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>
                                            </ul>
                                            <div class="review-score"><span class="fw-500 text-dark">4.7</span> out of 5</div>
                                        </div>
                                        <blockquote>Very impressed with the jobi template. The designs/layouts are modern and professional with quaintly..</blockquote>
                                        <div class="d-flex align-items-center">
                                            <img src="images/assets/img_22.jpg" alt="" class="author-img rounded-circle">
                                            <div class="ms-3">
                                                <div class="name fw-500 text-dark">Martin Jonas</div>
                                                <span class="opacity-50">USA</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.feedback-block-four -->
                                </div>
                            </div>
                        </div>

                        <nav class="share-option mt-60">

                            <ul class="style-none d-flex align-items-center">
                                <li class="fw-500 me-2"><?php esc_html_e('Share:', 'jobly'); ?></li>
                                <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Facebook', 'jobly'); ?>"><i class="bi bi-facebook"></i></a></li>
                                <li><a href="https://www.linkedin.com/share?url=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Linkedin', 'jobly'); ?>"><i class="bi bi-linkedin"></i></a></li>
                                <li><a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Twitter', 'jobly'); ?>"><i class="bi bi-twitter"></i></a></li>
                            </ul>

                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php


get_footer();
