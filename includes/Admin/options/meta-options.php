<?php
if ( class_exists( 'CSF' ) ) {

    // Set a unique slug-like ID for meta options
    $meta_prefix = 'jobly_meta_options';

    CSF::createMetabox( $meta_prefix, array(
		'title'        => esc_html__( 'Job Options', 'jobly' ),
		'post_type'    => 'job',
		'theme'        => 'dark',
		'output_css'   => true,
		'show_restore' => true,
	) );
    
    // Company Info Meta Options
	CSF::createSection( $meta_prefix, array(
		'title'     => esc_html__( 'General', 'jobly' ),
        'id'        => 'jobly_meta_general',
        'icon'      => 'fas fa-home',
		'fields'    => array(

            array(
                'id'    => 'company_logo',
                'type'  => 'media',
                'title' => esc_html__( 'Logo', 'jobly' ),
            ),

            array(
                'id'    => 'company_name',
                'type'  => 'text',
                'title' => esc_html__( 'Name', 'jobly' ),
                'default' => esc_html__('SpiderDevs', 'jobly')
            ),

            array(
                'id'    => 'company_website',
                'type'  => 'link',
                'title' => esc_html__( 'Website URL', 'jobly' ),
            )
		)
	) );
    
    // Retrieve the repeater field configurations from settings options    
    $specifications     = jobly_opt( 'job_specifications' );

    if ( is_array( $specifications ) ) {

        foreach ( $specifications as $field ) {
        
            $meta_value     = $field['meta_values_group'] ?? [];  
            $meta_icon      = ! empty ( $field['meta_icon'] ) ? '<i class="'.$field['meta_icon'].'"></i>' : '';  
            $opt_values     = [];
            $opt_val        = '';

            foreach ($meta_value as $value) {
                $opt_val = str_replace(' ', '@space@', $value['meta_values']);
                $opt_values[$opt_val] = $value['meta_values'];
            }
            
            if ( ! empty ( $field['meta_key'] ) ) {
                $fields[] = [
                    'id'       => $field['meta_key'] ?? '',
                    'type'     => 'select',
                    'title'    => $field['meta_name'] ?? '',
                    'options'  => $opt_values,
                    'multiple' => true,
                    'chosen'   => true,
                    'after'    => $meta_icon,
                    'class'    => 'job_specifications'
                ];
            }
        }

        CSF::createSection( $meta_prefix, array(
            'title'  => esc_html__( 'Specifications', 'jobly' ),
            'fields' => $fields,
            'icon'   => 'fas fa-cogs',
        ) );

        CSF::createSection( $meta_prefix, array(
            'title'  => esc_html__( 'test', 'jobly' ),
            'icon'   => 'fas fa-cogs',
            'fields' => [
                [
                    'id'       => 'ddddddd',
                    'type'     => 'select',
                    'title'    =>  'dddddddddddddd',
                    'options'  => [
                        'fdsfds'  => 'fdsfdsd',
                        'fdsfd 2 s'  => 'fdsfdsd',
                        'fdsfds 3'  => 'fdsfdsd',
                    ],
                    'default' => 'fdsfds',
                    'multiple' => true,
                    'chosen'   => true,
                ]
            ]
        ) );
    }
}