<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit(); // Exit if accessed directly.
}
/**
 * Notice to deactivate the Jobly Plugin
 *
 * @return void
 */
add_action( 'admin_notices', function () {

    // Check if the Jobly plugin is active and migration is not done
    if ( is_plugin_active( 'jobly/jobly.php' ) && !get_option( 'jobus_data_migrated' ) ) {
        ?>
        <div class="notice notice-warning eaz-notice">
            <p>
                <?php esc_html_e( 'We have renamed the Jobly plugin to Jobus according to WordPress.org guidelines.', 'jobus' ); ?> <br>
                <?php esc_html_e( 'Please deactivate the Jobly plugin and migrate your data to avoid conflicts with the new Jobus plugin.', 'jobus' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( wp_nonce_url( admin_url( '?jobus-deactivate-migrate=true' ), 'jobus_deactivate_migrate_nonce' ) ); ?>" class="button-primary button-large">
                    <?php esc_html_e( 'Deactivate Jobly and Migrate Data', 'jobus' ); ?>
                </a>
            </p>
        </div>
        <?php
    }

});


/**
 * Show a success message after migration
 */
add_action('admin_notices', function() {
    if ( isset( $_GET['migrated'] ) && $_GET['migrated'] === '1' ) {
        ?>
        <div class="notice notice-success">
            <p><?php _e('The Jobly plugin has been successfully deactivated, and your data has been migrated to the new Jobus plugin structure.', 'jobus'); ?></p>
        </div>
        <?php
    }
});



/**
 * Deactivate the Jobly plugin action
 */
add_action( 'admin_init', function() {

    // Check if the deactivation of Jobly is triggered
    if ( isset( $_GET['jobus-deactivate-plugin'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'jobus_deactivate_plugin_nonce' ) ) {

        // Check if the current user can activate/deactivate plugins
        if ( current_user_can( 'activate_plugins' ) ) {
            // Deactivate the Jobly plugin
            $plugin = sanitize_text_field( $_GET['jobus-deactivate-plugin'] );
            if ( $plugin === 'jobly' ) {
                deactivate_plugins( 'jobly/jobly.php' );
                wp_safe_redirect( admin_url( 'plugins.php?deactivated=1' ) );
                exit;
            }
        }
    }


    // Check if the deactivation and migration is triggered
    if ( isset( $_GET['jobus-deactivate-migrate'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'jobus_deactivate_migrate_nonce' ) ) {

        // Check if the current user can activate/deactivate plugins
        if ( current_user_can( 'activate_plugins' ) ) {
            // Deactivate the Jobly plugin if still active
            if ( is_plugin_active( 'jobly/jobly.php' ) ) {
                deactivate_plugins( 'jobly/jobly.php' );
            }

            // Run the data migration logic
            jobus_migrate_cpt_and_tax_and_metadata();

            // Mark the migration as complete to prevent this notice from showing again
            update_option( 'jobus_data_migrated', true );

            // Redirect with a success message
            wp_safe_redirect( admin_url( 'plugins.php?migrated=1' ) );
            exit;
        }
    }



});



/**
 * Migrate
 * Custom Post Types,
 * Taxonomies,
 * Page Meta Keys,
 * Settings
 */
function jobus_migrate_cpt_and_tax_and_metadata(): void {
    global $wpdb;

    // Step 1: Migrate Post-Types
    $post_type_mappings = array(
        'candidate'         => 'jobus_candidate',
        'job'               => 'jobus_job',
        'company'           => 'jobus_company',
    );

    foreach ( $post_type_mappings as $old_post_type => $new_post_type ) {
        $wpdb->query( $wpdb->prepare("
            UPDATE $wpdb->posts SET post_type = %s WHERE post_type = %s", $new_post_type, $old_post_type
        ));
    }

    // Step 2: Migrate Taxonomies
    $taxonomy_mappings = array(
        'job_cat'               => 'jobus_job_cat',
        'job_location'          => 'jobus_job_location',
        'job_tag'               => 'jobus_job_tag',
        'candidate_cat'         => 'jobus_candidate_cat',
        'candidate_location'    => 'jobus_candidate_location',
        'candidate_skill'       => 'jobus_candidate_skill',
        'company_cat'           => 'jobus_company_cat',
        'company_location'      => 'jobus_company_location',
        'job_application'       => 'jobus_applicant',
    );

    foreach ( $taxonomy_mappings as $old_taxonomy => $new_taxonomy ) {
        $wpdb->query( $wpdb->prepare("
            UPDATE $wpdb->term_taxonomy SET taxonomy = %s WHERE taxonomy = %s", $new_taxonomy, $old_taxonomy
        ));
    }

    // Step 3: Migrate Taxonomy Meta Keys (jobly_taxonomy_cat to jobus_taxonomy_cat)
    $meta_tax_mappings = array(
        'jobly_taxonomy_cat' => 'jobus_taxonomy_cat',
    );

    foreach ( $meta_tax_mappings as $old_meta_tax => $new_meta_tax ) {
        // Migrate taxonomy meta keys
        $wpdb->query( $wpdb->prepare("
            UPDATE $wpdb->termmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_tax, $old_meta_tax
        ));
    }

    // Step 4: Migrate Meta Keys
    $meta_key_mappings = array(
        'jobly_meta_candidate_options' => 'jobus_meta_candidate_options',
        'jobly_meta_company_options'   => 'jobus_meta_company_options',
        'jobly_meta_options'           => 'jobus_meta_options',
    );

    foreach ( $meta_key_mappings as $old_meta_key => $new_meta_key ) {
        // Update post meta keys from old to new
        $wpdb->query( $wpdb->prepare("
            UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_key, $old_meta_key
        ));
    }

    // Step 5: Migrate Plugin Options
    $option_mappings = array(
        'jobly_opt' => 'jobus_opt',
    );

    foreach ( $option_mappings as $old_option => $new_option ) {
        // Check if the old option exists
        $old_option_value = get_option( $old_option );
        if ( false !== $old_option_value ) {
            // Update the option with the new key
            update_option( $new_option, $old_option_value );
            // Optionally delete the old option to prevent redundancy
            delete_option( $old_option );
        }
    }
}