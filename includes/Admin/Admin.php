<?php
namespace Jobly\Admin;

/**
 * Class Admin
 *
 * @package Jobly\Admin
 */
class Admin {

    public function __construct() {
        // Custom Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'add_favorite_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_favorite_meta_box_data' ) );
    }

    /**
     * Add Favorite Meta Box
     */
    public function add_favorite_meta_box() {
        add_meta_box(
            'favorite_meta_box',
            esc_html__( 'Favorite', 'jobly' ),
            array( $this, 'favorite_meta_box_callback' ),
            'company',
            'side',
            'high'
        );
    }

    /**
     * Meta box content callback function
     *
     * @param WP_Post $post Current post object.
     */
    public function favorite_meta_box_callback($post) {
        // Get the current value of the 'favorite' meta-field
        $favorite = get_post_meta($post->ID, 'post_favorite', true);

        // Use nonce for verification
        wp_nonce_field(basename(__FILE__), 'favorite_nonce');

        // Output checkbox
        ?>
        <label for="favorite">
            <input type="checkbox" name="favorite" id="favorite" <?php checked($favorite, 'on'); ?> />
            <?php esc_html_e('Favorite', 'text-domain'); ?>
        </label>
        <?php
    }

    /**
     * Save Favorite Meta Box Data
     *
     * @param int $post_id Post ID.
     */
    public function save_favorite_meta_box_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['favorite_nonce'])) {
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['favorite_nonce'], basename(__FILE__))) {
            return;
        }

        // Check if the user has permissions to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the checkbox value
        $favorite = isset($_POST['favorite']) ? 'on' : 'off';
        update_post_meta($post_id, 'post_favorite', $favorite);
    }
}