<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Template for the candidate profile page.
 *
 * @package Jobus
 * @subpackage Templates
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Called the helper functions
$candidate_form = new \jobus\includes\Classes\submission\Candidate_Form_Submission();

// Get current user
$user = wp_get_current_user();

$candidate_id = $candidate_form->get_candidate_id($user->ID);
$specs = $candidate_form->get_candidate_specifications($candidate_id);
$candidate_location = $candidate_form->get_candidate_location($candidate_id);
$description_data = $candidate_form->get_candidate_description($candidate_id);
$candidate_age = $specs['age'];
$candidate_mail = $specs['mail'];
$candidate_specifications = $specs['specifications'];
$candidate_dynamic_fields = $specs['dynamic_fields'];
$description = $description_data['description'];
$avatar_url = $description_data['avatar_url'];
?>
<div class="jbs-position-relative">
    <h2 class="main-title"><?php esc_html_e('My Profile', 'jobus'); ?></h2>

    <form action="" id="candidate-profile-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field('candidate_profile_update', 'candidate_profile_nonce'); ?>
        <input type="hidden" name="candidate_profile_form_submit" value="1" />

        <div class="bg-white card-box border-20" id="candidate-profile-description">
            <div class="user-avatar-setting jbs-d-flex jbs-align-items-center mb-30">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img" id="candidate_avatar">
                <div class="upload-btn jbs-position-relative tran3s jbs-ms-4 jbs-me-3" id="candidate_profile_picture_upload">
                    <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                    <input type="hidden" id="candidate_profile_picture_id" name="candidate_profile_picture_id" value="<?php echo esc_attr( $description_data['profile_picture_id'] ); ?>">
                </div>
                <button type="button" name="delete_profile_picture" class="delete-btn tran3s" id="delete_profile_picture">
                    <?php esc_html_e( 'Delete', 'jobus' ); ?>
                </button>
                <input type="hidden" name="profile_picture_action" id="profile_picture_action" value="">
            </div>

            <div class="dash-input-wrapper mb-30">
                <label for="candidate_name"><?php esc_html_e( 'Full Name*', 'jobus' ); ?></label>
                <input type="text" name="candidate_name" id="candidate_name" value="<?php echo esc_attr( $user->display_name ); ?>">
            </div>

            <div class="dash-input-wrapper">
                <label for="candidate_description"><?php esc_html_e( 'Description', 'jobus' ); ?></label>
                <div class="editor-wrapper">
                    <?php
                    wp_editor(
                        $description,
                        'candidate_description',
                        array(
                            'textarea_name' => 'candidate_description',
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

        <div class="bg-white card-box border-20 mt-40" id="candidate-profile-social-icons">
            <h4 class="dash-title-three">
                <?php esc_html_e( 'Social Media', 'jobus' ); ?>
            </h4>
            <?php
            $available_icons = [
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

            $user_social_links = [];
            if ( $candidate_id ) {
                $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
                if ( is_array($meta) && !empty($meta['social_icons']) ) {
                    $user_social_links = $meta['social_icons'];
                }
            }
            if ( ! is_array( $user_social_links ) ) {
                $user_social_links = [];
            }
            ?>
            <div class="accordion dash-accordion-one" id="social-links-repeater">
                <?php
                foreach ( $user_social_links as $index => $item ) {
                    $accordion_id = 'social-link-' . esc_attr( $index );
                    $icon_label = $available_icons[ $item['icon'] ] ?? esc_html__( 'Social Network', 'jobus' );
                    ?>
                    <div class="accordion-item social-link-item">
                        <div class="accordion-header" id="heading-<?php echo esc_attr( $index ); ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-jbs-toggle="collapse"
                                    data-jbs-target="#<?php echo esc_attr( $accordion_id ); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr( $accordion_id ); ?>">
                                <?php
                                echo esc_html( $icon_label );
                                ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr( $accordion_id ); ?>" class="accordion-collapse collapse"
                             aria-labelledby="heading-<?php echo esc_attr( $index ); ?>"
                             data-jbs-parent="#social-links-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_<?php echo esc_attr($index); ?>_icon">
                                                <?php esc_html_e( 'Icon', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <select name="social_icons[<?php echo esc_attr( $index ); ?>][icon]" id="social_<?php echo esc_attr($index); ?>_icon" class="nice-select">
                                                <?php foreach ( $available_icons as $icon_class => $icon_label ) : ?>
                                                    <option value="<?php echo esc_attr( $icon_class ); ?>" <?php selected( $item['icon'], $icon_class ); ?>>
                                                        <?php echo esc_html( $icon_label ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="jbs-row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_<?php echo esc_attr($index); ?>_url">
                                                <?php esc_html_e( 'URL', 'jobus' ); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="social_icons[<?php echo esc_attr( $index ); ?>][url]" id="social_<?php echo esc_attr($index); ?>_url" class="jbs-form-control" value="<?php echo esc_attr( $item['url'] ); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="jbs-text-end">
                                    <button type="button" class="jbs-btn jbs-btn-danger jbs-btn-sm remove-social-link mt-2 mb-2" title="<?php esc_attr_e('Remove Item', 'jobus'); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e('Remove', 'jobus'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-social-link">
                <i class="bi bi-plus"></i> <?php esc_html_e( 'Add Social Item', 'jobus' ); ?>
            </a>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="candidate-profile-specifications">
            <h4 class="dash-title-three"><?php esc_html_e('Specifications', 'jobus'); ?></h4>
            <div class="jbs-row">
                <div class="jbs-col-lg-3">
                    <div class="dash-input-wrapper mb-25">
                        <label for="candidate_age"><?php esc_html_e('Date of Birth (Age)', 'jobus'); ?></label>
                        <input type="date" name="candidate_age" id="candidate_age" class="jbs-form-control" value="<?php echo esc_attr($candidate_age); ?>">
                    </div>
                </div>
                <div class="jbs-col-lg-3">
                    <div class="dash-input-wrapper mb-25">
                        <label for="candidate_mail"><?php esc_html_e('Candidate Email', 'jobus'); ?></label>
                        <input type="email" name="candidate_mail" id="candidate_mail" class="jbs-form-control" value="<?php echo esc_attr($candidate_mail); ?>">
                    </div>
                </div>
                <?php
                // Dynamic fields for candidate specifications
                if (function_exists('jobus_opt')) {
                    $candidate_spec_fields = jobus_opt('candidate_specifications');
                    if (!empty($candidate_spec_fields)) {
                        foreach ($candidate_spec_fields as $field) {
                            $meta_key = $field['meta_key'] ?? '';
                            $meta_name = $field['meta_name'] ?? '';
                            $meta_value = $candidate_dynamic_fields[ $meta_key ] ?? '';
                            $meta_values = $field['meta_values_group'] ?? array();
                            echo '<div class="jbs-col-lg-3"><div class="dash-input-wrapper mb-25">';
                            echo '<label for="' . esc_attr($meta_key) . '">' . esc_html($meta_name) . '</label>';
                            echo '<select name="' . esc_attr($meta_key) . '[]" id="' . esc_attr($meta_key) . '" class="nice-select" multiple>';
                            foreach ($meta_values as $option) {
                                $val = strtolower(preg_replace('/[\s,]+/', '@space@', $option['meta_values']));
                                $selected = (is_array($meta_value) && in_array($val, $meta_value)) ? 'selected' : '';
                                echo '<option value="' . esc_attr($val) . '" ' . esc_attr($selected) . '>' . esc_html($option['meta_values']) . '</option>';
                            }
                            echo '</select></div></div>';
                        }
                    }
                }
                ?>
            </div>
            <div class="jbs-row">
                <div class="jbs-col-12">
                    <div class="dash-input-wrapper mb-25">
                        <label><?php esc_html_e('Additional Specifications', 'jobus'); ?></label>
                        <div id="specifications-repeater">
                            <?php
                            if (!empty($candidate_specifications)) {
                                foreach ($candidate_specifications as $i => $spec) {
                                    ?>
                                    <div class="dash-input-wrapper mb-20 specification-item jbs-d-flex jbs-align-items-center gap-2">
                                        <input type="text" name="candidate_specifications[<?php echo esc_attr($i); ?>][title]" class="jbs-form-control jbs-me-2" placeholder="<?php esc_attr_e('Title', 'jobus'); ?>" value="<?php echo esc_attr($spec['title']); ?>" style="min-width:180px">
                                        <input type="text" name="candidate_specifications[<?php echo esc_attr($i); ?>][value]" class="jbs-form-control jbs-me-2" placeholder="<?php esc_attr_e('Value', 'jobus'); ?>" value="<?php echo esc_attr($spec['value']); ?>" style="min-width:180px">
                                        <button type="button" class="jbs-btn jbs-btn-danger remove-specification" title="<?php esc_attr_e('Remove', 'jobus'); ?>"><i class="bi bi-x"></i></button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <a href="javascript:void(0)" class="dash-btn-one" id="add-specification">
                            <i class="bi bi-plus"></i> <?php esc_html_e('Add Specification', 'jobus'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="candidate-profile-map">
            <h4 class="dash-title-three"><?php esc_html_e('Address & Location', 'jobus'); ?></h4>
            <div class="jbs-row">
                <div class="jbs-col-12">
                    <div class="dash-input-wrapper mb-25">
                        <label for="candidate_location_address"><?php esc_html_e('Map Location*', 'jobus') ?></label>
                        <div class="jbs-position-relative">
                            <input type="text" name="candidate_location_address" id="candidate_location_address" placeholder="<?php esc_attr_e('XC23+6XC, Moiran, N105', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['address']); ?>">
                        </div>
                        <div class="jbs-row mt-2">
                            <div class="jbs-col-md-6 mb-2">
                                <input type="text" name="candidate_location_lat" id="candidate_location_lat" placeholder="<?php esc_attr_e('Latitude', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['latitude']); ?>">
                            </div>
                            <div class="jbs-col-md-6 mb-2">
                                <input type="text" name="candidate_location_lng" id="candidate_location_lng" placeholder="<?php esc_attr_e('Longitude', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['longitude']); ?>">
                            </div>
                        </div>
                        <?php
                        $lat = trim($candidate_location['latitude']);
                        $lng = trim($candidate_location['longitude']);
                        $zoom = !empty($candidate_location['zoom']) ? intval($candidate_location['zoom']) : 15;
                        $is_http = is_ssl() ? 'https://' : 'http://';
                        $iframe_url = $is_http . "maps.google.com/maps?q={$lat},{$lng}&z={$zoom}&output=embed";
                        ?>
                        <div class="map-frame mt-30">
                            <iframe class="gmap_iframe jbs-h-100 jbs-w-100"
                                    id="candidate_gmap_iframe"
                                    src="<?php echo esc_url($iframe_url); ?>"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="button-group jbs-d-inline-flex jbs-align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s jbs-me-3 rounded-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>
    </form>

</div>