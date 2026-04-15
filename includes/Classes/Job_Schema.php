<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Job_Schema
 * 
 * Handles Google Jobs JSON-LD Structured Data injection for single job pages.
 */
class Job_Schema {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into wp_head to output the JSON-LD schema
		add_action( 'wp_head', [ $this, 'output_schema' ] );
	}

	/**
	 * Output JSON-LD schema for single job postings.
	 *
	 * @return void
	 */
	public function output_schema(): void {
		// Only output on single jobus_job pages
		if ( ! is_singular( 'jobus_job' ) ) {
			return;
		}

		$post_id = get_the_ID();
		$job     = get_post( $post_id );

		if ( ! $job ) {
			return;
		}

		// Gather Job Data
		$title       = get_the_title( $post_id );
		$description = apply_filters( 'the_content', $job->post_content );
		$date_posted = get_the_date( 'c', $post_id );

		// Meta Options
		$meta_options = get_post_meta( $post_id, 'jobus_meta_options', true );
		
		// Expiration Date
		$expiry_date = $meta_options['_jobus_expiration_date'] ?? '';
		if ( ! empty( $expiry_date ) ) {
			$valid_through = date( 'c', strtotime( $expiry_date ) );
		} else {
			// Optional: Set a default expiration if not provided (e.g. 60 days from posting)
			$valid_through = date( 'c', strtotime( '+60 days', strtotime( $job->post_date ) ) );
		}

		// Employment Type Mapping
		$employment_type = $this->get_employment_type( $meta_options );

		// Organization Data
		$organization = $this->get_organization_data( $meta_options );

		// Location Data
		$location = $this->get_location_data( $post_id );

		// Salary Data
		$salary = $this->get_salary_data( $meta_options );

		// Construct JSON-LD Array
		$schema = [
			'@context'            => 'https://schema.org/',
			'@type'               => 'JobPosting',
			'title'               => $title,
			'description'         => wp_kses_post( $description ),
			'datePosted'          => $date_posted,
			'validThrough'        => $valid_through,
			'employmentType'      => $employment_type,
			'hiringOrganization' => [
				'@type' => 'Organization',
				'name'  => $organization['name'],
				'sameAs' => $organization['url'],
				'logo'  => $organization['logo'],
			],
			'jobLocation'        => [
				'@type'   => 'Place',
				'address' => [
					'@type'           => 'PostalAddress',
					'addressLocality' => $location['city'] ?? '',
					'addressCountry'  => $location['country'] ?? '',
				],
			],
		];

		// Handle Remote Jobs
		if ( $this->is_remote_job( $meta_options ) ) {
			$schema['jobLocationType'] = 'TELECOMMUTE';
			// If it's fully remote, the location can be "Anywhere" or specific regions
			if ( empty( $schema['jobLocation']['address']['addressLocality'] ) ) {
				unset( $schema['jobLocation'] ); // Or keep as is
			}
		}

		// Add Salary if available
		if ( ! empty( $salary ) ) {
			$schema['baseSalary'] = [
				'@type'    => 'MonetaryAmount',
				'currency' => $salary['currency'],
				'value'    => [
					'@type'    => 'QuantitativeValue',
					'minValue' => $salary['minValue'],
					'maxValue' => $salary['maxValue'],
					'unitText' => $salary['unitText'],
				],
			];
		}

		// Output properly formatted JSON-LD
		echo "\n" . '<!-- Jobus Google Jobs SEO -->' . "\n";
		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}

	/**
	 * Check if the job is remote.
	 * 
	 * @param array $meta_options
	 * @return bool
	 */
	private function is_remote_job( $meta_options ) {
		$job_types = $meta_options['job-type'] ?? [];
		if ( ! is_array( $job_types ) ) {
			$job_types = [ $job_types ];
		}

		foreach ( $job_types as $type ) {
			if ( stripos( str_replace( '@space@', ' ', $type ), 'remote' ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Map job-type meta to Google Schema employmentType.
	 *
	 * @param array $meta_options
	 * @return array|string
	 */
	private function get_employment_type( $meta_options ) {
		// We try to find 'job-type' in meta. It's usually an array because of CSF select.
		$job_types = $meta_options['job-type'] ?? [];
		if ( ! is_array( $job_types ) ) {
			$job_types = [ $job_types ];
		}

		$schema_types = [];
		$mapping = [
			'full-time'     => 'FULL_TIME',
			'part-time'     => 'PART_TIME',
			'freelance'     => 'CONTRACTOR',
			'project-basis' => 'CONTRACTOR',
			'internship'    => 'INTERN',
			'temporary'     => 'TEMPORARY',
			'volunteer'     => 'VOLUNTEER',
			'remote'        => 'FULL_TIME', // Google handles remote via jobLocationType now, but we map to FT for type
		];

		foreach ( $job_types as $type ) {
			$type_slug = sanitize_title( str_replace( '@space@', '-', $type ) );
			if ( isset( $mapping[ $type_slug ] ) ) {
				$schema_types[] = $mapping[ $type_slug ];
			}
		}

		return ! empty( $schema_types ) ? $schema_types : 'FULL_TIME';
	}

	/**
	 * Get Organization/Company details.
	 *
	 * @param array $meta_options
	 * @return array
	 */
	private function get_organization_data( $meta_options ) {
		$company_id = $meta_options['select_company'] ?? 0;
		$company_name = get_bloginfo( 'name' );
		$company_url  = home_url();
		$company_logo = '';

		// Get default logo from plugin settings
		$default_logo = jobus_opt( 'default_company_logo' );
		if ( ! empty( $default_logo['url'] ) ) {
			$company_logo = $default_logo['url'];
		}

		if ( $company_id ) {
			$company      = get_post( $company_id );
			$company_name = $company->post_title;
			$company_url  = get_permalink( $company_id );
			
			// Try to get company website from meta
			$company_meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
			if ( ! empty( $company_meta['company_website']['url'] ) && '#' !== $company_meta['company_website']['url'] ) {
				$company_url = $company_meta['company_website']['url'];
			}

			// Get company logo (thumbnail)
			$thumbnail_id = get_post_thumbnail_id( $company_id );
			if ( $thumbnail_id ) {
				$img_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );
				if ( $img_src ) {
					$company_logo = $img_src[0];
				}
			}
		}

		return [
			'name' => $company_name,
			'url'  => $company_url,
			'logo' => $company_logo,
		];
	}

	/**
	 * Get Location details from taxonomy and potentially meta.
	 *
	 * @param int $post_id
	 * @return array
	 */
	private function get_location_data( $post_id ) {
		$terms = get_the_terms( $post_id, 'jobus_job_location' );
		$location_name = '';

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			// Get the first location
			$location_name = $terms[0]->name;
		}

		// Google Jobs prefers structured address.
		// Since we only have a name from taxonomy, we use it as addressLocality.
		return [
			'city'    => $location_name,
			'country' => '', // Could be inferred or added via meta if available
		];
	}

	/**
	 * Extract salary range from 'salary' meta.
	 *
	 * @param array $meta_options
	 * @return array|null
	 */
	private function get_salary_data( $meta_options ) {
		$salary_str = $meta_options['salary'] ?? '';
		if ( is_array( $salary_str ) ) {
			$salary_str = reset( $salary_str );
		}
		
		if ( empty( $salary_str ) ) {
			return null;
		}

		// Clean up @space@
		$salary_str = str_replace( '@space@', ' ', $salary_str );

		// Example: "$22k-$30k / year" or "$30-$50 / hour"
		// This is a bit tricky to parse reliably, but we'll try our best.
		
		$data = [
			'currency' => 'USD', // Default
			'minValue' => 0,
			'maxValue' => 0,
			'unitText' => 'YEAR',
		];

		// Detect currency
		if ( strpos( $salary_str, '$' ) !== false ) { $data['currency'] = 'USD'; }
		if ( strpos( $salary_str, '€' ) !== false ) { $data['currency'] = 'EUR'; }
		if ( strpos( $salary_str, '£' ) !== false ) { $data['currency'] = 'GBP'; }

		// Detect unit
		if ( stripos( $salary_str, 'hour' ) !== false ) { $data['unitText'] = 'HOUR'; }
		elseif ( stripos( $salary_str, 'week' ) !== false ) { $data['unitText'] = 'WEEK'; }
		elseif ( stripos( $salary_str, 'month' ) !== false ) { $data['unitText'] = 'MONTH'; }
		else { $data['unitText'] = 'YEAR'; }

		// Extract numbers
		// Match patterns like 22k, 30, 50k
		preg_match_all( '/(\d+)([kK])?/', $salary_str, $matches );

		if ( ! empty( $matches[1] ) ) {
			$values = [];
			foreach ( $matches[1] as $index => $val ) {
				$val = (int) $val;
				if ( ! empty( $matches[2][ $index ] ) ) {
					$val *= 1000;
				}
				$values[] = $val;
			}

			if ( count( $values ) >= 2 ) {
				$data['minValue'] = min( $values );
				$data['maxValue'] = max( $values );
			} elseif ( count( $values ) === 1 ) {
				$data['minValue'] = $values[0];
				$data['maxValue'] = $values[0];
			}
		}

		if ( $data['minValue'] === 0 ) {
			return null;
		}

		return $data;
	}
}
