<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Jobs JSON-LD Structured Data for single job pages.
 */
class Job_Schema {

	private $settings = [];

	/**
	 * Constructor.
	 *
	 * Initializes schema settings and hooks into WordPress core actions.
	 */
	public function __construct() {
		$options        = get_option( 'jobus_opt', [] );
		$this->settings = [
			'require_description' => ! empty( $options['schema_require_description'] ),
			'require_location'    => ! empty( $options['schema_require_location'] ),
			'require_salary'      => ! empty( $options['schema_require_salary'] ),
			'require_company'     => ! empty( $options['schema_require_company'] ),
			'require_expiry'      => ! empty( $options['schema_require_expiry'] ),
			'fallback_expiry'     => absint( $options['schema_fallback_expiry_days'] ?? 60 ),
			'hide_expired'        => ! isset( $options['schema_hide_expired'] ) || $options['schema_hide_expired'],
		];

		add_action( 'wp_head', [ $this, 'output_schema' ] );
		add_action( 'save_post_jobus_job', [ $this, 'invalidate_cache' ] );
		add_action( 'save_post_jobus_company', [ $this, 'invalidate_cache_company' ] );
	}

	/**
	 * Map currency symbols found in salary strings to ISO codes.
	 *
	 * @return array symbol => ISO code.
	 */
	private static function get_symbol_map() {
		return [
			'$'  => 'USD',
			'€'  => 'EUR',
			'£'  => 'GBP',
			'₹'  => 'INR',
			'¥'  => 'JPY',
			'₩'  => 'KRW',
			'₱'  => 'PHP',
			'₫'  => 'VND',
			'฿'  => 'THB',
			'₺'  => 'TRY',
			'₦'  => 'NGN',
			'₵'  => 'GHS',
			'৳'  => 'BDT',
			'₨'  => 'PKR',
			'R$' => 'BRL',
			'Rp' => 'IDR',
			'RM' => 'MYR',
		];
	}

	/**
	 * Output JSON-LD schema for single job postings.
	 *
	 * Hooked into wp_head. Handles guard checks, transient caching,
	 * and printing the final JSON-LD payload.
	 *
	 * @return void
	 */
	public function output_schema(): void {
		if ( ! is_singular( 'jobus_job' ) ) {
			return;
		}

		$post_id = get_the_ID();
		$job     = get_post( $post_id );

		if ( ! $job || 'publish' !== $job->post_status ) {
			return;
		}

		$cache_key = 'jobus_schema_' . $post_id;
		$schema    = get_transient( $cache_key );

		if ( false === $schema ) {
			$schema = $this->build_schema( $post_id, $job );

			if ( null === $schema ) {
				return;
			}

			set_transient( $cache_key, $schema, DAY_IN_SECONDS );
		}

		$schema = apply_filters( 'jobus_job_schema', $schema, $post_id );

		if ( empty( $schema ) ) {
			return;
		}

		echo "\n" . '<script type="application/ld+json">' . "\n";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		echo "\n" . '</script>' . "\n";
	}

	/**
	 * Build the complete schema array for a job post.
	 *
	 * Processes job details, requirements, location, salary, and company
	 * into a structured Schema.org JobPosting format.
	 *
	 * @param int      $post_id The job post ID.
	 * @param \WP_Post $job     The job post object.
	 * @return array|null Schema array or null if requirements not met.
	 */
	private function build_schema( $post_id, $job ) {
		$title       = get_the_title( $post_id );
		
		// Strip Gutenberg block comments (<!-- wp:paragraph --> etc.) before formatting
		$clean_content = preg_replace( '/<!--(.*?)-->/is', '', $job->post_content );
		$description   = wp_kses_post( wpautop( trim( $clean_content ) ) );
		
		$date_posted = get_the_date( 'c', $post_id );

		if ( $this->settings['require_description'] && empty( trim( wp_strip_all_tags( $description ) ) ) ) {
			return null;
		}

		$meta_options = get_post_meta( $post_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta_options ) ) {
			$meta_options = [];
		}

		$valid_through = $this->resolve_expiry( $meta_options, $job );

		if ( $this->settings['require_expiry'] && empty( $valid_through ) ) {
			return null;
		}

		if ( $this->settings['hide_expired'] && ! empty( $valid_through ) && strtotime( $valid_through ) < time() ) {
			return null;
		}

		$employment_type = $this->get_employment_type( $meta_options );
		$organization    = $this->get_organization_data( $meta_options );

		if ( $this->settings['require_company'] && empty( $organization['name'] ) ) {
			return null;
		}

		$location = $this->get_location_data( $post_id );

		if ( $this->settings['require_location'] && empty( $location['city'] ) ) {
			return null;
		}

		$salary = $this->get_salary_data( $meta_options );

		if ( $this->settings['require_salary'] && empty( $salary ) ) {
			return null;
		}

		$apply_method = $meta_options['is_apply_btn'] ?? 'default';

		$schema = [
			'@context'       => 'https://schema.org/',
			'@type'          => 'JobPosting',
			'title'          => $title,
			'description'    => $description,
			'datePosted'     => $date_posted,
			'employmentType' => $employment_type,
			'directApply'    => ( 'default' === $apply_method ),
			'identifier'     => [
				'@type' => 'PropertyValue',
				'name'  => get_bloginfo( 'name' ),
				'value' => (string) $post_id,
			],
		];

		if ( ! empty( $valid_through ) ) {
			$schema['validThrough'] = $valid_through;
		}

		// Hiring organization
		$hiring_org = [
			'@type' => 'Organization',
			'name'  => $organization['name'],
		];

		if ( ! empty( $organization['url'] ) ) {
			$hiring_org['sameAs'] = $organization['url'];
		}

		if ( ! empty( $organization['logo'] ) ) {
			$hiring_org['logo'] = $organization['logo'];
		}

		$schema['hiringOrganization'] = $hiring_org;

		// Job location
		$has_location = ! empty( $location['city'] );

		if ( $has_location ) {
			$address = [ '@type' => 'PostalAddress' ];

			if ( ! empty( $location['city'] ) ) {
				$address['addressLocality'] = $location['city'];
			}
			if ( ! empty( $location['region'] ) ) {
				$address['addressRegion'] = $location['region'];
			}
			if ( ! empty( $location['country'] ) ) {
				$address['addressCountry'] = $location['country'];
			}

			$schema['jobLocation'] = [
				'@type'   => 'Place',
				'address' => $address,
			];
		}

		// Remote jobs
		if ( $this->is_remote_job( $meta_options ) ) {
			$schema['jobLocationType'] = 'TELECOMMUTE';

			if ( ! $has_location ) {
				unset( $schema['jobLocation'] );
			}
		}

		// Salary
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

		return $schema;
	}

	/**
	 * Resolve the job expiration date.
	 *
	 * Attempts to find a hard expiration date, or calculates a fallback
	 * based on admin settings.
	 *
	 * @param array    $meta_options The post meta options array.
	 * @param \WP_Post $job          The job post object.
	 * @return string The valid expiration date in ISO 8601 format or an empty string.
	 */
	private function resolve_expiry( $meta_options, $job ) {
		$expiry_date = $meta_options['_jobus_expiration_date'] ?? '';

		if ( ! empty( $expiry_date ) ) {
			return wp_date( 'c', strtotime( $expiry_date ) );
		}

		if ( $this->settings['fallback_expiry'] > 0 ) {
			return wp_date( 'c', strtotime( '+' . $this->settings['fallback_expiry'] . ' days', strtotime( $job->post_date ) ) );
		}

		return '';
	}

	/**
	 * Check if the job is remote.
	 *
	 * Scans job type options for any indication of remote work.
	 *
	 * @param array $meta_options The post meta options array.
	 * @return bool True if remote, false otherwise.
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
	 * Translates plugin taxonomy terms to official schema property values.
	 *
	 * @param array $meta_options The post meta options array.
	 * @return array|string Employment type string or array of strings.
	 */
	private function get_employment_type( $meta_options ) {
		$job_types = $meta_options['job-type'] ?? [];
		if ( ! is_array( $job_types ) ) {
			$job_types = [ $job_types ];
		}

		$schema_types = [];
		$mapping      = [
			'full-time'     => 'FULL_TIME',
			'part-time'     => 'PART_TIME',
			'freelance'     => 'CONTRACTOR',
			'project-basis' => 'CONTRACTOR',
			'internship'    => 'INTERN',
			'temporary'     => 'TEMPORARY',
			'volunteer'     => 'VOLUNTEER',
			'remote'        => 'FULL_TIME',
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
	 * Retrieves linked company profile data including URL and logo.
	 * Falls back to site defaults if applicable.
	 *
	 * @param array $meta_options The post meta options array.
	 * @return array Associative array containing company name, url, and logo.
	 */
	private function get_organization_data( $meta_options ) {
		$company_id   = absint( $meta_options['select_company'] ?? 0 );
		$company_name = get_bloginfo( 'name' );
		$company_url  = home_url();
		$company_logo = '';

		$default_logo = jobus_opt( 'default_company_logo' );
		if ( ! empty( $default_logo['url'] ) ) {
			$company_logo = $default_logo['url'];
		}

		if ( $company_id ) {
			$company = get_post( $company_id );

			if ( $company && 'publish' === $company->post_status ) {
				$company_name = $company->post_title;
				$company_url  = get_permalink( $company_id );

				$company_meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
				if ( ! empty( $company_meta['company_website']['url'] ) && '#' !== $company_meta['company_website']['url'] ) {
					$company_url = $company_meta['company_website']['url'];
				}

				$thumbnail_id = get_post_thumbnail_id( $company_id );
				if ( $thumbnail_id ) {
					$img_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );
					if ( $img_src ) {
						$company_logo = $img_src[0];
					}
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
	 * Get Location details from taxonomy.
	 *
	 * Walks the parent hierarchy (child = city, parent = region, grandparent = country).
	 *
	 * @param int $post_id The job post ID.
	 * @return array Associative array containing city, region, and country.
	 */
	private function get_location_data( $post_id ) {
		$terms = get_the_terms( $post_id, 'jobus_job_location' );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [
				'city'    => '',
				'region'  => '',
				'country' => '',
			];
		}

		$deepest = $terms[0];
		$region  = '';
		$country = '';

		if ( $deepest->parent ) {
			$parent = get_term( $deepest->parent, 'jobus_job_location' );
			if ( $parent && ! is_wp_error( $parent ) ) {
				$region = $parent->name;
				if ( $parent->parent ) {
					$grandparent = get_term( $parent->parent, 'jobus_job_location' );
					if ( $grandparent && ! is_wp_error( $grandparent ) ) {
						$country = $grandparent->name;
					}
				}
			}
		}

		return [
			'city'    => $deepest->name,
			'region'  => $region,
			'country' => $country,
		];
	}

	/**
	 * Extract salary range and currency from meta options.
	 *
	 * Dynamic currency detection:
	 * 1. Auto-detect symbol in salary string
	 * 2. Fallback to jobus_get_currency() (WC -> setting -> USD)
	 *
	 * @param array $meta_options The post meta options array.
	 * @return array|null Associative array with salary properties, or null if invalid.
	 */
	private function get_salary_data( $meta_options ) {
		$salary_str = $meta_options['salary'] ?? '';
		if ( is_array( $salary_str ) ) {
			$salary_str = reset( $salary_str );
		}

		if ( empty( $salary_str ) ) {
			return null;
		}

		$salary_str = str_replace( '@space@', ' ', $salary_str );

		$data = [
			'currency' => function_exists( 'jobus_get_currency' ) ? jobus_get_currency() : 'USD',
			'minValue' => 0,
			'maxValue' => 0,
			'unitText' => 'YEAR',
		];

		// Auto-detect currency symbol in the string (multi-char symbols first)
		$symbol_map = self::get_symbol_map();
		uasort( $symbol_map, function ( $a, $b ) {
			return 0; // preserve order — multi-char symbols like R$ are listed first
		} );

		// Check multi-char symbols first (R$, Rp, RM), then single-char
		$sorted_symbols = $symbol_map;
		uksort( $sorted_symbols, function ( $a, $b ) {
			return mb_strlen( $b ) - mb_strlen( $a );
		} );

		foreach ( $sorted_symbols as $symbol => $code ) {
			if ( false !== mb_strpos( $salary_str, $symbol ) ) {
				$data['currency'] = $code;
				break;
			}
		}

		// Detect pay period
		if ( stripos( $salary_str, 'hour' ) !== false ) {
			$data['unitText'] = 'HOUR';
		} elseif ( stripos( $salary_str, 'week' ) !== false ) {
			$data['unitText'] = 'WEEK';
		} elseif ( stripos( $salary_str, 'month' ) !== false ) {
			$data['unitText'] = 'MONTH';
		}

		// Extract numeric values (supports: 22k, 30K, 60,000, 50000)
		preg_match_all( '/(\d[\d,]*)\s*([kK])?/', $salary_str, $matches );

		if ( ! empty( $matches[1] ) ) {
			$values = [];
			foreach ( $matches[1] as $index => $val ) {
				$val = (int) str_replace( ',', '', $val );
				if ( ! empty( $matches[2][ $index ] ) ) {
					$val *= 1000;
				}
				$values[] = $val;
			}

			if ( count( $values ) >= 2 ) {
				$data['minValue'] = min( $values );
				$data['maxValue'] = max( $values );
			} elseif ( 1 === count( $values ) ) {
				$data['minValue'] = $values[0];
				$data['maxValue'] = $values[0];
			}
		}

		if ( 0 === $data['minValue'] ) {
			return null;
		}

		return $data;
	}

	/**
	 * Invalidate the schema transient cache for a single job post.
	 *
	 * @param int $post_id The job post ID being saved.
	 * @return void
	 */
	public function invalidate_cache( $post_id ): void {
		delete_transient( 'jobus_schema_' . absint( $post_id ) );
	}

	/**
	 * Invalidate schema cache for all jobs linked to a saved company.
	 *
	 * When a company updates, all associated jobs' schemas should be refreshed.
	 *
	 * @param int $post_id The company post ID being saved.
	 * @return void
	 */
	public function invalidate_cache_company( $post_id ): void {
		$company_id = absint( $post_id );

		$jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				[
					'key'     => 'jobus_meta_options',
					'value'   => (string) $company_id,
					'compare' => 'LIKE',
				],
			],
			'fields' => 'ids',
		] );

		foreach ( $jobs as $job_id ) {
			delete_transient( 'jobus_schema_' . absint( $job_id ) );
		}
	}
}
