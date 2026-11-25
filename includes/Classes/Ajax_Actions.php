<?php

/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


use jobus\includes\Classes\Ajax\Auth_Contact_Ajax;
use jobus\includes\Classes\Ajax\Job_Application_Ajax;
use jobus\includes\Classes\Ajax\Job_Management_Ajax;

/**
 * AJAX Actions Class
 *
 * Handles AJAX actions for the Jobus plugin.
 */
class Ajax_Actions {


	use Auth_Contact_Ajax;
	use Job_Application_Ajax;
	use Job_Management_Ajax;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Candidate Single Page-> Contact Form
		add_action( 'wp_ajax_jobus_candidate_send_mail_form', [ $this, 'ajax_send_contact_email' ] );
		add_action( 'wp_ajax_nopriv_jobus_candidate_send_mail_form', [ $this, 'ajax_send_contact_email' ] );

		// Job Single Page-> Job Application Form
		add_action( 'wp_ajax_jobus_job_application', [ $this, 'job_application_form' ] );
		add_action( 'wp_ajax_nopriv_jobus_job_application', [ $this, 'job_application_form' ] );

		// Remove Job Application
		add_action( 'wp_ajax_jobus_remove_job_application', [ $this, 'remove_job_application' ] );
		add_action( 'wp_ajax_nopriv_jobus_remove_job_application', [ $this, 'remove_job_application' ] );

		// Save/Unsave Jobs for Candidates and Candidates for Employers
		add_action( 'wp_ajax_jobus_saved_post', [ $this, 'saved_post' ] );
		add_action( 'wp_ajax_nopriv_jobus_saved_post', [ $this, 'saved_post' ] );

		// Delete Job
		add_action( 'wp_ajax_jobus_delete_job', [ $this, 'delete_job' ] );

		// Submit Job
		add_action( 'wp_ajax_jobus_submit_job', [ $this, 'submit_job' ] );

		// Candidate Registration
		add_action( 'wp_ajax_nopriv_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );
		add_action( 'wp_ajax_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );

		// Employer Registration
		add_action( 'wp_ajax_nopriv_jobus_register_employer', [ $this, 'ajax_register_employer' ] );
		add_action( 'wp_ajax_jobus_register_employer', [ $this, 'ajax_register_employer' ] );

		// Send Test Email diagnostic (Admin only)
		add_action( 'wp_ajax_jobus_send_test_email', [ $this, 'ajax_send_test_email' ] );

		// Update Application Status (Employer & Admin)
		add_action( 'wp_ajax_jobus_update_applicant_status', [ $this, 'ajax_update_applicant_status' ] );
	}

}
