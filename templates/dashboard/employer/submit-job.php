<?php
/**
 * Template for displaying "Submit Job" section in the employer dashboard.
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
$dashboard_url          = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
$my_jobs_url            = $dashboard_url ? trailingslashit( $dashboard_url ) . 'jobs' : '#';

// Get dynamic labels
$post_job_label = jobus_opt( 'label_post_job', esc_html__( 'Post Job', 'jobus' ) );
$update_job_label = jobus_opt( 'label_update_job', esc_html__( 'Update Job', 'jobus' ) );
$submit_button_label = $editing_job ? $update_job_label : $post_job_label;
?>

<div class="jbs-position-relative">

    <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between jbs-mb-40 jbs-lg-mb-30">
        <h2 class="main-title jbs-m-0"> <?php echo esc_html( $sec_title ); ?> </h2>
        <a href="<?php echo esc_url( $my_jobs_url ); ?>" class="jbs-btn jbs-btn-primary jbs-mt-3 jbs-mt-sm-0">
            <i class="bi bi-arrow-left"></i>
            <?php esc_html_e( 'Back to Jobs', 'jobus' ); ?>
        </a>
    </div>

    <form action="#" id="employer-submit-job-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'employer_submit_job', 'employer_submit_job_nonce' ); ?>
        <input type="hidden" name="employer_submit_job_form" value="1">
        <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">

        <div class="jbs-bg-white card-box border-20">
            <h4 class="dash-title-three"><?php esc_html_e( 'Job Details', 'jobus' ); ?></h4>

            <!-- Job Title & Content -->
            <div class="dash-input-wrapper jbs-mb-30">
                <label for="job_title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></label>
                <input type="text" name="job_title" id="job_title" placeholder="<?php esc_attr_e( 'Enter job title', 'jobus' ); ?>" value="<?php echo esc_attr( $job_title ); ?>" required>
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

            <!-- Company Logo Upload -->
            <div id="company-logo-section">
                <h4 class="dash-title-three"><?php esc_html_e( 'Company Logo', 'jobus' ); ?></h4>
                
                <div class="dash-input-wrapper jbs-mb-30">
                    <label for="job_company_logo"><?php esc_html_e( 'Upload Company Logo', 'jobus' ); ?></label>
                    <div class="logo-upload-wrapper">
                        <?php
                        // Get current logo if editing
                        $current_logo_id = '';
                        $current_logo_url = '';
                        if ( $job_id ) {
                            $current_logo_id = get_post_thumbnail_id( $job_id );
                            if ( $current_logo_id ) {
                                $current_logo_url = wp_get_attachment_image_url( $current_logo_id, 'thumbnail' );
                            }
                        }
                        ?>
                        
                        <div class="logo-preview-container" style="margin-bottom: 15px;">
                            <?php if ( $current_logo_url ) : ?>
                                <img src="<?php echo esc_url( $current_logo_url ); ?>" alt="<?php esc_attr_e( 'Company Logo Preview', 'jobus' ); ?>" class="logo-preview" style="max-width: 150px; max-height: 150px; display: block; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                            <?php else : ?>
                                <img src="" alt="<?php esc_attr_e( 'Company Logo Preview', 'jobus' ); ?>" class="logo-preview" style="max-width: 150px; max-height: 150px; display: none; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                            <?php endif; ?>
                        </div>
                        
                        <div class="logo-upload-controls">
                            <input type="hidden" name="job_company_logo_id" id="job_company_logo_id" value="<?php echo esc_attr( $current_logo_id ); ?>">
                            <button type="button" class="jbs-btn jbs-btn-primary" id="upload_logo_button">
                                <i class="bi bi-upload"></i>
                                <?php echo $current_logo_url ? esc_html__( 'Change Logo', 'jobus' ) : esc_html__( 'Upload Logo', 'jobus' ); ?>
                            </button>
                            <?php if ( $current_logo_url ) : ?>
                                <button type="button" class="jbs-btn jbs-btn-secondary jbs-ms-2" id="remove_logo_button">
                                    <i class="bi bi-trash"></i>
                                    <?php esc_html_e( 'Remove Logo', 'jobus' ); ?>
                                </button>
                            <?php else : ?>
                                <button type="button" class="jbs-btn jbs-btn-secondary jbs-ms-2" id="remove_logo_button" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                    <?php esc_html_e( 'Remove Logo', 'jobus' ); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <p class="jbs-text-muted jbs-mt-2" style="font-size: 13px;">
                            <?php esc_html_e( 'Recommended size: 150x150px. Accepted formats: JPG, PNG, GIF', 'jobus' ); ?>
                        </p>
                    </div>
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
                        <ul id="job-category-list" class="jbs-style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
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
                        <ul id="job-location-list" class="jbs-style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
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
                        <ul id="job-tag-list" class="jbs-style-none jbs-d-flex jbs-flex-wrap jbs-align-items-center">
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
                                echo '<select name="' . esc_attr( $meta_key ) . '[]" id="' . esc_attr( $meta_key ) . '" class="jbs-nice-select" multiple>';
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
                <label class="jbs-mb-10 jbs-d-block" for="is_company_website">
                    <?php esc_html_e( 'Company Website', 'jobus' ); ?>
                </label>
                <div class="jbs-row">
                    <div class="jbs-row">
                        <div class="jbs-col-md-12">
                            <div class="dash-input-wrapper jbs-mb-30">
                                <select id="is_company_website" name="is_company_website" class="jbs-nice-select">
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
                                    <select id="company-website-target" name="company_website[target]" class="jbs-nice-select">
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
            <button type="submit" class="dash-btn-two tran3s jbs-me-3">
                <?php echo esc_html( $submit_button_label ); ?>
            </button>
        </div>
    </form>
</div>