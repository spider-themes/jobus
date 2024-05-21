<?php
namespace Jobly\Admin\Posttypes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Job_Application {

    private static $instance = null;

    public function __construct() {

        // Register the post type
        add_action('init', [$this, 'register_post_types_applications']);

        // Admin Columns
        add_filter('manage_edit-job_application_columns', [$this, 'job_application_columns']);
        add_action('manage_job_application_posts_custom_column', [$this, 'job_application_columns_data'], 10, 2);

        
        // Add custom content to edit form
        add_action('edit_form_top', array($this, 'admin_single_subtitle'));
        add_action('add_meta_boxes', [$this, 'admin_single_contents']);

    }

    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function admin_single_subtitle($post) {
        if ($post->post_type === 'job_application') {
            ?>
            <p class="jobly-application-submission-info">
                <span class="jobly-application-submission-date">Submitted on 10:46 am, May 17, 2024</span>
                <span class="jobly-applicant-ip">from IP 127.0.0.1</span>
            </p>
            <?php
        }
    }

    public function admin_single_contents()
    {
        add_meta_box(
            'applicant-details-meta-box',
            esc_html__('Applicant Details', 'jobly'),
            array($this, 'render_single_contents'),
            'job_application'
        );
    }

    public function render_single_contents($post)
    {

        if ($post->post_type === 'job_application') {
            require_once plugin_dir_path(__FILE__) . '../templates/meta/applicant-single.php';
        }

    }



    // Register the post type Applications
    public function register_post_types_applications() {
        $labels = array(
            'name'                  => esc_html__('Applications', 'jobly'),
            'singular_name'         => esc_html__('Application', 'jobly'),
            'menu_name'             => esc_html__('Applications', 'jobly'),
            'edit_item'             => esc_html__('Applications', 'jobly'),
            'search_items'          => esc_html__('Search Applications', 'jobly'),
            'not_found'             => esc_html__('No Applications found', 'jobly'),
            'not_found_in_trash'    => esc_html__('No Applications found in Trash', 'jobly'),
        );

        $args = array(
            'labels'                => $labels,
            'public'                => false,
            'show_ui'               => true,
            'map_meta_cap'          => true,
            'show_in_menu'          => 'edit.php?post_type=job',
            'capability_type'       => 'post',
            'capabilities'          => array(
                'create_posts' => 'do_not_allow',
            ),
            'supports'              => false,
            'rewrite'               => false,
        );

        register_post_type('job_application', $args); // Register the post type `job_application`
    }

    public function job_application_columns($columns) {

        $columns = array(
            'cb'                 => '<input type="checkbox" />',
            'title'              => esc_html__('Applicant', 'jobly'),
            'applicant_id'       => esc_html__('Applicant ID', 'jobly'),
            'job_applied_for'    => esc_html__('Job', 'jobly'),
            'submission_time'    => esc_html__('Applied on', 'jobly'),
            'candidate_email'    => esc_html__('Email', 'jobly'),
            'candidate_phone'    => esc_html__('Phone', 'jobly'),
            'candidate_message'  => esc_html__('Message', 'jobly'),
            'candidate_cv'       => esc_html__('CV', 'jobly'),
        );

        return $columns;
    }

    public function job_application_columns_data($column, $post_id) {

        $job_id = get_post_meta($post_id, 'job_applied_for', true);
        $job_title = get_the_title($job_id);

        switch ($column) {

            case 'applicant_id':
                if ( current_user_can( 'edit_others_applications' ) ) {
                    edit_post_link( esc_html( $post_id ) );
                } else {
                    echo esc_html( $post_id );
                }
                break;
            case 'job_applied_for':
                if ($job_id && $job_title) {
                    echo '<a href="' . get_edit_post_link($job_id) . '">' . esc_html($job_title) . '</a>';
                } else {
                    echo 'Job title not found';
                }
                break;
            case 'submission_time':
                echo get_the_date('', $post_id);
                break;
            case 'candidate_email':
                echo esc_html(get_post_meta($post_id, 'candidate_email', true));
                break;
            case 'candidate_phone':
                echo esc_html(get_post_meta($post_id, 'candidate_phone', true));
                break;
            case 'candidate_message':
                echo esc_html(get_post_meta($post_id, 'candidate_message', true));
                break;
            case 'candidate_cv':
                $cv_id = get_post_meta($post_id, 'candidate_cv', true);
                if ($cv_id) {
                    $cv_url = wp_get_attachment_url($cv_id);
                    echo '<a href="' . esc_url($cv_url) . '" target="_blank">' . esc_html__('Download CV', 'jobly') . '</a>';
                }
                break;
        }
    }
}