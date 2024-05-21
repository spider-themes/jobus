<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (class_exists('CSF')) {

    // Set a unique slug-like ID for meta options
    $meta_prefix = 'jobly_meta_options';

    CSF::createMetabox($meta_prefix, array(
        'title' => esc_html__('Job Options', 'jobly'),
        'post_type' => 'job',
        'theme' => 'dark',
        'output_css' => true,
        'show_restore' => true,
    ));

    // Company Info Meta Options
    CSF::createSection($meta_prefix, array(
        'title' => esc_html__('General', 'jobly'),
        'id' => 'jobly_meta_general',
        'icon' => 'fas fa-home',
        'fields' => array(

            // Single Post Layout
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Single Post Layout', 'jobly'),
            ),

            array(
                'id'        => 'job_details_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobly'),
                'subtitle'  => esc_html__('Select the preferred layout for your job post for this page.', 'jobly'),
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/job/single-layout-1.png',
                    '2' => JOBLY_IMG . '/layout/job/single-layout-2.png',
                ),
                'default'   => '1'
            ),


            // Company Information
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Company Info', 'jobly'),
            ),

            array(
                'id' => 'select_company',
                'type' => 'select',
                'title' => esc_html__('Select Company', 'jobly'),
                'options' => jobly_company_post_list(),
                'chosen' => true,
                'default' => '',
            ),

            array(
                'id'         => 'is_company_website',
                'type'       => 'button_set',
                'title'      => esc_html__('Company Website', 'jobly'),
                'options'    => array(
                    'default'  => esc_html__('Default', 'jobly'),
                    'custom' => esc_html__('Custom', 'jobly'),
                ),
                'default'    => 'default'
            ),

            array(
                'id'       => 'company_website',
                'type'     => 'link',
                'title'    => esc_html__('Website Button', 'jobly'),
                'default'  => array(
                    'url'    => '#',
                ),
                'dependency' => array('is_company_website', '==', 'custom'),
            ), // End company information


            //====================== Job Information ======================//
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Information', 'jobly'),
            ),

            array(
                'id'         => 'is_apply_btn',
                'type'       => 'button_set',
                'title'      => esc_html__('Choose Option', 'jobly'),
                'desc'       => esc_html__('Choose the Apply button for job post.', 'jobly'),
                'options'    => array(
                    'default'  => esc_html__('Default', 'jobly'),
                    'custom'  => esc_html__('Custom with Link', 'jobly'),
                ),
                'default'    => 'default'
            ),

            array(
                'id'       => 'apply_form_url',
                'type'     => 'text',
                'title'    => esc_html__('Apply Link', 'jobly'),
                'default'  => '#',
                'dependency' => array('is_apply_btn', '==', 'custom'),
            ),

        )
    ));

    // Retrieve the repeater field configurations from settings options    
    $specifications = jobly_opt('job_specifications');
    if (is_array($specifications)) {
        foreach ($specifications as $field) {

            $meta_value     = $field['meta_values_group'] ?? [];
            $opt_values     = [];
            $opt_val        = '';


            // Determine meta options based on the value of the switcher
            $is_meta_icon = isset($field['is_meta_icon']) ? $field['is_meta_icon'] : '';
            $meta_options = '';

            if ($is_meta_icon == 'meta_icon' && !empty($field['meta_icon'])) {
                $meta_options = '<i class="' . esc_attr($field['meta_icon']) . '"></i>';
            } elseif ($is_meta_icon == 'meta_image' && !empty($field['meta_image'])) {
                $meta_options = '<img src="'.esc_url($field['meta_image']['url']).'" alt="" class="lazy-img m-auto icon">';
            }

            foreach ($meta_value as $value) {
                $modifiedString = preg_replace('/[,\s]+/', '@space@', $value['meta_values'] ?? '');
                $opt_val = strtolower($modifiedString);
                $opt_values[$opt_val] = $value['meta_values'] ?? '';
            }

            if (!empty($field['meta_key'])) {
                $fields[] = [
                    'id' => $field['meta_key'] ?? '',
                    'type' => 'select',
                    'title' => $field['meta_name'] ?? '',
                    'options' => $opt_values,
                    'multiple' => true,
                    'chosen' => true,
                    'after' => $meta_options,
                    'class' => 'job_specifications'
                ];
            }
        }

        CSF::createSection($meta_prefix, array(
            'title' => esc_html__('Specifications', 'jobly'),
            'fields' => $fields,
            'icon'   => 'fas fa-cogs',
            'id'     => 'jobly_meta_specifications',
        ));
    }
}
