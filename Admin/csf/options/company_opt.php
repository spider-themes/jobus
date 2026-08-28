<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Company Specifications
CSF::createSection($settings_prefix, array(
	'title' => esc_html__('Company Options', 'jobus'),
	'id' => 'jobus_company',
	'icon' => 'fa fa-building',
));

// Company Specifications
CSF::createSection($settings_prefix, array(
	'title' => esc_html__('Company Specifications', 'jobus'),
	'parent' => 'jobus_company',
	'id' => 'company_specifications',
	'fields' => array(
		array(
			'id' => 'company_specifications',
			'type' => 'group',
			'title' => esc_html__('Company Specifications', 'jobus'),
			'subtitle' => esc_html__('Manage Company Specifications', 'jobus'),
			'class' => trim($pro_access_class . $active_theme_class),
			'fields' => array(
				array(
					'id' => 'meta_name',
					'type' => 'text',
					'title' => esc_html__('Name', 'jobus'),
					'placeholder' => esc_html__('Enter a specification', 'jobus'),
					'after' => esc_html__('Insert a unique name', 'jobus'),
					'attributes' => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id' => 'meta_key',
					'type' => 'text',
					'title' => esc_html__('Key', 'jobus'),
					'placeholder' => esc_html__('Specification key', 'jobus'),
					'after' => esc_html__('Insert a unique key', 'jobus'),
					'attributes' => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id' => 'meta_values_group',
					'type' => 'repeater',
					'title' => esc_html__('Options', 'jobus'),
					'button_title' => esc_html__('Add Option', 'jobus'),
					'fields' => array(
						array(
							'id' => 'meta_values',
							'type' => 'text',
							'title' => null,
						)
					)
				),

				array(
					'id' => 'meta_icon',
					'type' => 'icon',
					'title' => esc_html__('Icon (Optional)', 'jobus'),
					'placeholder' => esc_html__('Select icon', 'jobus'),
				)
			)
		)// End company specifications
	)
));


// Company Archive Page Layout Settings
CSF::createSection($settings_prefix, array(
	'parent' => 'jobus_company',
	'title' => esc_html__('Company Archive Page', 'jobus'),
	'id' => 'company_page_layout',
	'fields' => array_values( array_filter( array(

		//Subheading field
		array(
			'type' => 'subheading',
			'content' => esc_html__('Company Page Layout', 'jobus'),
		),

		array(
			'id' => 'company_archive_layout',
			'type' => 'image_select',
			'title' => esc_html__('Choose Layout', 'jobus'),
			'subtitle' => esc_html__('Select the preferred layout for your company page across the entire website.', 'jobus'),
			'options' => array(
				'1' => esc_url(JOBUS_IMG . '/layout/company/archive-layout-1.png'),
				'2' => esc_url(JOBUS_IMG . '/layout/company/archive-layout-2.png'),
			),
			'default' => '1',
			'class' => trim($pro_access_class . $active_theme_class)
		),

		array(
			'id' => 'company_posts_per_page',
			'type' => 'number',
			'title' => esc_html__('Posts Per Page', 'jobus'),
			'default' => -1,
			'desc' => esc_html__('Set the value to \'-1\' to display all company posts.', 'jobus'),
			'class' => trim($pro_access_class . $active_theme_class),
		),

		array(
			'id' => 'company_page_padding',
			'type' => 'spacing',
			'title' => esc_html__('Page Padding', 'jobus'),
			'subtitle' => esc_html__('Control the top, right, bottom and left padding of the Company archive page.', 'jobus'),
			'output' => '.jbs-company-classic',
			'output_mode' => 'padding',
		),

		array(
			'id' => 'company_archive_grid_column',
			'type' => 'select',
			'title' => esc_html__('Select Column', 'jobus'),
			'subtitle' => esc_html__('Select the number of columns to display in the company grid layout.', 'jobus'),
			'options' => array(
				'6' => esc_html__('Two Column', 'jobus'),
				'4' => esc_html__('Three Column', 'jobus'),
				'3' => esc_html__('Four Column', 'jobus'),
				'2' => esc_html__('Six Column', 'jobus'),
			),
			'default' => '4',
			'class' => trim($pro_access_class . $active_theme_class)
		),

		// Company Attributes
		array(
			'type' => 'subheading',
			'content' => esc_html__('Company Attributes', 'jobus'),
		),

		array(
			'id' => 'company_archive_attr_layout',
			'type' => 'button_set',
			'title' => esc_html__('Content Layout', 'jobus'),
			'options' => array(
				'grid' => esc_html__('Grid', 'jobus'),
				'list' => esc_html__('List', 'jobus'),
			),
			'default' => 'grid',
			'class' => trim($pro_access_class . $active_theme_class),
		),

		array(
			'id' => 'company_archive_meta_1',
			'type' => 'select',
			'title' => esc_html__('Attribute 01', 'jobus'),
			'options' => jobus_get_specs('company_specifications'),
			'dependency' => array('company_archive_attr_layout', '==', 'list'),
			'class' => trim($pro_access_class . $active_theme_class),
		),

		// Sidebar Filters
		array(
			'type' => 'subheading',
			'content' => esc_html__('Sidebar Search & Filter Options', 'jobus'),
		),

		array(
			'type'    => 'notice',
			'style'   => 'info',
			'content' => esc_html__( 'Understanding Sidebar Filters: Enable "Sidebar Keyword Search" for keyword searches. Use "Custom Specification Widgets" to add custom attribute filters (like Team Size or Founded Year) after defining them in Company Specifications. Use "Built-in Taxonomy Widgets" to toggle default filters like Category or Location.', 'jobus' ),
		),

		// Search Form
		array(
			'id' => 'company_show_search_form',
			'type' => 'switcher',
			'title' => esc_html__('Sidebar Keyword Search', 'jobus'),
			'subtitle' => esc_html__('Display a keyword search box at the top of the archive sidebar.', 'jobus'),
			'desc' => esc_html__('Enabling this allows users to quickly search for Companies by keywords or names.', 'jobus'),
			'default' => false,
			'class' => trim($pro_access_class . $active_theme_class),
		),

		// Notice for missing Company Specifications
		(function () {
			$specifications = jobus_opt('company_specifications');
			if (empty($specifications) || !is_array($specifications)) {
				$settings_url = admin_url('admin.php?page=jobus-settings&tab=jobus_job&section=job_specifications#tab=company-options/company-specifications');
				return array(
					'type' => 'content',
					'content' => '<div style="padding: 15px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; margin-bottom: 20px;">' .
						'<p style="margin: 0 0 8px 0; font-weight: 500;">' .
						esc_html__('No Company Specifications Configured', 'jobus') .
						'</p>' .
						'<p style="margin: 0; font-size: 13px;">' .
						sprintf(
							/* translators: %s: settings page link */
							esc_html__('Please add Company Specifications from %s before creating filter widgets.', 'jobus'),
							'<a href="' . esc_url($settings_url) . '" style="color: #856404; text-decoration: underline; font-weight: 500;">Settings > Company Options > Company Specifications</a>'
						) .
						'</p>' .
						'</div>',
				);
			}
			return null;
		})(),

		// Meta Widgets
		array(
			'id' => 'company_sidebar_widgets',
			'type' => 'repeater',
			'title' => esc_html__('Custom Specification Widgets', 'jobus'),
			'subtitle' => esc_html__('Build your custom sidebar filter by adding specification widgets.', 'jobus'),
			'desc' => esc_html__('Widgets added here will appear in the Company Archive sidebar, allowing users to filter companies dynamically based on your custom Company Specifications (e.g., Company Size, Founded Year). Ensure you have created Specifications first.', 'jobus'),
			'button_title' => esc_html__('Add Widget', 'jobus'),
			'class' => trim($pro_access_class . $active_theme_class),
			'fields' => array(

				array(
					'id' => 'widget_name',
					'type' => 'select',
					'title' => esc_html__('Widget', 'jobus'),
					'options' => jobus_get_specs('company_specifications'),
					'default' => false,
				),

				array(
					'id' => 'widget_layout',
					'type' => 'button_set',
					'title' => esc_html__('Widget Layout', 'jobus'),
					'options' => array(
						'dropdown' => esc_html__('Dropdown', 'jobus'),
						'checkbox' => esc_html__('Checkbox', 'jobus'),
					),
					'default' => 'checkbox',
				),

			)
		),

		// Taxonomy Widgets
		array(
			'id' => 'company_taxonomy_widgets',
			'type' => 'sortable',
			'title' => esc_html__('Built-in Taxonomy Widgets', 'jobus'),
			'subtitle' => esc_html__('Enable and manage built-in taxonomy filters for the sidebar.', 'jobus'),
			'desc' => esc_html__('Use the toggle to enable/disable specific taxonomies (like Category, Location). Drag and drop to change their display order in the frontend sidebar filter.', 'jobus'),
			'class' => trim($pro_access_class . $active_theme_class),
			'fields' => array(

				array(
					'id' => 'is_company_widget_location',
					'type' => 'switcher',
					'title' => esc_html__('Location', 'jobus'),
					'default' => true,
				),

				array(
					'id' => 'is_company_widget_cat',
					'type' => 'switcher',
					'title' => esc_html__('Category', 'jobus'),
					'default' => true,
				),
			),
		),
	) ) ),

));

// Company Details Layout Settings
CSF::createSection($settings_prefix, array(
	'parent' => 'jobus_company',
	'title' => esc_html__('Company Details Page', 'jobus'),
	'id' => 'company_details_layout',
	'fields' => array(

		// Single Post Layout
		array(
			'type' => 'subheading',
			'content' => esc_html__('Single Post Layout', 'jobus'),
		),

		array(
			'id' => 'company_single_sec_pad',
			'type' => 'spacing',
			'title' => esc_html__('Page Section Padding', 'jobus'),
			'subtitle' => esc_html__('Sitewide default top/bottom padding for the Company details page. Can be overridden per post.', 'jobus'),
			'top' => true,
			'bottom' => true,
			'left' => false,
			'right' => false,
			'output' => '.jbs-company-details',
			'output_mode' => 'padding',
		),

		// Social Share Options
		array(
			'type' => 'subheading',
			'content' => esc_html__('Social Media Sharing', 'jobus'),
		),

		array(
			'id' => 'enable_company_social_share',
			'type' => 'switcher',
			'title' => esc_html__('Social Share Buttons', 'jobus'),
			'subtitle' => esc_html__('Show or hide social media sharing buttons on company detail pages.', 'jobus'),
			'default' => false,
			'class' => trim($pro_access_class . $active_theme_class),
		),

		// Open Job Position
		array(
			'type' => 'subheading',
			'content' => esc_html__('Open Jobs Section', 'jobus'),
		),

		array(
			'id' => 'company_open_job_meta_1',
			'type' => 'select',
			'title' => esc_html__('Attribute 01', 'jobus'),
			'options' => jobus_get_specs(),
			'class' => trim($pro_access_class . $active_theme_class),
		),

		array(
			'id' => 'company_open_job_meta_2',
			'type' => 'select',
			'title' => esc_html__('Attribute 02', 'jobus'),
			'options' => jobus_get_specs(),
			'class' => trim($pro_access_class . $active_theme_class),
		),
	)
));

