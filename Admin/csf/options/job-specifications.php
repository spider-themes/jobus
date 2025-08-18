<?php
// Job Specifications
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Job Specifications', 'jobus' ),
	'id'     => 'jobus_job_specifications',
	'icon'   => 'fa fa-plus',
	'fields' => array(

		array(
			'id'       => 'job_specifications',
			'type'     => 'group',
			'title'    => esc_html__( 'Job Specifications', 'jobus' ),
			'subtitle' => esc_html__( 'Manage Job Specifications', 'jobus' ),
			'fields'   => array(

				array(
					'id'          => 'meta_name',
					'type'        => 'text',
					'title'       => esc_html__( 'Name', 'jobus' ),
					'placeholder' => esc_html__( 'Enter a specification', 'jobus' ),
					'after'       => esc_html__( 'Insert a unique name', 'jobus' ),
					'attributes'  => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id'          => 'meta_key',
					'type'        => 'text',
					'title'       => esc_html__( 'Key', 'jobus' ),
					'placeholder' => esc_html__( 'Specification key', 'jobus' ),
					'after'       => esc_html__( 'Insert a unique key', 'jobus' ),
					'attributes'  => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id'           => 'meta_values_group',
					'type'         => 'repeater',
					'title'        => esc_html__( 'Options', 'jobus' ),
					'button_title' => esc_html__( 'Add Option', 'jobus' ),
					'fields'       => array(
						array(
							'id'    => 'meta_values',
							'type'  => 'text',
							'title' => esc_html__( 'Meta Value', 'jobus' ),
						)
					)
				),

				array(
					'id'      => 'is_meta_icon',
					'type'    => 'button_set',
					'title'   => esc_html__( 'Meta Options (Icon/Image)', 'jobus' ),
					'options' => array(
						'meta_icon'  => esc_html__( 'Icon', 'jobus' ),
						'meta_image' => esc_html__( 'Image', 'jobus' )
					),
				),


				array(
					'id'          => 'meta_icon',
					'type'        => 'icon',
					'title'       => esc_html__( 'Icon (Optional)', 'jobus' ),
					'placeholder' => esc_html__( 'Select icon', 'jobus' ),
					'dependency'  => array( 'is_meta_icon', '==', 'meta_icon' ),
				),

				array(
					'id'          => 'meta_image',
					'type'        => 'media',
					'title'       => esc_html__( 'Image (Optional)', 'jobus' ),
					'placeholder' => esc_html__( 'Upload a Image', 'jobus' ),
					'dependency'  => array( 'is_meta_icon', '==', 'meta_image' ),
				)
			)
		)// End job specifications
	)
) );