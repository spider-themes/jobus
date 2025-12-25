<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Dashboard Settings - Parent Section
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_dashboard',
	'title' => esc_html__( 'User Dashboard', 'jobus' ),
	'icon'  => 'fa fa-tachometer',
) );

// Dashboard General Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_dashboard',
	'title'  => esc_html__( 'General Settings', 'jobus' ),
	'id'     => 'dashboard_general',
	'fields' => array(

		array(
			'id'      => 'dashboard_intro',
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the appearance of the front-end dashboard where employers and candidates manage their profiles, jobs, and applications.', 'jobus' ),
		),

		// Logo Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Branding', 'jobus' ),
		),

		array(
			'id'       => 'dashboard_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Dashboard Logo', 'jobus' ),
			'subtitle' => esc_html__( 'Upload a custom logo to display in the user dashboard sidebar.', 'jobus' ),
			'desc'     => esc_html__( 'Recommended size: 180x50 pixels. PNG format with transparent background works best.', 'jobus' ),
			'default'  => array(
				'url' => JOBUS_IMG . '/dashboard/logo/logo.png',
			),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Dashboard Page Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Dashboard Page', 'jobus' ),
		),

		array(
			'id'       => 'dashboard_page_title',
			'type'     => 'text',
			'title'    => esc_html__( 'Dashboard Title', 'jobus' ),
			'subtitle' => esc_html__( 'The main heading displayed on the dashboard homepage.', 'jobus' ),
			'default'  => esc_html__( 'Dashboard', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Pagination Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Pagination', 'jobus' ),
		),

		array(
			'id'       => 'dashboard_items_per_page',
			'type'     => 'number',
			'title'    => esc_html__( 'Items Per Page', 'jobus' ),
			'subtitle' => esc_html__( 'Number of items to display per page in dashboard lists.', 'jobus' ),
			'desc'     => esc_html__( 'Applies to jobs, applications, saved items lists.', 'jobus' ),
			'default'  => 6,
			'min'      => 1,
			'max'      => 50,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'dashboard_widget_items',
			'type'     => 'number',
			'title'    => esc_html__( 'Widget Items Count', 'jobus' ),
			'subtitle' => esc_html__( 'Number of items shown in dashboard overview widgets.', 'jobus' ),
			'desc'     => esc_html__( 'Applies to recent jobs, saved candidates preview sections.', 'jobus' ),
			'default'  => 4,
			'min'      => 1,
			'max'      => 10,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),
	)
) );

// Candidate Dashboard Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_dashboard',
	'title'  => esc_html__( 'Candidate Dashboard', 'jobus' ),
	'id'     => 'dashboard_candidate',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the dashboard experience for job seekers and candidates.', 'jobus' ),
		),

		// Candidate Navigation Menu
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Navigation Menu', 'jobus' ),
		),

		array(
			'id'       => 'candidate_menu_dashboard',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Dashboard', 'jobus' ),
			'subtitle' => esc_html__( 'Show the main dashboard overview page.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_menu_profile',
			'type'     => 'switcher',
			'title'    => esc_html__( 'My Profile', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to edit their profile information.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_menu_resume',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Resume', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to manage their resume, education, and experience.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_menu_applied_jobs',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Applied Jobs', 'jobus' ),
			'subtitle' => esc_html__( 'Show the list of jobs the candidate has applied for.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_menu_saved_jobs',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Saved Jobs', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to save and view bookmarked jobs.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_menu_change_password',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Change Password', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to update their account password.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Candidate Profile Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Profile Settings', 'jobus' ),
		),

		array(
			'id'       => 'candidate_profile_photo_upload',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Profile Photo Upload', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to upload a profile photo.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_social_links',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Social Media Links', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to add social media profile links.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_description_editor',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Rich Text Description', 'jobus' ),
			'subtitle' => esc_html__( 'Enable the WYSIWYG editor for candidate descriptions.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_profile_specifications',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Specifications Section', 'jobus' ),
			'subtitle' => esc_html__( 'Show specifications section (age, email, custom fields) on profile page.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_profile_location',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Address & Location Section', 'jobus' ),
			'subtitle' => esc_html__( 'Show address and map location section on profile page.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Candidate Resume Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Resume Settings', 'jobus' ),
		),

		array(
			'id'       => 'candidate_cv_upload',
			'type'     => 'switcher',
			'title'    => esc_html__( 'CV/Resume Upload', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to upload their CV/resume file.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'         => 'candidate_cv_file_types',
			'type'       => 'text',
			'title'      => esc_html__( 'Allowed File Types', 'jobus' ),
			'subtitle'   => esc_html__( 'Comma-separated list of allowed file extensions.', 'jobus' ),
			'default'    => 'pdf,doc,docx',
			'dependency' => array( 'candidate_cv_upload', '==', true ),
			'class'      => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_intro_video',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Intro Video', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to add an introduction video.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_education',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Education Section', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to add their educational background.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_experience',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Experience Section', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to add their work experience.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_portfolio',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Portfolio Section', 'jobus' ),
			'subtitle' => esc_html__( 'Allow candidates to showcase their portfolio items.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Dashboard Stats Cards
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Dashboard Statistics', 'jobus' ),
		),

		array(
			'id'       => 'candidate_stat_total_visitor',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Total Visitor Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show total profile visitors count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_stat_shortlisted',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Shortlisted Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show count of times candidate was shortlisted.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_stat_views',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Profile Views Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show employer profile views count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'candidate_stat_applied_jobs',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Applied Jobs Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show total applied jobs count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),
	)
) );

// Employer Dashboard Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_dashboard',
	'title'  => esc_html__( 'Employer Dashboard', 'jobus' ),
	'id'     => 'dashboard_employer',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the dashboard experience for employers and companies.', 'jobus' ),
		),

		// Employer Navigation Menu
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Navigation Menu', 'jobus' ),
		),

		array(
			'id'       => 'employer_menu_dashboard',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Dashboard', 'jobus' ),
			'subtitle' => esc_html__( 'Show the main dashboard overview page.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_profile',
			'type'     => 'switcher',
			'title'    => esc_html__( 'My Profile', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to edit their company profile.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_jobs',
			'type'     => 'switcher',
			'title'    => esc_html__( 'My Jobs', 'jobus' ),
			'subtitle' => esc_html__( 'Show the list of jobs posted by the employer.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_submit_job',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Submit Job', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to post new job listings.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_applications',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Applications', 'jobus' ),
			'subtitle' => esc_html__( 'Show job applications received from candidates.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_saved_candidate',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Saved Candidates', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to save and view bookmarked candidates.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_menu_change_password',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Change Password', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to update their account password.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Employer Profile Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Profile Settings', 'jobus' ),
		),

		array(
			'id'       => 'employer_profile_photo_upload',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Company Logo Upload', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to upload a company logo.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_social_links',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Social Media Links', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to add company social media links.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_description_editor',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Rich Text Description', 'jobus' ),
			'subtitle' => esc_html__( 'Enable the WYSIWYG editor for company descriptions.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_testimonials',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Testimonials Section', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to add client/employee testimonials.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_intro_video',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Company Video', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to add a company introduction video.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_website_link',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Website Link', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to add their company website URL.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Job Submission Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Job Submission Settings', 'jobus' ),
		),

		array(
			'id'       => 'job_submission_status',
			'type'     => 'select',
			'title'    => esc_html__( 'Default Job Status', 'jobus' ),
			'subtitle' => esc_html__( 'Status for newly submitted jobs.', 'jobus' ),
			'desc'     => esc_html__( 'Choose whether jobs are published immediately or require admin approval.', 'jobus' ),
			'options'  => array(
				'publish' => esc_html__( 'Published (Immediate)', 'jobus' ),
				'pending' => esc_html__( 'Pending Review', 'jobus' ),
				'draft'   => esc_html__( 'Draft', 'jobus' ),
			),
			'default'  => 'publish',
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'job_description_editor',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Rich Text Job Description', 'jobus' ),
			'subtitle' => esc_html__( 'Enable the WYSIWYG editor for job descriptions.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Dashboard Stats Cards
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Dashboard Statistics', 'jobus' ),
		),

		array(
			'id'       => 'employer_stat_posted_jobs',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Posted Jobs Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show total posted jobs count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_stat_applications',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Applications Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show total applications received count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_stat_saved_candidates',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Saved Candidates Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show saved candidates count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_stat_job_views',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Job Views Card', 'jobus' ),
			'subtitle' => esc_html__( 'Show total job views count.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Application Management
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Application Management', 'jobus' ),
		),

		array(
			'id'       => 'employer_download_cv',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Download CV', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to download applicant CVs.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_view_candidate_profile',
			'type'     => 'switcher',
			'title'    => esc_html__( 'View Candidate Profile', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to view full candidate profiles.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'employer_approve_reject',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Approve/Reject Applications', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to change application status.', 'jobus' ),
			'default'  => true,
			'class'    => trim( $pro_access_class . $active_theme_class )
		),
	)
) );

// Dashboard Labels & Text
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_dashboard',
	'title'  => esc_html__( 'Labels & Text', 'jobus' ),
	'id'     => 'dashboard_labels',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Customize the labels and text displayed throughout the dashboard.', 'jobus' ),
		),

		// Candidate Menu Labels
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Candidate Menu Labels', 'jobus' ),
		),

		array(
			'id'       => 'label_candidate_dashboard',
			'type'     => 'text',
			'title'    => esc_html__( 'Dashboard Label', 'jobus' ),
			'default'  => esc_html__( 'Dashboard', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_candidate_profile',
			'type'     => 'text',
			'title'    => esc_html__( 'My Profile Label', 'jobus' ),
			'default'  => esc_html__( 'My Profile', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_candidate_resume',
			'type'     => 'text',
			'title'    => esc_html__( 'Resume Label', 'jobus' ),
			'default'  => esc_html__( 'Resume', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_candidate_applied_jobs',
			'type'     => 'text',
			'title'    => esc_html__( 'Applied Jobs Label', 'jobus' ),
			'default'  => esc_html__( 'Applied Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_candidate_saved_jobs',
			'type'     => 'text',
			'title'    => esc_html__( 'Saved Jobs Label', 'jobus' ),
			'default'  => esc_html__( 'Saved Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Employer Menu Labels
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Employer Menu Labels', 'jobus' ),
		),

		array(
			'id'       => 'label_employer_dashboard',
			'type'     => 'text',
			'title'    => esc_html__( 'Dashboard Label', 'jobus' ),
			'default'  => esc_html__( 'Dashboard', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_employer_profile',
			'type'     => 'text',
			'title'    => esc_html__( 'My Profile Label', 'jobus' ),
			'default'  => esc_html__( 'My Profile', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_employer_jobs',
			'type'     => 'text',
			'title'    => esc_html__( 'My Jobs Label', 'jobus' ),
			'default'  => esc_html__( 'My Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_employer_submit_job',
			'type'     => 'text',
			'title'    => esc_html__( 'Submit Job Label', 'jobus' ),
			'default'  => esc_html__( 'Submit Job', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_employer_applications',
			'type'     => 'text',
			'title'    => esc_html__( 'Applications Label', 'jobus' ),
			'default'  => esc_html__( 'Applications', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_employer_saved_candidate',
			'type'     => 'text',
			'title'    => esc_html__( 'Saved Candidates Label', 'jobus' ),
			'default'  => esc_html__( 'Saved Candidate', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Common Labels
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Common Labels', 'jobus' ),
		),

		array(
			'id'       => 'label_change_password',
			'type'     => 'text',
			'title'    => esc_html__( 'Change Password Label', 'jobus' ),
			'default'  => esc_html__( 'Change Password', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_logout',
			'type'     => 'text',
			'title'    => esc_html__( 'Logout Label', 'jobus' ),
			'default'  => esc_html__( 'Logout', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_view_profile',
			'type'     => 'text',
			'title'    => esc_html__( 'View Profile Label', 'jobus' ),
			'default'  => esc_html__( 'View Profile', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Button Labels
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Button Labels', 'jobus' ),
		),

		array(
			'id'       => 'label_save_changes',
			'type'     => 'text',
			'title'    => esc_html__( 'Save Changes Button', 'jobus' ),
			'default'  => esc_html__( 'Save Changes', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_update_password',
			'type'     => 'text',
			'title'    => esc_html__( 'Update Password Button', 'jobus' ),
			'default'  => esc_html__( 'Update Password', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_cancel',
			'type'     => 'text',
			'title'    => esc_html__( 'Cancel Button', 'jobus' ),
			'default'  => esc_html__( 'Cancel', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_post_job',
			'type'     => 'text',
			'title'    => esc_html__( 'Post a Job Button', 'jobus' ),
			'default'  => esc_html__( 'Post a Job', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_update_job',
			'type'     => 'text',
			'title'    => esc_html__( 'Update Job Button', 'jobus' ),
			'default'  => esc_html__( 'Update Job', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_view_all',
			'type'     => 'text',
			'title'    => esc_html__( 'View All Button', 'jobus' ),
			'default'  => esc_html__( 'View All', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'label_browse_jobs',
			'type'     => 'text',
			'title'    => esc_html__( 'Browse Jobs Button', 'jobus' ),
			'default'  => esc_html__( 'Browse Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),
	)
) );

// Empty States & Messages
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_dashboard',
	'title'  => esc_html__( 'Empty States', 'jobus' ),
	'id'     => 'dashboard_empty_states',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Customize the messages shown when dashboard sections have no content.', 'jobus' ),
		),

		// Candidate Empty States
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Candidate Empty States', 'jobus' ),
		),

		array(
			'id'       => 'empty_applied_jobs_title',
			'type'     => 'text',
			'title'    => esc_html__( 'No Applied Jobs Title', 'jobus' ),
			'default'  => esc_html__( 'No Applied Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_applied_jobs_desc',
			'type'     => 'textarea',
			'title'    => esc_html__( 'No Applied Jobs Description', 'jobus' ),
			'default'  => esc_html__( 'You haven\'t applied for any jobs yet.', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_saved_jobs_title',
			'type'     => 'text',
			'title'    => esc_html__( 'No Saved Jobs Title', 'jobus' ),
			'default'  => esc_html__( 'No Saved Jobs', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_saved_jobs_desc',
			'type'     => 'textarea',
			'title'    => esc_html__( 'No Saved Jobs Description', 'jobus' ),
			'default'  => esc_html__( 'You haven\'t saved any jobs yet.', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		// Employer Empty States
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Employer Empty States', 'jobus' ),
		),

		array(
			'id'       => 'empty_posted_jobs_title',
			'type'     => 'text',
			'title'    => esc_html__( 'No Posted Jobs Title', 'jobus' ),
			'default'  => esc_html__( 'No Jobs Posted', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_posted_jobs_desc',
			'type'     => 'textarea',
			'title'    => esc_html__( 'No Posted Jobs Description', 'jobus' ),
			'default'  => esc_html__( 'You haven\'t posted any jobs yet.', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_applications_title',
			'type'     => 'text',
			'title'    => esc_html__( 'No Applications Title', 'jobus' ),
			'default'  => esc_html__( 'No Applications', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_applications_desc',
			'type'     => 'textarea',
			'title'    => esc_html__( 'No Applications Description', 'jobus' ),
			'default'  => esc_html__( 'You haven\'t received any job applications yet.', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_saved_candidates_title',
			'type'     => 'text',
			'title'    => esc_html__( 'No Saved Candidates Title', 'jobus' ),
			'default'  => esc_html__( 'No Saved Candidates', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),

		array(
			'id'       => 'empty_saved_candidates_desc',
			'type'     => 'textarea',
			'title'    => esc_html__( 'No Saved Candidates Description', 'jobus' ),
			'default'  => esc_html__( 'You haven\'t saved any candidates yet.', 'jobus' ),
			'class'    => trim( $pro_access_class . $active_theme_class )
		),
	)
) );