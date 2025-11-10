<?php
/**
 * Template for displaying the employer profile section in the employer dashboard.
 *
 * This template is used to show the profile information of the employer in their dashboard.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Ensure media library is available for frontend uploader
if ( function_exists( 'wp_enqueue_media' ) ) {
    wp_enqueue_media();
}

// Called the helper functions
$company_form = new jobus\includes\Classes\submission\Employer_Form_Submission();

// Get current user
$user                   = wp_get_current_user();
$company_id             = $company_form->get_company_id( $user->ID );
$content_data           = $company_form->get_company_content( $company_id );
$profile_picture_id     = $content_data['company_profile_picture'];
$avatar_url             = $profile_picture_id ? wp_get_attachment_url( $profile_picture_id ) : get_avatar_url( $user->ID );
$description            = $content_data['company_description'];
$specs                  = $company_form->get_company_specifications( $company_id );
$dynamic_fields         = $specs['dynamic_fields'];
$user_social_links      = $company_form->get_company_social_icons( $company_id );
$company_website        = $company_form::get_company_website( $company_id );
$company_website_url    = $company_website['url'] ?? '';
$company_website_title  = $company_website['title'] ?? '';
$company_website_target = $company_website['target'] ?? '_self';
$video_data             = $company_form->get_company_video( $company_id );
$testimonials           = $company_form->get_company_testimonials( $company_id );
$testimonial_title      = get_post_meta( $company_id, 'company_testimonial_title', true );


// Handle form submission for taxonomies [categories, locations, tags]
if ( isset( $_POST['company_profile_form_submit'] ) ) {

    if ( isset( $_POST['company_categories'] ) ) {
        $cat_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['company_categories'] ) ) ) );
        wp_set_object_terms( $company_id, $cat_ids, 'jobus_company_cat' );
    }

    if ( isset( $_POST['company_locations'] ) ) {
        $location_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['company_locations'] ) ) ) );
        wp_set_object_terms( $company_id, $location_ids, 'jobus_company_location' );
    }
}
?>
<div class="jbs-position-relative">

    <h2 class="main-title"><?php esc_html_e( 'Profile', 'jobus' ); ?></h2>

    <form action="#" id="company-profile-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'company_profile_update', 'company_profile_nonce' ); ?>
        <input type="hidden" name="company_profile_form_submit" value="1"/>

        <div class="jbs-bg-white card-box border-20">
            <div class="user-avatar-setting jbs-d-flex jbs-align-items-center jbs-mb-30" id="employer-profile">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img"
                     id="company-avatar-preview">
                <button type="button" class="upload-btn jbs-position-relative tran3s jbs-ms-4 jbs-me-3 jbs-border-0" id="company-profile-picture-upload">
                    <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                </button>
                <button type="button" name="company_delete_profile_picture" class="delete-btn tran3s" id="company-delete-profile-picture">
                    <?php esc_html_e( 'Delete', 'jobus' ); ?>
                </button>
                <input type="hidden" name="company_profile_picture" id="company-profile-picture"
                       value="<?php echo esc_attr( $content_data['company_profile_picture'] ); ?>">
                <input type="hidden" name="company_profile_picture_temp" id="company-profile-picture-temp" value="">
            </div>
            <div class="jbs-row">
                <div class="jbs-col-md-6">
                    <div class="dash-input-wrapper jbs-mb-30">
                        <label for="company-name"><?php esc_html_e( 'Company Name*', 'jobus' ); ?></label>
                        <input type="text" id="company-name" name="company_name" placeholder="<?php esc_attr_e( 'Company Name', 'jobus' ); ?>"
                               value="<?php echo esc_attr( $content_data['company_name'] ); ?>">
                    </div>
                </div>
                <div class="jbs-col-md-6">
                    <div class="dash-input-wrapper jbs-mb-30">
                        <label for="company-email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                        <input type="email" id="company-email" name="company_mail" placeholder="you@example.com"
                               value="<?php echo esc_attr( $user->user_email ); ?>">
                    </div>
                </div>
            </div>
            <div class="dash-input-wrapper">
                <label for="company_description"><?php esc_html_e( 'Description', 'jobus' ); ?></label>
                <div class="editor-wrapper">
                    <?php
                    wp_editor(
                            $description,
                            'company_description',
                            array(
                                    'textarea_name' => 'company_description',
                                    'textarea_rows' => 8,
                                    'media_buttons' => true,
                                    'teeny'         => false, // Use full toolbar
                                    'quicktags'     => true,
                                    'editor_class'  => 'size-lg',
                                    'tinymce'       => array(
                                            'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6',
                                            'toolbar1'      => 'formatselect bold italic underline bullist numlist blockquote alignleft aligncenter alignright link unlink undo redo wp_adv',
                                            'toolbar2'      => 'strikethrough hr forecolor pastetext removeformat charmap outdent indent',
                                    ),
                            )
                    );
                    ?>
                </div>
                <div class="alert-text">
                    <?php esc_html_e( 'Brief description for your profile. URLs are hyperlinked.', 'jobus' ); ?>
                </div>
            </div>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40" id="job-taxonomy">
            <h4 class="dash-title-three"><?php esc_html_e( 'Taxonomies', 'jobus' ); ?></h4>

            <!-- Add Categories -->
            <div class="dash-input-wrapper jbs-mb-40 jbs-mt-20">
                <label for="company-category-list"><?php esc_html_e( 'Categories', 'jobus' ); ?></label>
                <div class="skills-wrapper">
                    <?php
                    $current_categories = array();
                    if ( isset( $company_id ) && $company_id ) {
                        $current_categories = wp_get_object_terms( $company_id, 'jobus_company_cat' );
                    }
                    $category_ids = ! empty( $current_categories ) ? implode( ',', wp_list_pluck( $current_categories, 'term_id' ) ) : '';
                    ?>
                    <ul id="company-category-list" class="jbs-style-none jbs-d-flex flex-wrap jbs-align-items-center">
                        <?php if ( ! empty( $current_categories ) ): ?>
                            <?php foreach ( $current_categories as $cat ): ?>
                                <li class="is_tag" data-category-id="<?php echo esc_attr( $cat->term_id ); ?>">
                                    <button type="button"><?php echo esc_html( $cat->name ); ?> <i class="bi bi-x"></i></button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="more_tag">
                            <button type="button"><?php esc_html_e( '+', 'jobus' ); ?></button>
                        </li>
                    </ul>
                    <input type="hidden" name="company_categories" id="company_categories_input" value="<?php echo esc_attr( $category_ids ); ?>">
                </div>
            </div>

            <!-- Add Locations -->
            <div class="dash-input-wrapper jbs-mb-40 jbs-mt-20">
                <label for="company-location-list"><?php esc_html_e( 'Locations', 'jobus' ); ?></label>
                <div class="skills-wrapper">
                    <?php
                    $current_locations = array();
                    if ( isset( $company_id ) && $company_id ) {
                        $current_locations = wp_get_object_terms( $company_id, 'jobus_company_location' );
                    }
                    $location_ids = ! empty( $current_locations ) ? implode( ',', wp_list_pluck( $current_locations, 'term_id' ) ) : '';
                    ?>
                    <ul id="company-location-list" class="jbs-style-none jbs-d-flex flex-wrap jbs-align-items-center">
                        <?php if ( ! empty( $current_locations ) ): ?>
                            <?php foreach ( $current_locations as $loc ): ?>
                                <li class="is_tag" data-location-id="<?php echo esc_attr( $loc->term_id ); ?>">
                                    <button type="button"><?php echo esc_html( $loc->name ); ?> <i class="bi bi-x"></i></button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="more_tag">
                            <button type="button"><?php esc_html_e( '+', 'jobus' ); ?></button>
                        </li>
                    </ul>
                    <input type="hidden" name="company_locations" id="company_locations_input" value="<?php echo esc_attr( $location_ids ); ?>">
                </div>
            </div>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40" id="company-specifications">
            <h4 class="dash-title-three"><?php esc_html_e( 'Specifications', 'jobus' ); ?></h4>
            <div class="jbs-row">
                <?php
                // Dynamic fields for candidate specifications
                if ( function_exists( 'jobus_opt' ) ) {
                    $spec_fields = jobus_opt( 'company_specifications' );
                    if ( ! empty( $spec_fields ) ) {
                        foreach ( $spec_fields as $field ) {
                            $meta_key    = $field['meta_key'] ?? '';
                            $meta_name   = $field['meta_name'] ?? '';
                            $meta_value  = $dynamic_fields[ $meta_key ] ?? '';
                            $meta_values = $field['meta_values_group'] ?? array();
                            echo '<div class="jbs-col-lg-3"><div class="dash-input-wrapper jbs-mb-25">';
                            echo '<label for="' . esc_attr( $meta_key ) . '">' . esc_html( $meta_name ) . '</label>';
                            echo '<select name="' . esc_attr( $meta_key ) . '[]" id="' . esc_attr( $meta_key ) . '" class="jbs-nice-select" multiple>';
                            foreach ( $meta_values as $option ) {
                                $val      = strtolower( preg_replace( '/[\s,]+/', '@space@', $option['meta_values'] ) );
                                $selected = ( is_array( $meta_value ) && in_array( $val, $meta_value ) ) ? 'selected' : '';
                                echo '<option value="' . esc_attr( $val ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $option['meta_values'] )
                                     . '</option>';
                            }
                            echo '</select></div></div>';
                        }
                    }
                }
                ?>
            </div>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40">
            <h4 class="dash-title-three"><?php esc_html_e( 'Company Website', 'jobus' ); ?></h4>
            <div class="jbs-row">
                <div class="jbs-col-md-12">
                    <div class="dash-input-wrapper jbs-mb-30">
                        <label for="company-website-url"><?php esc_html_e( 'Website URL', 'jobus' ); ?></label>
                        <input type="url" id="company-website-url" name="company_website[url]"
                               placeholder="<?php esc_attr_e( 'Enter Your Website URL', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_url ); ?>">
                    </div>
                    <div class="dash-input-wrapper jbs-mb-30">
                        <label for="company-website-title"><?php esc_html_e( 'Website Text', 'jobus' ); ?></label>
                        <input type="text" id="company-website-title" name="company_website[title]"
                               placeholder="<?php esc_attr_e( 'Visit our website', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_title ); ?>">
                    </div>
                    <div class="dash-input-wrapper jbs-mb-30">
                        <label for="company-website-target"><?php esc_html_e( 'Link Target', 'jobus' ); ?></label>
                        <select id="company-website-target" name="company_website[target]" class="jbs-nice-select">
                            <option value="_self" <?php selected( $company_website_target, '_self' ); ?>><?php esc_html_e( 'Self Tab', 'jobus' ); ?></option>
                            <option value="_blank" <?php selected( $company_website_target, '_blank' ); ?>><?php esc_html_e( 'New Tab', 'jobus' ); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40" id="company-social-icons">
            <h4 class="dash-title-three">
                <?php esc_html_e( 'Social Media', 'jobus' ); ?>
            </h4>
            <?php
            // Get available icons from meta options (fallback to default set)
            $default_icons   = [
                    'bi bi-facebook'  => esc_html__( 'Facebook', 'jobus' ),
                    'bi bi-instagram' => esc_html__( 'Instagram', 'jobus' ),
                    'bi bi-twitter'   => esc_html__( 'Twitter', 'jobus' ),
                    'bi bi-linkedin'  => esc_html__( 'LinkedIn', 'jobus' ),
                    'bi bi-github'    => esc_html__( 'GitHub', 'jobus' ),
                    'bi bi-youtube'   => esc_html__( 'YouTube', 'jobus' ),
                    'bi bi-dribbble'  => esc_html__( 'Dribbble', 'jobus' ),
                    'bi bi-behance'   => esc_html__( 'Behance', 'jobus' ),
                    'bi bi-pinterest' => esc_html__( 'Pinterest', 'jobus' ),
                    'bi bi-tiktok'    => esc_html__( 'TikTok', 'jobus' ),
            ];
            $available_icons = $default_icons;
            // Get saved social links from meta
            if ( ! is_array( $user_social_links ) ) {
                $user_social_links = [];
            }
            ?>
            <div class="accordion dash-accordion-one" id="social-links-repeater">
                <?php foreach ( $user_social_links as $index => $item ) :
                    $social_icon_id = 'social-link-' . esc_attr( $index );
                    $icon_label = $available_icons[ $item['icon'] ] ?? esc_html__( 'Social Network', 'jobus' );
                    ?>
                    <div class="jbs-accordion-item social-link-item">
                        <div class="jbs-accordion-header" id="heading-<?php echo esc_attr( $index ); ?>">
                            <button class="jbs-accordion-button collapsed" type="button"
                                    data-jbs-toggle="collapse"
                                    data-jbs-target="#<?php echo esc_attr( $social_icon_id ); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr( $social_icon_id ); ?>">
                                <?php echo esc_html( $icon_label ); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr( $social_icon_id ); ?>" class="jbs-accordion-collapse collapse"
                             aria-labelledby="heading-<?php echo esc_attr( $index ); ?>"
                             data-jbs-parent="#social-links-repeater">
                            <div class="jbs-accordion-body">
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="social_<?php echo esc_attr( $index ); ?>_icon">
                                                <?php esc_html_e( 'Icon', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <select name="social_icons[<?php echo esc_attr( $index ); ?>][icon]"
                                                    id="social_<?php echo esc_attr( $index ); ?>_icon" class="jbs-nice-select">
                                                <?php foreach ( $available_icons as $icon_class => $icon_label ) : ?>
                                                    <option value="<?php echo esc_attr( $icon_class ); ?>" <?php selected( $item['icon'], $icon_class ); ?>>
                                                        <?php echo esc_html( $icon_label ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="social_<?php echo esc_attr( $index ); ?>_url">
                                                <?php esc_html_e( 'URL', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <input type="text" name="social_icons[<?php echo esc_attr( $index ); ?>][url]"
                                                   id="social_<?php echo esc_attr( $index ); ?>_url" class="jbs-form-control"
                                                   value="<?php echo esc_attr( $item['url'] ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-text-end">
                                    <button type="button" class="jbs-btn jbs-btn-danger jbs-btn-sm remove-social-link jbs-mt-2 jbs-mb-2"
                                            title="<?php esc_attr_e( 'Remove Item', 'jobus' ); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e( 'Remove', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button href="javascript:void(0)" class="dash-btn-one jbs-mt-2" id="add-social-link">
                <i class="bi bi-plus"></i> <?php esc_html_e( 'Add Social Item', 'jobus' ); ?>
            </button>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40" id="company-video">
            <h4 class="dash-title-three"><?php esc_html_e( 'Intro Video', 'jobus' ); ?></h4>
            <div class="intro-video-form jbs-position-relative jbs-mt-20 w-100">
                <div class="dash-input-wrapper jbs-mb-15">
                    <label for="video-title"><?php esc_html_e( 'Title', 'jobus' ); ?></label>
                    <input type="text" id="video-title" name="company_video_title" value="<?php echo esc_attr( $video_data['company_video_title'] ); ?>"
                           placeholder="<?php esc_attr_e( 'Intro', 'jobus' ); ?>">
                </div>
                <div class="dash-input-wrapper jbs-mb-15">
                    <label for="video-url"><?php esc_html_e( 'Video URL', 'jobus' ); ?></label>
                    <input type="text" id="video-url" name="company_video_url" value="<?php echo esc_attr( $video_data['company_video_url'] ); ?>"
                           placeholder="<?php esc_attr_e( 'Enter your video URL', 'jobus' ); ?>">
                </div>
                <div class="dash-input-wrapper jbs-mb-15">
                    <label for="video-bg-img"><?php esc_html_e( 'Background Image', 'jobus' ); ?></label>

                    <!-- Image Preview Section -->
                    <div id="company-bg-img-preview" class="preview bg-img-preview <?php echo empty( $video_data['company_video_bg_img']['url'] ) ? 'hidden' : ''; ?>">
                        <div class="attached-file jbs-d-flex jbs-align-items-center jbs-justify-content-between">
                            <span id="company-video-bg-image-uploaded-filename"><?php echo esc_html( $video_data['company_video_bg_img']['url'] ); ?></span>
                            <a href="#" id="company-remove-uploaded-bg-img" class="remove-btn"><i class="bi bi-x"></i></a>
                        </div>
                    </div>

                    <div id="company-bg-img-upload-btn-wrapper" class="<?php echo ! empty( $video_data['company_video_bg_img']['url'] ) ? 'hidden' : ''; ?>">
                        <div class="dash-btn-one jbs-d-inline-block jbs-position-relative jbs-me-3">
                            <i class="bi bi-plus"></i>
                            <?php esc_html_e( 'Upload Image', 'jobus' ); ?>
                            <button type="button" id="company-video-bg-img-upload-btn" class="jbs-position-absolute jbs-w-100 jbs-h-100 jbs-start-0 jbs-top-0 jbs-opacity-0"></button>
                        </div>
                    </div>
                    <!-- Hidden field for image ID -->
                    <input type="hidden" id="company-video-bg-img" name="company_video_bg_img" value="<?php echo esc_attr( $video_data['company_video_bg_img']['id'] ?? '' ); ?>">
                    <input type="hidden" id="company-video-bg-img-action" name="video_bg_img_action" value="">
                </div>
            </div>
        </div>

        <div class="jbs-bg-white card-box border-20 jbs-mt-40" id="company-testimonials">
            <h4 class="dash-title-three"><?php esc_html_e( 'Testimonials', 'jobus' ); ?></h4>
            <div class="dash-input-wrapper jbs-mb-15">
                <label for="company-testimonial-title"><?php esc_html_e( 'Title', 'jobus' ); ?></label>
                <input type="text" id="company-testimonial-title" name="company_testimonial_title"
                       value="<?php echo esc_attr( $testimonial_title ); ?>"
                       placeholder="<?php esc_attr_e( 'Testimonials', 'jobus' ); ?>">
            </div>
            <div class="accordion dash-accordion-one" id="company-testimonial-repeater">
                <?php
                if ( empty( $testimonials ) ) {
                    $testimonials[] = array(
                            'author_name'    => '',
                            'location'       => '',
                            'review_content' => '',
                            'rating'         => '',
                            'image'          => '',
                    );
                }
                foreach ( $testimonials as $key => $testimonial ) {
                    $testimonial_id = 'company-testimonial-' . esc_attr( $key );
                    ?>
                    <div class="jbs-accordion-item company-testimonial-item">
                        <div class="jbs-accordion-header" id="company-testimonial-heading-<?php echo esc_attr( $key ); ?>">
                            <button class="jbs-accordion-button collapsed" type="button"
                                    data-jbs-toggle="collapse"
                                    data-jbs-target="#<?php echo esc_attr( $testimonial_id ); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr( $testimonial_id ); ?>">
                                <?php echo esc_html( $testimonial['author_name'] ?? esc_html__( 'Testimonial', 'jobus' ) ); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr( $testimonial_id ); ?>" class="jbs-accordion-collapse collapse"
                             aria-labelledby="company-testimonial-heading-<?php echo esc_attr( $key ); ?>"
                             data-jbs-parent="#company-testimonial-repeater">
                            <div class="jbs-accordion-body">
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-author-name">
                                                <?php esc_html_e( 'Author Name', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <input type="text" name="company_testimonials[<?php echo esc_attr( $key ); ?>][author_name]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-author-name" class="jbs-form-control"
                                                   value="<?php echo esc_attr( $testimonial['author_name'] ?? '' ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-location">
                                                <?php esc_html_e( 'Location', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <input type="text" name="company_testimonials[<?php echo esc_attr( $key ); ?>][location]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-location" class="jbs-form-control"
                                                   value="<?php echo esc_attr( $testimonial['location'] ?? '' ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-review-content">
                                                <?php esc_html_e( 'Review', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <textarea name="company_testimonials[<?php echo esc_attr( $key ); ?>][review_content]"
                                                      id="company-testimonial-<?php echo esc_attr( $key ); ?>-review-content" class="jbs-form-control"
                                                      rows="3"><?php echo esc_textarea( $testimonial['review_content'] ?? '' ); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-rating">
                                                <?php esc_html_e( 'Rating', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <select name="company_testimonials[<?php echo esc_attr( $key ); ?>][rating]"
                                                    id="company-testimonial-<?php echo esc_attr( $key ); ?>-rating" class="jbs-form-control">
                                                <option value="">--</option>
                                                <?php for ( $i = 1; $i <= 5; $i ++ ) : ?>
                                                    <option value="<?php echo esc_attr( $i ); ?>" <?php selected( (int) ( $testimonial['rating'] ?? 0 ), $i ); ?>><?php echo esc_html( $i ); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-row jbs-mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-image">
                                                <?php esc_html_e( 'Author Image', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper jbs-mb-10">
                                            <!-- Hidden field for image ID -->
                                            <input type="hidden" name="company_testimonials[<?php echo esc_attr( $key ); ?>][image]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-image" class="testimonial-image-id"
                                                   value="<?php echo esc_attr( $testimonial['image'] ?? '' ); ?>">
                                            <!-- Upload button for WP media -->
                                            <button type="button" class="jbs-btn jbs-btn-secondary testimonial-image-upload-btn"
                                                    data-index="<?php echo esc_attr( $key ); ?>">
                                                <?php esc_html_e( 'Upload Image', 'jobus' ); ?>
                                            </button>
                                            <!-- Preview area (always present) -->
                                            <div class="testimonial-image-preview jbs-mt-2 jbs-mb-2" id="testimonial-image-preview-<?php echo esc_attr( $key ); ?>">
                                                <?php
                                                $image_id  = $testimonial['image'] ?? '';
                                                $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
                                                if ( ! empty( $image_url ) ) : ?>
                                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Author Image', 'jobus' ); ?>"
                                                         style="max-width: 100px; max-height: 100px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="jbs-text-end">
                                    <button type="button" class="jbs-btn jbs-btn-danger jbs-btn-sm remove-company-testimonial jbs-mt-2 jbs-mb-2"
                                            title="<?php esc_attr_e( 'Remove Item', 'jobus' ); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e( 'Remove', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <button href="javascript:void(0)" class="dash-btn-one jbs-mt-2" id="add-company-testimonial">
                <i class="bi bi-plus"></i> <?php esc_html_e( 'Add Testimonial', 'jobus' ); ?>
            </button>
        </div>

        <div class="button-group jbs-d-inline-flex jbs-align-items-center jbs-mt-30">
            <button type="submit" class="dash-btn-two tran3s jbs-me-3 jbs-rounded-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>

    </form>
</div>
