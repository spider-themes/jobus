<?php
namespace Jobus\Admin\CPT;

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
        if ($post->post_type === 'jobus_job_application') {
            $candidate_ip = get_post_meta($post->ID, 'candidate_ip', true);
            ?>
            <p class="jobus-application-submission-info">
                <span class="jobus-application-submission-date">
                    <?php esc_html_e('Submitted On ', 'jobus'); ?> <?php echo esc_html( get_the_time(get_option('date_format'))) ?></span>
                <span class="jobus-applicant-ip"><?php esc_html_e('from IP', 'jobus'); ?> <?php echo esc_html($candidate_ip); ?></span>
            </p>
            <?php
        }
    }

    public function admin_single_contents()
    {
        add_meta_box(
            'applicant-details-meta-box',
            esc_html__('Applicant Details', 'jobus'),
            array($this, 'render_single_contents'),
            'jobus_job_application'
        );
    }

    public function render_single_contents($post)
    {

        if ($post->post_type === 'jobus_job_application') {
            require_once plugin_dir_path(__FILE__) . '../templates/meta/applicant-single.php';
        }

    }



    // Register the post type Applications
    public function register_post_types_applications() {
        $labels = array(
            'name'                  => esc_html__('Applications', 'jobus'),
            'singular_name'         => esc_html__('Application', 'jobus'),
            'menu_name'             => esc_html__('Applications', 'jobus'),
            'edit_item'             => esc_html__('Applications', 'jobus'),
            'search_items'          => esc_html__('Search Applications', 'jobus'),
            'not_found'             => esc_html__('No Applications found', 'jobus'),
            'not_found_in_trash'    => esc_html__('No Applications found in Trash', 'jobus'),
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

        register_post_type('jobus_job_application', $args); // Register the post type `job_application`
    }

    public function job_application_columns($columns) {

        $columns = array(
            'cb'                 => '<input type="checkbox" />',
            'applicant_photo'    => '',
            'title'              => esc_html__('Applicant', 'jobus'),
            'applicant_id'       => esc_html__('ID', 'jobus'),
            'job_applied_for'    => esc_html__('Job', 'jobus'),
            'submission_time'    => esc_html__('Applied on', 'jobus'),
        );

        return $columns;
    }

    public function job_application_columns_data($column, $post_id) {

        switch ($column) {
            case 'applicant_photo':
                $candidate_email = get_post_meta($post_id, 'candidate_email', true);
                echo get_avatar($candidate_email, 40);
                break;
            case 'applicant_id':
                echo esc_html( $post_id );
                break;
            case 'job_applied_for':
                $job_id = get_post_meta($post_id, 'job_applied_for_id', true);
                $job_title = get_post_meta($post_id, 'job_applied_for_title', true);
                if ($job_id && $job_title) {
                    echo '<a href="' . esc_url(get_edit_post_link($job_id)) . '">' . esc_html($job_title) . '</a>';
                }
                break;
            case 'submission_time':
                echo get_the_date('', $post_id);
                break;
        }
    }
}