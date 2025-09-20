<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Company Specifications
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Company Specifications', 'jobus' ),
	'id'     => 'jobus_company_specifications',
	'icon'   => 'fa fa-plus',
	'fields' => array(

		array(
			'id'       => 'company_specifications',
			'type'     => 'group',
			'title'    => esc_html__( 'Company Specifications', 'jobus' ),
			'subtitle' => esc_html__( 'Manage Company Specifications', 'jobus' ),
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
							'title' => null,
						)
					)
				),

				array(
					'id'          => 'meta_icon',
					'type'        => 'icon',
					'title'       => esc_html__( 'Icon (Optional)', 'jobus' ),
					'placeholder' => esc_html__( 'Select icon', 'jobus' ),
				)
			)
		)// End job specifications
	)
) );