<?php
/**
 * Template for displaying the "Profile" section in the employer dashboard.
 *
 * This template is used to show the profile information of the employer in their dashboard.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
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
$company_specifications = $specs['specifications'];
$company_dynamic_fields = $specs['dynamic_fields'];
$user_social_links      = $company_form->get_company_social_icons( $company_id );
$company_website        = $company_form::get_company_website( $company_id );
$company_website_url    = $company_website['url'];
$company_website_title  = $company_website['title'];
$company_website_target = $company_website['target'];
$video_data             = $company_form->get_company_video( $company_id );
?>
<div class="position-relative">

    <h2 class="main-title"><?php esc_html_e( 'Profile', 'jobus' ); ?></h2>

    <form action="#" id="company-profile-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'company_profile_update', 'company_profile_nonce' ); ?>
        <input type="hidden" name="company_profile_form_submit" value="1"/>

        <div class="bg-white card-box border-20">
            <div class="user-avatar-setting d-flex align-items-center mb-30">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img"
                     id="company-avatar-preview">
                <button type="button" class="upload-btn position-relative tran3s ms-4 me-3" id="company-profile-picture-upload">
                    <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                </button>
                <button type="button" name="company_delete_profile_picture" class="delete-btn tran3s" id="company-delete-profile-picture">
                    <?php esc_html_e( 'Delete', 'jobus' ); ?>
                </button>
                <input type="hidden" name="company_profile_picture" id="company-profile-picture"
                       value="<?php echo esc_attr( $content_data['company_profile_picture'] ); ?>">
                <input type="hidden" name="company_profile_picture_temp" id="company-profile-picture-temp" value="">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-name"><?php esc_html_e( 'Company Name*', 'jobus' ); ?></label>
                        <input type="text" id="company-name" name="company_name" placeholder="<?php esc_attr_e( 'Company Name', 'jobus' ); ?>"
                               value="<?php echo esc_attr( $content_data['company_name'] ); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                        <input type="email" id="company-email" name="company_mail" placeholder="<?php esc_attr_e( 'companyinc@gmail.com', 'jobus' ); ?>"
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

        <div class="bg-white card-box border-20 mt-40" id="company-specifications">
            <h4 class="dash-title-three"><?php esc_html_e( 'Specifications', 'jobus' ); ?></h4>
            <div class="row">
                <?php
                // Dynamic fields for candidate specifications
                if ( function_exists( 'jobus_opt' ) ) {
                    $company_spec_fields = jobus_opt( 'company_specifications' );
                    if ( ! empty( $company_spec_fields ) ) {
                        foreach ( $company_spec_fields as $field ) {
                            $meta_key    = $field['meta_key'] ?? '';
                            $meta_name   = $field['meta_name'] ?? '';
                            $meta_value  = $company_dynamic_fields[ $meta_key ] ?? '';
                            $meta_values = $field['meta_values_group'] ?? array();
                            echo '<div class="col-lg-3"><div class="dash-input-wrapper mb-25">';
                            echo '<label for="' . esc_attr( $meta_key ) . '">' . esc_html( $meta_name ) . '</label>';
                            echo '<select name="' . esc_attr( $meta_key ) . '[]" id="' . esc_attr( $meta_key ) . '" class="nice-select" multiple>';
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

        <div class="bg-white card-box border-20 mt-40">
            <h4 class="dash-title-three"><?php esc_html_e( 'Company Website', 'jobus' ); ?></h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-website-url"><?php esc_html_e( 'Website URL', 'jobus' ); ?></label>
                        <input type="url" id="company-website-url" name="company_website[url]"
                               placeholder="<?php esc_attr_e( 'https://yourcompany.com', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_url ); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-website-title"><?php esc_html_e( 'Website Text', 'jobus' ); ?></label>
                        <input type="text" id="company-website-title" name="company_website[title]"
                               placeholder="<?php esc_attr_e( 'Visit our website', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_title ); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-website-target"><?php esc_html_e( 'Link Target', 'jobus' ); ?></label>
                        <select id="company-website-target" name="company_website[target]" class="nice-select">
                            <option value="_self" <?php selected( $company_website_target, '_self' ); ?>><?php esc_html_e( 'Self Tab', 'jobus' ); ?></option>
                            <option value="_blank" <?php selected( $company_website_target, '_blank' ); ?>><?php esc_html_e( 'New Tab', 'jobus' ); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="company-social-icons">
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
                    $accordion_id = 'social-link-' . esc_attr( $index );
                    $icon_label = $available_icons[ $item['icon'] ] ?? esc_html__( 'Social Network', 'jobus' );
                    ?>
                    <div class="accordion-item social-link-item">
                        <div class="accordion-header" id="heading-<?php echo esc_attr( $index ); ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo esc_attr( $accordion_id ); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr( $accordion_id ); ?>">
                                <?php echo esc_html( $icon_label ); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr( $accordion_id ); ?>" class="accordion-collapse collapse"
                             aria-labelledby="heading-<?php echo esc_attr( $index ); ?>"
                             data-bs-parent="#social-links-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_<?php echo esc_attr( $index ); ?>_icon">
                                                <?php esc_html_e( 'Icon', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <select name="social_icons[<?php echo esc_attr( $index ); ?>][icon]"
                                                    id="social_<?php echo esc_attr( $index ); ?>_icon" class="nice-select">
                                                <?php foreach ( $available_icons as $icon_class => $icon_label ) : ?>
                                                    <option value="<?php echo esc_attr( $icon_class ); ?>" <?php selected( $item['icon'], $icon_class ); ?>>
                                                        <?php echo esc_html( $icon_label ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_<?php echo esc_attr( $index ); ?>_url">
                                                <?php esc_html_e( 'URL', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="social_icons[<?php echo esc_attr( $index ); ?>][url]"
                                                   id="social_<?php echo esc_attr( $index ); ?>_url" class="form-control"
                                                   value="<?php echo esc_attr( $item['url'] ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-social-link mt-2 mb-2"
                                            title="<?php esc_attr_e( 'Remove Item', 'jobus' ); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e( 'Remove', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-social-link">
                <i class="bi bi-plus"></i> <?php esc_html_e( 'Add Social Item', 'jobus' ); ?>
            </a>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="company-video">
            <h4 class="dash-title-three"><?php esc_html_e( 'Intro Video', 'jobus' ); ?></h4>
            <div class="intro-video-form position-relative mt-20 w-100">
                <div class="dash-input-wrapper mb-15">
                    <label for="video-title"><?php esc_html_e( 'Title', 'jobus' ); ?></label>
                    <input type="text" id="video-title" name="company_video_title" value="<?php echo esc_attr( $video_data['company_video_title'] ); ?>"
                           placeholder="<?php esc_attr_e( 'Intro', 'jobus' ); ?>">
                </div>
                <div class="dash-input-wrapper mb-15">
                    <label for="video-url"><?php esc_html_e( 'Video URL', 'jobus' ); ?></label>
                    <input type="text" id="video-url" name="company_video_url" value="<?php echo esc_attr( $video_data['company_video_url'] ); ?>"
                           placeholder="<?php esc_attr_e( 'Enter your video URL', 'jobus' ); ?>">
                </div>
                <div class="dash-input-wrapper mb-15">
                    <label for="video-bg-img"><?php esc_html_e( 'Background Image', 'jobus' ); ?></label>

                    <!-- Image Preview Section -->
                    <div id="bg-img-preview" class="preview <?php echo empty( $video_data['company_video_bg_img']['url'] ) ? 'hidden' : ''; ?>">
                        <div class="attached-file d-flex align-items-center justify-content-between">
                            <span id="video-bg-image-uploaded-filename"><?php echo esc_html( $video_data['company_video_bg_img']['url'] ); ?></span>
                            <a href="#" id="remove-uploaded-bg-img" class="remove-btn"><i class="bi bi-x"></i></a>
                        </div>
                    </div>

                    <div id="bg-img-upload-btn-wrapper" class="<?php echo ! empty( $video_data['company_video_bg_img']['url'] ) ? 'hidden' : ''; ?>">
                        <div class="dash-btn-one d-inline-block position-relative me-3">
                            <i class="bi bi-plus"></i>
                            <?php esc_html_e( 'Upload Image', 'jobus' ); ?>
                            <button type="button" id="video-bg-img-upload-btn" class="position-absolute w-100 h-100 start-0 top-0 opacity-0"></button>
                        </div>
                    </div>
                    <!-- Hidden field for image ID -->
                    <input type="hidden" id="video-bg-img" name="company_video_bg_img" value="<?php echo esc_attr( $video_data['company_video_bg_img']['id'] ?? '' ); ?>">
                    <input type="hidden" id="video-bg-img-action" name="video_bg_img_action" value="">
                </div>
            </div>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="company-testimonial-section">
            <h4 class="dash-title-three"><?php esc_html_e( 'Testimonials', 'jobus' ); ?></h4>
            <?php
            $testimonials = $company_form->get_company_testimonials( $company_id );
            $testimonial_title = get_post_meta( $company_id, 'company_testimonial_title', true );
            ?>
            <div class="dash-input-wrapper mb-15">
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
                    $accordion_id = 'company-testimonial-' . esc_attr( $key );
                    ?>
                    <div class="accordion-item company-testimonial-item">
                        <div class="accordion-header" id="company-testimonial-heading-<?php echo esc_attr( $key ); ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo esc_attr( $accordion_id ); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr( $accordion_id ); ?>">
                                <?php echo esc_html( $testimonial['author_name'] ?? esc_html__( 'Testimonial', 'jobus' ) ); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr( $accordion_id ); ?>" class="accordion-collapse collapse"
                             aria-labelledby="company-testimonial-heading-<?php echo esc_attr( $key ); ?>"
                             data-bs-parent="#company-testimonial-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-author-name">
                                                <?php esc_html_e( 'Author Name', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="company_testimonials[<?php echo esc_attr( $key ); ?>][author_name]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-author-name" class="form-control"
                                                   value="<?php echo esc_attr( $testimonial['author_name'] ?? '' ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-location">
                                                <?php esc_html_e( 'Location', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="company_testimonials[<?php echo esc_attr( $key ); ?>][location]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-location" class="form-control"
                                                   value="<?php echo esc_attr( $testimonial['location'] ?? '' ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-review-content">
                                                <?php esc_html_e( 'Review', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <textarea name="company_testimonials[<?php echo esc_attr( $key ); ?>][review_content]"
                                                      id="company-testimonial-<?php echo esc_attr( $key ); ?>-review-content" class="form-control"
                                                      rows="3"><?php echo esc_textarea( $testimonial['review_content'] ?? '' ); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-rating">
                                                <?php esc_html_e( 'Rating', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="number" min="0" max="5" step="0.1"
                                                   name="company_testimonials[<?php echo esc_attr( $key ); ?>][rating]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-rating" class="form-control"
                                                   value="<?php echo esc_attr( $testimonial['rating'] ?? '' ); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-<?php echo esc_attr( $key ); ?>-image">
                                                <?php esc_html_e( 'Author Image', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="company_testimonials[<?php echo esc_attr( $key ); ?>][image]"
                                                   id="company-testimonial-<?php echo esc_attr( $key ); ?>-image" class="form-control"
                                                   value="<?php echo esc_attr( $testimonial['image'] ?? '' ); ?>">
                                            <!-- You may want to add a media uploader for images here in future -->
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-company-testimonial mt-2 mb-2"
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
            <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-company-testimonial">
                <i class="bi bi-plus"></i> <?php esc_html_e( 'Add Testimonial', 'jobus' ); ?>
            </a>
        </div>

        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3 rounded-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>

    </form>
</div>
