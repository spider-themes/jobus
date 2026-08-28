<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( class_exists( 'CSF' ) ) {

	// Set a unique slug-like ID for meta options
	$meta_company_prefix = 'jobus_meta_company_options';

	CSF::createMetabox( $meta_company_prefix, array(
		'title'        => esc_html__( 'Company Options', 'jobus' ),
		'post_type'    => 'jobus_company',
		'theme'        => 'dark',
		'output_css'   => true,
		'show_restore' => true,
	) );

	// Company Info Meta Options
	CSF::createSection( $meta_company_prefix, array(
		'title'  => esc_html__( 'General', 'jobus' ),
		'id'     => 'jobus_meta_general',
		'icon'   => 'fas fa-home',
		'fields' => array(

			// Single Post Layout
			array(
				'type'    => 'subheading',
				'content' => esc_html__( 'Single Post Layout', 'jobus' ),
			),

			array(
				'id'       => 'company_single_sec_pad',
				'type'     => 'spacing',
				'title'    => esc_html__( 'Page Section Padding', 'jobus' ),
				'subtitle' => esc_html__( 'Top/bottom padding for this Company\'s details page. Leave empty to use the global default.', 'jobus' ),
				'top'      => true,
				'bottom'   => true,
				'left'     => false,
				'right'    => false,
				'output'   => '.jbs-company-details',
				'output_mode' => 'padding',
				'output_important' => true,
			),

			array(
				'id'      => 'post_favorite',
				'type'    => 'checkbox',
				'title'   => esc_html__( 'Favorite', 'jobus' ),
				'default' => false,
			),

			array(
				'id'      => 'company_website',
				'type'    => 'link',
				'title'   => esc_html__( 'Company Website URL', 'jobus' ),
				'default' => array(
					'url' => '#',
				),
			),

			array(
				'id'      => 'company_verified',
				'type'    => 'switcher',
				'title'   => esc_html__( 'Verified Company', 'jobus' ),
				'text_on' => esc_html__( 'Yes', 'jobus' ),
				'text_off' => esc_html__( 'No', 'jobus' ),
				'desc'    => esc_html__( 'Toggle to display a verified trust badge for this company.', 'jobus' ),
				'default' => false,
			),

		)
	) );



	// Retrieve the repeater field configurations from settings options
	$company_specifications = jobus_opt( 'company_specifications' );
	$company_fields         = [];

	if ( ! empty( $company_specifications ) ) {
		foreach ( $company_specifications as $field ) {

			$meta_value = $field['meta_values_group'] ?? [];
			$meta_icon  = ! empty( $field['meta_icon'] ) ? '<i class="' . $field['meta_icon'] . '"></i>' : '';
			$opt_values = [];
			$opt_val    = '';

			foreach ( $meta_value as $value ) {
				$modifiedString         = preg_replace( '/[,\s]+/', '@space@', $value['meta_values'] );
				$opt_val                = strtolower( $modifiedString );
				$opt_values[ $opt_val ] = $value['meta_values'];
			}

			if ( ! empty( $field['meta_key'] ) ) {
				$company_fields[] = [
					'id'       => $field['meta_key'] ?? '',
					'type'     => 'select',
					'title'    => esc_html( $field['meta_name'] ) ?? '',
					'options'  => $opt_values,
					'multiple' => true,
					'chosen'   => true,
					'after'    => $meta_icon,
					'class'    => 'job_specifications'
				];
			}
		}

		CSF::createSection( $meta_company_prefix, array(
			'title'  => esc_html__( 'Specifications', 'jobus' ),
			'fields' => $company_fields,
			'icon'   => 'fas fa-cogs',
			'id'     => 'jobus_meta_specifications',
		) );
	}


	// Social Icons
	CSF::createSection( $meta_company_prefix, array(
		'id'     => 'jobus_meta_social_icons', // Set a unique slug-like ID
		'title'  => esc_html__( 'Social Icons', 'jobus' ),
		'icon'   => 'fa fa-hashtag',
		'fields' => array(

			array(
				'id'           => 'social_icons',
				'type'         => 'repeater',
				'title'        => esc_html__( 'Social Icons', 'jobus' ),
				'subtitle'     => esc_html__( 'Customize and manage your social media icons along with respective URLs', 'jobus' ),
				'button_title' => esc_html__( 'Add Icon', 'jobus' ),
				'fields'       => array(
					array(
						'id'      => 'icon',
						'type'    => 'icon',
						'title'   => esc_html__( 'Icon', 'jobus' ),
						'default' => 'bi bi-facebook',
					),
					array(
						'id'      => 'url',
						'type'    => 'text',
						'title'   => esc_html__( 'URL', 'jobus' ),
						'default' => '#',
					),
				),
				'default'      => array(
					array(
						'icon' => 'bi bi-facebook',
						'url'  => '#',
					),
					array(
						'icon' => 'bi bi-instagram',
						'url'  => '#',
					),
					array(
						'icon' => 'bi bi-twitter',
						'url'  => '#',
					),
					array(
						'icon' => 'bi bi-linkedin',
						'url'  => '#',
					),
				),
			)
		)
	) ); //End Social Icons
}