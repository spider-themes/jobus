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
 * @package Jobus_Pro
 */

if (! isset($settings_prefix)) {
    $settings_prefix = 'jobus_opt';
}

CSF::createSection($settings_prefix, array(
    'id'    => 'jobus_pro_notifications',
    'title' => esc_html__('Email Templates', 'jobus-pro'),
    'icon'  => 'fa fa-envelope-open-text',
    'fields' => array(

        array(
            'type'    => 'notice',
            'style'   => 'info',
            'content' => esc_html__('Customize the emails sent through the Jobus notification system. You can use global placeholders to personalize the messages.', 'jobus-pro'),
        ),

        // Candidate Notification
        array(
            'type'    => 'heading',
            'content' => esc_html__('Candidate: Application Received', 'jobus-pro'),
        ),
        array(
            'id'      => 'candidate_conf_subject',
            'type'    => 'text',
            'title'   => esc_html__('Email Subject', 'jobus-pro'),
            'default' => esc_html__('Application Received: {job_title}', 'jobus-pro'),
        ),
        array(
            'id'      => 'candidate_conf_body',
            'type'    => 'wp_editor',
            'title'   => esc_html__('Message Content', 'jobus-pro'),
            'subtitle' => esc_html__('Available: {candidate_name}, {job_title}, {site_name}', 'jobus-pro'),
            'default' => "Dear {candidate_name},\n\nThank you for your application for the \"{job_title}\" position.\n\nWe have successfully received your materials and our hiring team is now reviewing your application. \n\nWhat's next:\n- If your profile matches our requirements, we will contact you directly.\n- Most reviews are completed within 5-7 business days.\n\nThank you for your interest in joining us.\n\nBest regards,\n{site_name}",
        ),

        // Employer Notification
        array(
            'type'    => 'heading',
            'content' => esc_html__('Employer: New Application Alert', 'jobus-pro'),
        ),
        array(
            'id'      => 'employer_notif_subject',
            'type'    => 'text',
            'title'   => esc_html__('Email Subject', 'jobus-pro'),
            'default' => esc_html__('New Application for {job_title}', 'jobus-pro'),
        ),
        array(
            'id'      => 'employer_notif_body',
            'type'    => 'wp_editor',
            'title'   => esc_html__('Message Content', 'jobus-pro'),
            'subtitle' => esc_html__('Available: {employer_name}, {candidate_name}, {job_title}, {site_name}', 'jobus-pro'),
            'default' => "Dear {employer_name},\n\nYou have received a new application for your job posting: \"{job_title}\".\n\nCandidate Name: {candidate_name}\n\nYou can review this application in your dashboard.\n\nBest regards,\n{site_name}",
        ),
    )
));
