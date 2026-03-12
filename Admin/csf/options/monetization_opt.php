<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (class_exists('WooCommerce')) {
    CSF::createSection($settings_prefix, array(
        'title'  => esc_html__('Monetization (WC)', 'jobus'),
        'id'     => 'jobus_monetization',
        'icon'   => 'fa fa-money',
        'fields' => array(
            array(
                'id'       => 'enable_job_packages',
                'type'     => 'switcher',
                'title'    => esc_html__('Enable Job Packages', 'jobus'),
                'subtitle' => esc_html__('Enable WooCommerce integration to sell job listing packages.', 'jobus'),
                'default'  => false,
                'class'    => trim($pro_access_class . $active_theme_class)
            ),
        )
    ));
} else {
    $install_url = admin_url('plugin-install.php?s=WooCommerce&tab=search&type=term');

    CSF::createSection($settings_prefix, array(
        'title'  => esc_html__('Monetization (WC)', 'jobus'),
        'id'     => 'jobus_monetization',
        'icon'   => 'fa fa-money',
        'fields' => array(
            array(
                'type'    => 'content',
                'content' => '<div style="padding: 15px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; margin-bottom: 20px;">'
                    . '<p style="margin: 0 0 8px 0; font-weight: 600;">' . esc_html__('WooCommerce Required', 'jobus') . '</p>'
                    . '<p style="margin: 0 0 15px 0; font-size: 13px;">' . esc_html__('You need to install and activate the free WooCommerce plugin to use the Jobus Pro Monetization features. WooCommerce handles the checkout, payment gateways, and order management for job packages.', 'jobus') . '</p>'
                    . '<a href="' . esc_url($install_url) . '" class="button button-primary" style="text-decoration: none;">' . esc_html__('Install WooCommerce', 'jobus') . '</a>'
                    . '</div>',
            ),
        )
    ));
}
