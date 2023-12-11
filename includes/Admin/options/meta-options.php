<?php
if (class_exists('CSF')) {

    // Set a unique slug-like ID for meta options
    $meta_prefix = 'jobly_meta_options';

    CSF::createMetabox($meta_prefix, array(
        'title' => esc_html__('Job Options', 'jobly'),
        'post_type' => 'job',
        'theme' => 'dark',
        'output_css' => true,
        'show_restore' => true,
        'data_type' => 'unserialize',
    ));

    // Company Info Meta Options
    CSF::createSection($meta_prefix, array(
        'title' => esc_html__('General', 'jobly'),
        'id' => 'jobly_meta_general',
        'icon' => 'fas fa-home',
        'fields' => array(

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

        )
    ));

    // Retrieve the repeater field configurations from settings options    
    $specifications = jobly_opt('job_specifications');
    if (is_array($specifications)) {
        foreach ($specifications as $field) {

            $meta_value     = $field['meta_values_group'] ?? [];
            $meta_icon      = !empty($field['meta_icon']) ? '<i class="' . $field['meta_icon'] . '"></i>' : '';
            $opt_values     = [];
            $opt_val        = '';

            foreach ($meta_value as $value) {
                $modifiedString = preg_replace('/[,\s]+/', '@space@', $value['meta_values']);
                $opt_val = strtolower($modifiedString);
                $opt_values[$opt_val] = $value['meta_values'];
            }

            if (!empty($field['meta_key'])) {
                $fields[] = [
                    'id' => $field['meta_key'] ?? '',
                    'type' => 'select',
                    'title' => $field['meta_name'] ?? '',
                    'options' => $opt_values,
                    'multiple' => true,
                    'chosen' => true,
                    'after' => $meta_icon,
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
