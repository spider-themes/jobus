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
$employer_form = new jobus\includes\Classes\submission\Employer_Form_Submission();

// Get current user
$user               = wp_get_current_user();
$employer_id        = $employer_form->get_employer_id( $user->ID );
$content_data       = $employer_form->get_employer_content( $employer_id );
$profile_picture_id = $content_data['employer_profile_picture'];
$avatar_url         = $profile_picture_id ? wp_get_attachment_url( $profile_picture_id ) : get_avatar_url( $user->ID );
$description        = $content_data['employer_description'];

$specs                    = $employer_form->get_company_specifications( $employer_id );
$company_specifications   = $specs['specifications'];
$company_dynamic_fields   = $specs['dynamic_fields'];

$company_website = $employer_form::get_company_website( $employer_id );
$company_website_url = $company_website['url'];
$company_website_title = $company_website['title'];
$company_website_target = $company_website['target'];
?>
<div class="position-relative">

    <h2 class="main-title"><?php esc_html_e( 'Profile', 'jobus' ); ?></h2>

    <form action="#" id="employer-profile-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'employer_profile_update', 'employer_profile_nonce' ); ?>
        <input type="hidden" name="employer_profile_form_submit" value="1"/>

        <div class="bg-white card-box border-20">
            <div class="user-avatar-setting d-flex align-items-center mb-30">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img"
                     id="employer-avatar-preview">
                <button type="button" class="upload-btn position-relative tran3s ms-4 me-3" id="employer-profile-picture-upload">
                    <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                </button>
                <button type="button" name="employer_delete_profile_picture" class="delete-btn tran3s" id="employer-delete-profile-picture">
                    <?php esc_html_e( 'Delete', 'jobus' ); ?>
                </button>
                <input type="hidden" name="employer_profile_picture" id="employer-profile-picture"
                       value="<?php echo esc_attr( $content_data['employer_profile_picture'] ); ?>">
                <input type="hidden" name="employer_profile_picture_temp" id="employer-profile-picture-temp" value="">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="dash-input-wrapper mb-30">
                        <label for="employer-name"><?php esc_html_e( 'Employer Name*', 'jobus' ); ?></label>
                        <input type="text" id="employer-name" name="employer_name" placeholder="<?php esc_attr_e( 'Company Name', 'jobus' ); ?>"
                               value="<?php echo esc_attr( $content_data['employer_name'] ); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dash-input-wrapper mb-30">
                        <label for="employer-email"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                        <input type="email" id="employer-email" name="employer_mail" placeholder="<?php esc_attr_e( 'companyinc@gmail.com', 'jobus' ); ?>" value="<?php echo esc_attr( $user->user_email ); ?>">
                    </div>
                </div>
            </div>
            <div class="dash-input-wrapper">
                <label for="employer_description"><?php esc_html_e( 'Description', 'jobus' ); ?></label>
                <div class="editor-wrapper">
                    <?php
                    wp_editor(
                            $description,
                            'employer_description',
                            array(
                                    'textarea_name' => 'employer_description',
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
                        <input type="url" id="company-website-url" name="company_website[url]" placeholder="<?php esc_attr_e( 'https://yourcompany.com', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_url ); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-30">
                        <label for="company-website-title"><?php esc_html_e( 'Website Text', 'jobus' ); ?></label>
                        <input type="text" id="company-website-title" name="company_website[title]" placeholder="<?php esc_attr_e( 'Visit our website', 'jobus' ); ?>" value="<?php echo esc_attr( $company_website_title ); ?>">
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

        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3 rounded-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>

    </form>
</div>
