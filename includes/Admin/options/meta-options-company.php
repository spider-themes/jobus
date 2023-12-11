<?php
if (class_exists('CSF')) {

    // Set a unique slug-like ID for meta options
    $meta_prefix = 'jobly_meta_options';

    CSF::createMetabox($meta_prefix, array(
        'title' => esc_html__('Company Options', 'jobly'),
        'post_type' => 'company',
        'theme' => 'dark',
        'output_css' => true,
        'show_restore' => true,
    ));

    // Retrieve the repeater field configurations from settings options
    $specifications = jobly_opt('company_specifications');
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