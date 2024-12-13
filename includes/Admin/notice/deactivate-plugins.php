<?php
/**
 * Notice
 * Deactivate the JOBLY plugin
 *
 * @return void
 */
add_action( 'admin_notices', function () {
    if ( is_plugin_active( 'jobly/jobly.php' ) ) :
        ?>
        <div class="notice notice-warning eaz-notice">
            <p>
                <?php esc_html_e( 'We have renamed the Jobly plugin to Jobus according to WordPress.org guidelines.', 'jobus' ); ?> <br>
                <?php esc_html_e( 'Please deactivate the Jobly plugin and migrate your data to avoid conflicts with the new Jobus plugin.', 'jobus' ); ?>
            </p>

            <a href="<?php echo esc_url(wp_nonce_url(admin_url('?jobus-deactivate-plugin=jobly'), 'jobus_deactivate_plugin_nonce')); ?>" class="button-primary">
                <?php esc_html_e('Deactivate Jobly and Migrate Data', 'jobus'); ?>
            </a>

        </div>
    <?php
    endif;
} );

/**
 * Deactivate the Jobly plugin action
 */
if ( ! empty( $_GET['jobus-deactivate-plugin'] ) ) {


    add_action('admin_init', function () {
        // Ensure the user has permissions to activate/deactivate plugins
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $plugin = sanitize_text_field($_GET['jobus-deactivate-plugin']);

        // Verify nonce for security
        if (!wp_verify_nonce($_GET['_wpnonce'], 'jobus_deactivate_plugin_nonce')) {
            wp_die(esc_html__('Invalid request. Please try again.', 'jobus'));
        }

        // Deactivate the Jobly plugin if active
        if (is_plugin_active("$plugin/jobly.php")) {
            deactivate_plugins("$plugin/jobly.php");

            // Migrate data after deactivation
            jobus_migrate_cpt_and_tax_and_metadata();

            // Redirect to the plugin page with a success message
            wp_safe_redirect(admin_url('plugins.php?migrated=1'));
        } else {
            // Redirect if Jobly is not active
            wp_safe_redirect(admin_url('plugins.php?jobly-not-active=1'));
        }
        exit;

    });

}


/**
 * Show success or error messages
 */
add_action('admin_notices', function () {
    if (isset($_GET['migrated']) && $_GET['migrated'] === '1') {
        ?>
        <div class="notice notice-success">
            <p><?php esc_html_e('The Jobly plugin has been successfully deactivated, and your data has been migrated to the new Jobus plugin structure.', 'jobus'); ?></p>
        </div>
        <?php
    }
});


/**
 * Migrate
 * Custom Post-Types,
 * Taxonomies,
 * Page Meta Keys,
 * Settings
 */
function jobus_migrate_cpt_and_tax_and_metadata(): void {
    global $wpdb;

    // Step 1: Migrate Post-Types
    $post_type_mappings = [
        'candidate' => 'jobus_candidate',
        'job'       => 'jobus_job',
        'company'   => 'jobus_company',
    ];

    foreach ($post_type_mappings as $old_post_type => $new_post_type) {
        $wpdb->query(
            $wpdb->prepare("UPDATE $wpdb->posts SET post_type = %s WHERE post_type = %s", $new_post_type, $old_post_type)
        );
    }

    // Step 2: Migrate Taxonomies
    $taxonomy_mapping = array(
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

    foreach ($taxonomy_mapping as $old_taxonomy => $new_taxonomy) {
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->term_taxonomy} SET taxonomy = %s WHERE taxonomy = %s",
            $new_taxonomy,
            $old_taxonomy
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

    foreach ($meta_key_mappings as $old_meta_key => $new_meta_key) {
        $wpdb->query(
            $wpdb->prepare("UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s", $new_meta_key, $old_meta_key)
        );
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

    // Step 6: Migrate Elementor Widget Names
    $widget_name_mappings = array(
        'jobly_job_categories' => 'jobus_job_categories',
        'jobly_companies' => 'jobus_companies',
        'jobly_job_tabs' => 'jobus_job_tabs',
        'jobly_job_listing' => 'jobus_job_listing',
        'jobly_search_Form' => 'jobus_search_Form',

    );

    $elementor_data = $wpdb->get_results("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data'");

    foreach ($elementor_data as $data) {
        $meta_id = $data->meta_id;
        $meta_value = json_decode($data->meta_value, true);
        if ($meta_value) {
            array_walk_recursive($meta_value, function (&$value) use ($widget_name_mappings) {
                if (isset($widget_name_mappings[$value])) {
                    $value = $widget_name_mappings[$value];
                }
            });
            $wpdb->update($wpdb->postmeta, ['meta_value' => wp_json_encode($meta_value)], ['meta_id' => $meta_id]);
        }
    }

}