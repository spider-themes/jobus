<?php
/**
 * Template for displayi// Handle form submission for taxonomies [categories, locations, tags]
if ( isset( $_POST['employer_submit_job_form'] ) ) {
    $nonce = isset( $_POST['employer_submit_job_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_submit_job_nonce'] ) ) : '';
    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'employer_submit_job' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'jobus' ) );
    }

    if ( isset( $_POST['job_categories'] ) ) {
        $cat_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_categories'] ) ) ) );
        wp_set_object_terms( $job_id, $cat_ids, 'jobus_job_cat' );
    }

    if ( isset( $_POST['job_locations'] ) ) {
        $location_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_locations'] ) ) );
        wp_set_object_terms( $job_id, $location_ids, 'jobus_job_location' );
    }

    if ( isset( $_POST['job_tags'] ) ) {
        $skill_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_tags'] ) ) );
        wp_set_object_terms( $job_id, $skill_ids, 'jobus_job_tag' );
    }
}" section in the employer dashboard.
 *
 * This template is used to show the job submission form for employers in their dashboard.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Get current user
$user = wp_get_current_user();

// Called the helper functions
$job_form               = new jobus\includes\Classes\submission\Job_Form_Submission();
$company_id             = $job_form->get_company_id( $user->ID );
$job_id                 = isset( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : 0; // Get job ID from URL if editing
if ( $job_id ) {
    $job_post = get_post( $job_id );
    if ( ! $job_post || $job_post->post_type !== 'jobus_job' || (int) $job_post->post_author !== (int) $user->ID ) {
        wp_die( esc_html__( 'Access denied.', 'jobus' ) );
    }
}
$editing_job            = $job_id ? get_post( $job_id ) : null;
$sec_title              = $editing_job ? esc_html__( 'Edit Job', 'jobus' ) : esc_html__( 'Submit New Job', 'jobus' );
$job_content            = $job_id ? $job_form::get_job_content( $job_id ) : [];
$job_title              = $job_content['job_title'] ?? '';
$job_description        = $job_content['job_description'] ?? '';
$specs                  = $editing_job ? $job_form->get_job_specifications( $job_id ) : $job_form->get_job_specifications( $company_id );
$dynamic_fields         = $specs['dynamic_fields'];
$company_website        = $editing_job ? $job_form::get_company_website( $job_id ) : $job_form::get_company_website( $company_id );
$company_website_url    = $company_website['url'];
$company_website_text   = $company_website['text'];
$company_website_target = $company_website['target'];
$is_company_website     = $company_website['is_company_website'] ?? 'default';

// Handle form submission for taxonomies [categories, locations, tags]
if ( isset( $_POST['employer_submit_job_form'] ) ) {

    if ( isset( $_POST['job_categories'] ) ) {
        $cat_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_categories'] ) ) ) );
        wp_set_object_terms( $job_id, $cat_ids, 'jobus_job_cat' );
    }

    if ( isset( $_POST['job_locations'] ) ) {
        $location_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_locations'] ) ) ) );
        wp_set_object_terms( $job_id, $location_ids, 'jobus_job_location' );
    }

    if ( isset( $_POST['job_tags'] ) ) {
        $skill_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_tags'] ) ) ) );
        wp_set_object_terms( $job_id, $skill_ids, 'jobus_job_tag' );
    }
}
?>

<div class="jbs-position-relative">
    <h2 class="main-title"><?php echo esc_html( $sec_title ); ?></h2>

    <form action="#" id="employer-submit-job-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'employer_submit_job', 'employer_submit_job_nonce' ); ?>
        <input type="hidden" name="employer_submit_job_form" value="1">
        <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">

        <div class="bg-white card-box border-20">
            <h4 class="dash-title-three"><?php esc_html_e( 'Job Details', 'jobus' ); ?></h4>

            <!-- Job Title & Content -->
            <div class="dash-input-wrapper jbs-mb-30">
                <label for="job_title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></label>
                <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( $job_title ); ?>" required>
            </div>

            <!-- Job Content -->
            <div class="dash-input-wrapper jbs-mb-30">
                <label for="job_description"><?php esc_html_e( 'Job Description', 'jobus' ); ?></label>
                <div class="editor-wrapper">
                    <?php
                    wp_editor(
                            $job_description,
                            'job_description',
                            array(
                                    'textarea_name' => 'job_description',
                                    'textarea_rows' => 8,
                                    'media_buttons' => true,
                                    'teeny'         => false,
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
            </div>

            <div id="job-taxonomy">
                <h4 class="dash-title-three"><?php esc_html_e( 'Taxonomies', 'jobus' ); ?></h4>

                <!-- Add Categories -->
                <div class="dash-input-wrapper jbs-mb-40 jbs-mt-20">
                    <label for="job-category-list"><?php esc_html_e( 'Categories', 'jobus' ); ?></label>
                    <div class="skills-wrapper">
                        <?php
                        $current_categories = array();
                        if ( isset( $job_id ) && $job_id ) {
                            $current_categories = wp_get_object_terms( $job_id, 'jobus_job_cat' );
                        }
                        $category_ids = ! empty( $current_categories ) ? implode( ',', wp_list_pluck( $current_categories, 'term_id' ) ) : '';
                        ?>
                        <ul id="job-category-list" class="style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
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
                        <input type="hidden" name="job_categories" id="job_categories_input" value="<?php echo esc_attr( $category_ids ); ?>">
                    </div>
                </div>

                <!-- Add Locations -->
                <div class="dash-input-wrapper jbs-mb-40 jbs-mt-20">
                    <label for="job-location-list"><?php esc_html_e( 'Locations', 'jobus' ); ?></label>
                    <div class="skills-wrapper">
                        <?php
                        $current_locations = array();
                        if ( isset( $job_id ) && $job_id ) {
                            $current_locations = wp_get_object_terms( $job_id, 'jobus_job_location' );
                        }
                        $location_ids = ! empty( $current_locations ) ? implode( ',', wp_list_pluck( $current_locations, 'term_id' ) ) : '';
                        ?>
                        <ul id="job-location-list" class="style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
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
                        <input type="hidden" name="job_locations" id="job_locations_input" value="<?php echo esc_attr( $location_ids ); ?>">
                    </div>
                </div>

                <!-- Add Tags -->
                <div class="dash-input-wrapper jbs-mb-40 jbs-mt-20">
                    <label for="job-tag-list"><?php esc_html_e( 'Tags', 'jobus' ); ?></label>
                    <div class="skills-wrapper">
                        <?php
                        $current_tags = array();
                        if ( isset( $job_id ) && $job_id ) {
                            $current_tags = wp_get_object_terms( $job_id, 'jobus_job_tag' );
                        }
                        $tag_ids = ! empty( $current_tags ) ? implode( ',', wp_list_pluck( $current_tags, 'term_id' ) ) : '';
                        ?>
                        <ul id="job-tag-list" class="style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
                            <?php if ( ! empty( $current_tags ) ): ?>
                                <?php foreach ( $current_tags as $tag ): ?>
                                    <li class="is_tag" data-tag-id="<?php echo esc_attr( $tag->term_id ); ?>">
                                        <button type="button"><?php echo esc_html( $tag->name ); ?> <i class="bi bi-x"></i></button>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="more_tag">
                                <button type="button"><?php esc_html_e( '+', 'jobus' ); ?></button>
                            </li>
                        </ul>
                        <input type="hidden" name="job_tags" id="job_tags_input" value="<?php echo esc_attr( $tag_ids ); ?>">
                    </div>
                </div>
            </div>

            <div id="job-specifications">
                <h4 class="dash-title-three"><?php esc_html_e( 'Specifications', 'jobus' ); ?></h4>
                <div class="jbs-row">
                    <?php
                    // Dynamic fields for candidate specifications
                    if ( function_exists( 'jobus_opt' ) ) {
                        $job_spec_fields = jobus_opt( 'job_specifications' );
                        if ( ! empty( $job_spec_fields ) ) {
                            foreach ( $job_spec_fields as $field ) {
                                $meta_key    = $field['meta_key'] ?? '';
                                $meta_name   = $field['meta_name'] ?? '';
                                $meta_value  = isset( $dynamic_fields[ $meta_key ] ) ? (array) $dynamic_fields[ $meta_key ] : array();
                                $meta_values = $field['meta_values_group'] ?? array();
                                echo '<div class="jbs-col-lg-3"><div class="dash-input-wrapper jbs-mb-25">';
                                echo '<label for="' . esc_attr( $meta_key ) . '">' . esc_html( $meta_name ) . '</label>';
                                echo '<select name="' . esc_attr( $meta_key ) . '[]" id="' . esc_attr( $meta_key ) . '" class="nice-select" multiple>';
                                foreach ( $meta_values as $option ) {
                                    $val      = strtolower( preg_replace( '/[\s,]+/', '@space@', $option['meta_values'] ) );
                                    $selected = in_array( $val, $meta_value, true ) ? 'selected' : '';
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

            <div id="company-website">
                <h4 class="dash-title-three"><?php esc_html_e( 'Company Website', 'jobus' ); ?></h4>
                <div class="jbs-row">
                    <div class="jbs-row">
                        <div class="jbs-col-md-12">
                            <div class="dash-input-wrapper jbs-mb-30">
                                <select id="is_company_website" name="is_company_website" class="nice-select">
                                    <option value="default" <?php selected( $is_company_website, 'default' ); ?>><?php esc_html_e( 'Default', 'jobus' ); ?></option>
                                    <option value="custom" <?php selected( $is_company_website, 'custom' ); ?>><?php esc_html_e( 'Custom', 'jobus' ); ?></option>
                                </select>
                            </div>
                            <div id="company-website-fields">
                                <div class="dash-input-wrapper jbs-mb-30">
                                    <label for="company-website-text"><?php esc_html_e( 'Website Text', 'jobus' ); ?></label>
                                    <input type="text" id="company-website-text" name="company_website[text]"
                                           placeholder="<?php esc_attr_e( 'Visit job website', 'jobus' ); ?>"
                                           value="<?php echo esc_attr( $company_website_text ); ?>">
                                </div>
                                <div class="dash-input-wrapper jbs-mb-30">
                                    <label for="company-website-url"><?php esc_html_e( 'Website URL', 'jobus' ); ?></label>
                                    <input type="url" id="company-website-url" name="company_website[url]"
                                           placeholder="<?php esc_attr_e( 'Enter Your Website URL', 'jobus' ); ?>"
                                           value="<?php echo esc_attr( $company_website_url ); ?>">
                                </div>
                                <div class="dash-input-wrapper jbs-mb-30">
                                    <label for="company-website-target"><?php esc_html_e( 'Website Target', 'jobus' ); ?></label>
                                    <select id="company-website-target" name="company_website[target]" class="nice-select">
                                        <option value="_self" <?php selected( $company_website_target, '_self' ); ?>><?php esc_html_e( 'Self Tab', 'jobus' ); ?></option>
                                        <option value="_blank" <?php selected( $company_website_target, '_blank' ); ?>><?php esc_html_e( 'New Tab', 'jobus' ); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="button-group jbs-d-inline-flex jbs-align-items-center jbs-mt-30">
            <button type="submit" class="dash-btn-two tran3s jbs-me-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>
    </form>
</div>