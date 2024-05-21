<?php
if (class_exists('CSF')) {

    // Set a unique slug-like ID for meta options
    $meta_candidate_prefix = 'jobly_meta_candidate_options';

    CSF::createMetabox($meta_candidate_prefix, array(
        'title' => esc_html__('Candidate Options', 'jobly'),
        'post_type' => 'candidate',
        'theme' => 'dark',
        'output_css' => true,
        'show_restore' => true,
    ));

    // Company Info Meta Options
    CSF::createSection($meta_candidate_prefix, array(
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
		        'id'        => 'candidate_profile_layout',
		        'type'      => 'image_select',
		        'title'     => esc_html__('Choose Layout', 'jobly'),
		        'subtitle'  => esc_html__('Select the preferred layout for your candidate post for this page.', 'jobly'),
		        'options'   => array(
			        '1' => JOBLY_IMG . '/layout/candidate/candidate-profile-1.png',
			        '2' => JOBLY_IMG . '/layout/candidate/candidate-profile-2.png',
		        ),
		        'default'   => '1'
	        ),

            array(
                'id' => 'post_favorite',
                'type' => 'checkbox',
                'title' => esc_html__('Favorite', 'jobly'),
                'default' => false,
            ),

            array(
                'id'                => 'cv_attachment',
                'type'              => 'upload',
                'title'             => esc_html__( 'CV Attachment', 'jobly' ),
                'button_title'      => esc_html__( 'Add or Upload Files', 'jobly' ),
                'remove_title'      => esc_html__( 'Remove', 'jobly' ),
            )

        )
    ));


    // Retrieve the repeater field configurations from settings options
    $candidate_specifications = jobly_opt('candidate_specifications');
    if (!empty($candidate_specifications)) {
        foreach ($candidate_specifications as $field) {

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
                $candidate_fields[] = [
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

        CSF::createSection($meta_candidate_prefix, array(
            'title' => esc_html__('Specifications', 'jobly'),
            'fields' => $candidate_fields,
            'icon'   => 'fas fa-cogs',
            'id'     => 'jobly_meta_candidate_specifications',
        ));

    } //End Candidate Specifications


    // Social Icons
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_social_icons', // Set a unique slug-like ID
        'title' => esc_html__( 'Social Icons', 'jobly' ),
        'icon' => 'fa fa-hashtag',
        'fields' => array(

            array(
                'id'                => 'social_icons',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Social Icons', 'jobly' ),
                'subtitle'              => esc_html__( 'Customize and manage your social media icons along with respective URLs', 'jobly' ),
                'button_title'      => esc_html__( 'Add Icon', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon', 'jobly' ),
                        'default'       => 'bi bi-facebook',
                    ),

                    array(
                        'id'            => 'url',
                        'type'          => 'text',
                        'title'         => esc_html__( 'URL', 'jobly' ),
                        'default'       => '#',
                    ),

                ),
                'default' => array(
                    array(
                        'icon' => 'bi bi-facebook',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-instagram',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-twitter',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-linkedin',
                        'url' => '#',
                    ),
                ),
            )

        )
    ) ); //End Social Icons


    // Contact Information
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_contact_info', // Set a unique slug-like ID
        'title' => esc_html__( 'Contact Information', 'jobly' ),
        'icon' => 'fa fa-map',
        'fields' => array(

            array(
                'id'         => 'candidate_mail',
                'type'       => 'text',
                'title'      => esc_html__( 'Candidate Mail', 'jobly' ),
                'subtitle'   => esc_html__( 'Input the Candidate Mail Address', 'jobly' ),
                'default'    => 'demo.candidate@mail.com',
            ),

            array(
                'id'          => 'candidate_location',
                'type'        => 'map',
                'title'       => esc_html__('Location', 'jobly'),
                'height'   => '500px',
                'settings' => array(
                    'scrollWheelZoom' => true,
                ),
                'default'     => array(
                    'address'   => 'Dhaka Division, Bangladesh',
                    'latitude'  => '23.9456166',
                    'longitude' => '90.2526382',
                    'zoom'      => '20',
                ),

            ),

        )
    ) ); //End Contact Information


    // Intro Video
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_video', // Set a unique slug-like ID
        'title' => esc_html__( 'Intro Video', 'jobly' ),
        'icon' => 'fa fa-play',
        'fields' => array(

            array(
                'id'                => 'bg_img',
                'type'              => 'media',
                'title'             => esc_html__( 'Background Image', 'jobly' ),
            ),

            array(
                'id'                => 'video_url',
                'type'              => 'text',
                'title'             => esc_html__( 'Video URL', 'jobly' ),
                'subtitle'          => esc_html__( 'Input the candidate video introduction', 'jobly' ),
            ),

        )
    ) ); //End Intro Video


    // Education History
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_education', // Set a unique slug-like ID
        'title' => esc_html__( 'Education', 'jobly' ),
        'icon' => 'fa fa-graduation-cap',
        'fields' => array(

            array(
                'id'                => 'education',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Education', 'jobly' ),
                'subtitle'              => esc_html__( 'Customize and manage your Academic history', 'jobly' ),
                'button_title'      => esc_html__( 'Add Item', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'sl_num',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Serial Number', 'jobly' ),
                        'default'       => esc_html__('1', 'jobly'),
                    ),

                    array(
                        'id'            => 'title',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Title', 'jobly' ),
                        'default'       => esc_html__('Bachelor Degree of Design', 'jobly'),
                    ),

                    array(
                        'id'            => 'academy',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Academy', 'jobly' ),
                        'default'       => esc_html__('University of Boston', 'jobly'),
                    ),

                    /*array(
                        'id'            => 'year',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Year', 'jobly' ),
                        'default'       => esc_html__('2012-16', 'jobly'),
                    ),*/

                    array(
                        'id'            => 'description',
                        'type'          => 'wp_editor',
                        'title'         => esc_html__( 'Description', 'jobly' ),
                        'default'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.', 'jobly' ),
                    ),

                ),
                'default' => array(
                    array(
                        'sl_num' => esc_html__('1', 'jobly'),
                        'title' => esc_html__('Bachelor Degree of Design', 'jobly'),
                        'academy' => esc_html__('University of Boston', 'jobly'),
                        'year' => esc_html__('2012-16', 'jobly'),
                        'description' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.', 'jobly' ),
                    ),
                ),
            )

        )
    ) ); //End Education History


    // Work Experience
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_experience', // Set a unique slug-like ID
        'title' => esc_html__( 'Experience', 'jobly' ),
        'icon' => 'fa fa-toolbox',
        'fields' => array(

            array(
                'id'                => 'experience',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Work Experience', 'jobly' ),
                'subtitle'              => esc_html__( 'Customize and manage your work experience', 'jobly' ),
                'button_title'      => esc_html__( 'Add Item', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'sl_num',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Serial Number', 'jobly' ),
                        'default'       => esc_html__('1', 'jobly'),
                    ),

                    array(
                        'id'            => 'title',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Title', 'jobly' ),
                        'default'       => esc_html__('Product Designer (Google)', 'jobly'),
                    ),

                    array(
                        'id'            => 'start_date',
                        'type'          => 'date',
                        'title'         => esc_html__( 'Start Date', 'jobly' ),
                        'default'       => esc_html__('02/03/18 - 13/05/20', 'jobly'),
                        'settings' => array(
                            'dateFormat'      => 'dd/mm/yy',
                            'changeMonth'     => true,
                            'changeYear'      => true,
                            'showButtonPanel' => true,
                            'monthNamesShort' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
                            'dayNamesMin'     => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                        )
                    ),

                    array(
                        'id'            => 'end_date',
                        'type'          => 'date',
                        'title'         => esc_html__( 'End Date', 'jobly' ),
                        'settings'      => array(
                            'dateFormat'      => 'dd/mm/yy',
                            'changeMonth'     => true,
                            'changeYear'      => true,
                            'showButtonPanel' => true,
                            'monthNamesShort' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
                            'dayNamesMin'     => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                        )
                    ),

                    array(
                        'id'            => 'description',
                        'type'          => 'wp_editor',
                        'title'         => esc_html__( 'Description', 'jobly' ),
                        'default'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.', 'jobly' ),
                    ),

                ),
                'default' => array(
                    array(
                        'sl_num' => esc_html__('1', 'jobly'),
                        'title' => esc_html__('Product Designer (Google)', 'jobly'),
                        'year' => esc_html__('2012-16', 'jobly'),
                        'description' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.', 'jobly' ),
                    ),
                ),
            )

        )
    ) ); //End Experience


    // Portfolio
    CSF::createSection( $meta_candidate_prefix, array(
        'id'    => 'jobly_meta_portfolio', // Set a unique slug-like ID
        'title' => esc_html__( 'Portfolio', 'jobly' ),
        'icon' => 'fa fa-briefcase',
        'fields' => array(

            array(
                'id'                => 'portfolio',
                'type'              => 'gallery',
                'title'             => esc_html__( 'Portfolio', 'jobly' ),
            )

        )
    ) ); //End Portfolio

}