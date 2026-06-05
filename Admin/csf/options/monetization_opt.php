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

            array(
                'type'       => 'heading',
                'content'    => esc_html__('Membership Display Text', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
            ),

            array(
                'id'         => 'membership_current_plan_title',
                'type'       => 'text',
                'title'      => esc_html__('Current Plan Title', 'jobus'),
                'subtitle'   => esc_html__('Title above the active plan details.', 'jobus'),
                'default'    => esc_html__('Current Plan', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_current_plan_subtitle',
                'type'       => 'textarea',
                'title'      => esc_html__('Current Plan Subtitle', 'jobus'),
                'subtitle'   => esc_html__('Description text below the current plan title.', 'jobus'),
                'default'    => esc_html__('Your current active job listing package limits and access duration.', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_credits_label',
                'type'       => 'text',
                'title'      => esc_html__('Credits Label', 'jobus'),
                'subtitle'   => esc_html__('Text before the active credits amount.', 'jobus'),
                'default'    => esc_html__('Credits Remaining: ', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_upgrade_plan_label',
                'type'       => 'text',
                'title'      => esc_html__('Upgrade Plan Button Label', 'jobus'),
                'subtitle'   => esc_html__('Text for the link that scrolls to the pricing table.', 'jobus'),
                'default'    => esc_html__('Upgrade Plan', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_popular_badge_label',
                'type'       => 'text',
                'title'      => esc_html__('Featured Package Badge Label', 'jobus'),
                'subtitle'   => esc_html__('Text displayed on the badge for WooCommerce Featured packages.', 'jobus'),
                'default'    => esc_html__('Popular', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_choose_plan_label',
                'type'       => 'text',
                'title'      => esc_html__('Choose Plan Button Label', 'jobus'),
                'subtitle'   => esc_html__('Text for the select plan button in the pricing table.', 'jobus'),
                'default'    => esc_html__('Choose Plan', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_choose_plan_color',
                'type'       => 'color',
                'title'      => esc_html__('Choose Plan Button Text Color', 'jobus'),
                'subtitle'   => esc_html__('Text color of the select plan button.', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
            ),

            array(
                'id'         => 'membership_choose_plan_bg_color',
                'type'       => 'color',
                'title'      => esc_html__('Choose Plan Button Background Color', 'jobus'),
                'subtitle'   => esc_html__('Background color of the select plan button.', 'jobus'),
                'dependency' => array('enable_job_packages', '==', 'true'),
                'class'      => trim($pro_access_class . $active_theme_class)
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
