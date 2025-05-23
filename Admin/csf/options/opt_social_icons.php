<?php
// Social Icons
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_social_icons', // Set a unique slug-like ID
	'title'  => esc_html__( 'Social Icons', 'jobus' ),
	'icon'   => 'fa fa-hashtag',
	'fields' => array(

		array(
			'id'           => 'jobus_social_icons',
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