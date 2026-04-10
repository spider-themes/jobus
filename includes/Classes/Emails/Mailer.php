<?php
namespace jobus\includes\Classes\Emails;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mailer engine for Jobus.
 * 
 * Handles sending emails with a branded HTML template wrapper.
 */
class Mailer {

	/**
	 * Send a branded email using Jobus settings.
	 *
	 * @param string|array $to          Array or comma-separated list of email addresses to send message.
	 * @param string       $subject     Email subject.
	 * @param string       $message     Message contents.
	 * @param string|array $headers     Optional. Additional headers.
	 * @param string|array $attachments Optional. Files to attach.
	 *
	 * @return bool Whether the email contents were sent successfully.
	 */
	public static function send( $to, $subject, $message, $headers = '', $attachments = [] ) {
		// Retrieve Jobus opt settings.
		$options = get_option( 'jobus_opt', [] );
		
		// Setup from header
		$from_name  = ! empty( $options['email_from_name'] ) ? $options['email_from_name'] : get_bloginfo( 'name' );
		$from_email = ! empty( $options['email_from_address'] ) ? $options['email_from_address'] : get_option( 'admin_email' );
		
		// Unify headers
		if ( ! is_array( $headers ) ) {
			$headers = array_filter( explode( "\n", str_replace( "\r\n", "\n", $headers ) ) );
		}
		
		// Add HTML content type and From header
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = sprintf( 'From: %s <%s>', $from_name, $from_email );

		// Load global brand settings
		$logo_info     = $options['email_logo'] ?? [];
		$logo_url      = ! empty( $logo_info['url'] ) ? $logo_info['url'] : '';
		$primary_color = ! empty( $options['email_primary_color'] ) ? $options['email_primary_color'] : '#007bff';
		$footer_text   = ! empty( $options['email_footer_text'] ) ? wpautop( $options['email_footer_text'] ) : '';

		// Format message layout: auto paragraph pure text strings
		if ( wp_strip_all_tags( $message ) === $message ) {
			$message = wpautop( $message );
		}

		ob_start();
		// Locate template wrapper
		$template_path = locate_template( 'jobus/emails/email-wrapper.php' );
		if ( ! $template_path ) {
			$template_path = JOBUS_PATH . '/templates/emails/email-wrapper.php';
		}

		if ( file_exists( $template_path ) ) {
			include $template_path;
			$html_body = ob_get_clean();
		} else {
			ob_end_clean();
			$html_body = $message;
		}

		return wp_mail( $to, $subject, $html_body, $headers, $attachments );
	}
}
