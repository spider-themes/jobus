<?php
namespace Jobly\Admin\classes\templates;

use Jobly\Admin\classes\Job_Settings;

if (!defined('ABSPATH')) {
	exit;// Exit if accessed directly
}

$tab_menus = Job_Settings::get_settings_tab_menus();
$current_tab = isset($_GET['tab']) ? sanitize_title($_GET['tab']) : key($tab_menus);
?>
<div class="wrap jobly-job-settings-wrap" id="jobly-job-settings-wrap">
	<h1><?php esc_html_e('Settings', 'jobly'); ?></h1>

    <?php settings_errors(); ?>

    <div class="nav-tab-wrapper jobly-settings-tab-wrapper">
        <?php
        $settings_tabs = apply_filters('jobly_jobs_settings_tab_menus', $tab_menus);
        if ( !empty($settings_tabs) ) {
            foreach ( $settings_tabs as $index => $tab_item ) {
                $active_tab = ( $current_tab === $index ) ? ' nav-tab-active' : '';
                printf(
                    '<a href="%2$s" class="nav-tab%3$s">%1$s</a>',
                    esc_html( $tab_item ),
                    esc_url(
                        add_query_arg(
                            array(
                                'post_type' => 'job',
                                'page'      => 'jobly-jobs-settings',
                                'tab'       => $index,
                            ),
                            admin_url( 'edit.php' )
                        )
                    ),
                    esc_attr( $active_tab )
                );
            }
        }
        ?>
    </div>

    <!--<div class="jobly-jobs-settings-section-wrapper">

        <div class="jobly-jobs-settings-loader-container">
            <span class="jobly-jobs-settings-loader"><img src="<?php /*echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); */?>" width="32" height="32" alt="" /></span>
        </div>

        <div class="jobly-jobs-settings-section" id="jobly-jobs-settings-section">
            <?php
/*            $settings_filename = trailingslashit( plugin_dir_path( __FILE__ ) ) . $current_tab . '.php';
            if ( file_exists( $settings_filename ) ) {
                include_once $settings_filename;
            }
            do_action( 'jobly_jobs_settings_tab_section' );
            */?>
        </div>

    </div>-->

</div>

