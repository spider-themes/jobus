<?php
if ( class_exists( 'CSF' ) ) {

	// Set a unique slug-like ID for meta options
	$meta_prefix = 'jobly_meta';

	// Create a metabox
	CSF::createMetabox( $meta_prefix, array(
		'title'        => esc_html__( 'Job Options', 'jobly' ),
		'post_type'    => 'job',
		'theme'        => 'dark',
		'output_css'   => true,
		'show_restore' => true,
	) );


	// Company Info Meta Options
	CSF::createSection( $meta_prefix, array(
		'title'  => esc_html__( 'Company Info', 'jobly' ),
        'id' => 'company_info',
		'fields' => array(

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
            ),

		)
	) );

}

