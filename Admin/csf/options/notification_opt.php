<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Notification Template Settings
 * 
 * Allows users to customize the subject and body of emails sent to 
 * candidates and employers.
 *
 * @package Jobus
 */

if (! isset($settings_prefix)) {
    $settings_prefix = 'jobus_opt';
}

CSF::createSection($settings_prefix, array(
    'id'    => 'jobus_pro_notifications', // Keeping ID as is just to preserve potential dependencies/settings values.
    'title' => esc_html__('Email Templates', 'jobus'),
    'icon'  => 'fa fa-envelope-open-text',
    'fields' => array(

        array(
            'type'    => 'notice',
            'style'   => 'info',
            'content' => esc_html__('Customize the emails sent through the Jobus notification system. You can use global placeholders to personalize the messages.', 'jobus'),
        ),

        // Global Email Branding
        array(
            'type'    => 'heading',
            'content' => esc_html__('Global Email Branding', 'jobus'),
        ),
        array(
            'id'      => 'email_from_name',
            'type'    => 'text',
            'title'   => esc_html__('Sender Name', 'jobus'),
            'desc'    => esc_html__('Displays as the "From" name in emails.', 'jobus'),
            'default' => get_bloginfo('name'),
        ),
        array(
            'id'      => 'email_from_address',
            'type'    => 'text',
            'title'   => esc_html__('Sender Email Address', 'jobus'),
            'desc'    => esc_html__('Displays as the "From" email.', 'jobus'),
            'default' => get_option('admin_email'),
        ),
        array(
            'id'      => 'email_logo',
            'type'    => 'media',
            'title'   => esc_html__('Email Header Logo', 'jobus'),
            'desc'    => esc_html__('Brand your emails with your logo.', 'jobus'),
        ),
        array(
            'id'      => 'email_primary_color',
            'type'    => 'color',
            'title'   => esc_html__('Primary Accent Color', 'jobus'),
            'desc'    => esc_html__('Used for the accent bar in the email template.', 'jobus'),
            'default' => '#007bff'
        ),
        array(
            'id'      => 'email_footer_text',
            'type'    => 'wp_editor',
            'title'   => esc_html__('Footer Text', 'jobus'),
            'desc'    => esc_html__('Add physical address or social links to the bottom of all emails.', 'jobus'),
            'settings' => array(
                'textarea_rows' => 5,
                'media_buttons' => false,
            ),
        ),

        // Candidate Notification
        array(
            'type'    => 'heading',
            'content' => esc_html__('Candidate: Application Received', 'jobus'),
        ),
        array(
            'id'      => 'candidate_conf_subject',
            'type'    => 'text',
            'title'   => esc_html__('Email Subject', 'jobus'),
            'default' => esc_html__('Application Received: {job_title}', 'jobus'),
        ),
        array(
            'id'      => 'candidate_conf_body',
            'type'    => 'wp_editor',
            'title'   => esc_html__('Message Content', 'jobus'),
            'subtitle' => esc_html__('Available: {candidate_name}, {job_title}, {site_name}', 'jobus'),
            'default' => "Dear {candidate_name},\n\nThank you for your application for the \"{job_title}\" position.\n\nWe have successfully received your materials and our hiring team is now reviewing your application. \n\nWhat's next:\n- If your profile matches our requirements, we will contact you directly.\n- Most reviews are completed within 5-7 business days.\n\nThank you for your interest in joining us.\n\nBest regards,\n{site_name}",
        ),

        // Employer Notification
        array(
            'type'    => 'heading',
            'content' => esc_html__('Employer: New Application Alert', 'jobus'),
        ),
        array(
            'id'      => 'employer_notif_subject',
            'type'    => 'text',
            'title'   => esc_html__('Email Subject', 'jobus'),
            'default' => esc_html__('New Application for {job_title}', 'jobus'),
        ),
        array(
            'id'      => 'employer_notif_body',
            'type'    => 'wp_editor',
            'title'   => esc_html__('Message Content', 'jobus'),
            'subtitle' => esc_html__('Available: {employer_name}, {candidate_name}, {job_title}, {site_name}', 'jobus'),
            'default' => "Dear {employer_name},\n\nYou have received a new application for your job posting: \"{job_title}\".\n\nCandidate Name: {candidate_name}\n\nYou can review this application in your dashboard.\n\nBest regards,\n{site_name}",
        ),
    )
));
