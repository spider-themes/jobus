<?php
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles geocoding and radius searching via a dedicated index table.
 */
class Geolocation {

	/**
	 * Init
	 */
	public function __construct() {
		add_action( 'save_post_jobus_job', [ $this, 'sync_job_location' ], 20, 2 );
		add_filter( 'jobus_job_query_args', [ $this, 'apply_radius_filter' ], 10, 2 );
	}

	/**
	 * Geocode taxonomy term name or custom location into lat/lng.
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 */
	public function sync_job_location( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$coords = false;

		// 1. Check if the taxonomy term has explicit Lat/Lng override configured via CSF
		$terms = wp_get_post_terms( $post_id, 'jobus_job_location' );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$term_id = $terms[0]->term_id; // Use primary location term
			$term_meta = get_term_meta( $term_id, 'jobus_taxonomy_location', true );
			
			if ( isset( $term_meta['location_map']['latitude'] ) && isset( $term_meta['location_map']['longitude'] ) ) {
				$lat = (float) $term_meta['location_map']['latitude'];
				$lng = (float) $term_meta['location_map']['longitude'];
				// Ignore if map was saved without ever being touched (default 20, 0)
				if ( ! ( $lat == 20 && $lng == 0 ) ) {
					$coords = [
						'lat' => $lat,
						'lng' => $lng,
					];
				}
			}
		}

		// 2. Fallback to API geocoding of taxonomy strings if manual overrides don't exist
		if ( ! $coords ) {
			$address = '';
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$names = wp_list_pluck( $terms, 'name' );
				$address = implode( ', ', $names );
			}

			if ( empty( $address ) ) {
				return;
			}

			$coords = $this->geocode_address( $address );
		}

		// 3. Save resolved coordinates into Custom Index
		if ( $coords ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'jobus_search_index';

			$wpdb->replace(
				$table_name,
				[
					'post_id' => $post_id,
					'lat'     => $coords['lat'],
					'lng'     => $coords['lng'],
				],
				[ '%d', '%f', '%f' ]
			);
		}
	}

	/**
	 * Geocode an address using the configured provider.
	 *
	 * @param string $address
	 * @return array|false Returns ['lat' => float, 'lng' => float] or false on failure.
	 */
	public function geocode_address( $address ) {
		// 1. Check if the address matches a jobus_job_location term exactly to use precise meta overrides
		$term = get_term_by( 'name', $address, 'jobus_job_location' );
		if ( $term ) {
			$term_meta = get_term_meta( $term->term_id, 'jobus_taxonomy_location', true );
			if ( isset( $term_meta['location_map']['latitude'] ) && isset( $term_meta['location_map']['longitude'] ) ) {
				$lat = (float) $term_meta['location_map']['latitude'];
				$lng = (float) $term_meta['location_map']['longitude'];
				if ( ! ( $lat == 20 && $lng == 0 ) ) {
					return [
						'lat' => $lat,
						'lng' => $lng,
					];
				}
			}
		}

		$options  = get_option( 'jobus_opt', [] );
		$provider = $options['geolocation_provider'] ?? 'nominatim';

		// Return cached value if exists to save API cost
		$cache_key = 'jobus_geocode_' . md5( $address . $provider );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$coords = false;

		if ( 'google' === $provider ) {
			$api_key = $options['google_maps_api_key'] ?? '';
			if ( $api_key ) {
				$url  = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&key=' . $api_key;
				$res  = wp_remote_get( $url );
				$body = wp_remote_retrieve_body( $res );
				$data = json_decode( $body, true );
				if ( 'OK' === ( $data['status'] ?? '' ) && ! empty( $data['results'][0]['geometry']['location'] ) ) {
					$coords = [
						'lat' => $data['results'][0]['geometry']['location']['lat'],
						'lng' => $data['results'][0]['geometry']['location']['lng'],
					];
				}
			}
		} elseif ( 'mapbox' === $provider ) {
			$api_key = $options['mapbox_api_key'] ?? '';
			if ( $api_key ) {
				$url  = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode( $address ) . '.json?access_token=' . $api_key;
				$res  = wp_remote_get( $url );
				$body = wp_remote_retrieve_body( $res );
				$data = json_decode( $body, true );
				if ( ! empty( $data['features'][0]['center'] ) ) {
					$coords = [
						// Mapbox returns [longitude, latitude]
						'lng' => $data['features'][0]['center'][0],
						'lat' => $data['features'][0]['center'][1],
					];
				}
			}
		} else {
			// Nominatim (Free openstreetmap)
			$url  = 'https://nominatim.openstreetmap.org/search?q=' . urlencode( $address ) . '&format=json&limit=1';
			$res  = wp_remote_get( $url, [
				'headers' => [
					'User-Agent' => 'Jobus WordPress Plugin (spider-themes)',
				]
			] );
			$body = wp_remote_retrieve_body( $res );
			$data = json_decode( $body, true );
			if ( ! empty( $data[0]['lat'] ) && ! empty( $data[0]['lon'] ) ) {
				$coords = [
					'lat' => (float) $data[0]['lat'],
					'lng' => (float) $data[0]['lon'],
				];
			}
		}

		if ( $coords ) {
			set_transient( $cache_key, $coords, WEEK_IN_SECONDS * 4 );
		}

		return $coords;
	}

	/**
	 * Append radius filtering to job archive queries.
	 *
	 * @param array $args
	 * @param array $params Contains $_GET payload typically mapped to query args
	 * @return array
	 */
	public function apply_radius_filter( $args, $params = [] ) {
		$options = get_option( 'jobus_opt', [] );
		if ( isset( $options['enable_radius_search'] ) && empty( $options['enable_radius_search'] ) ) {
			return $args;
		}

		// Use native $_GET or fallback to custom params matching `radius_location` and `radius_distance`
		$location = isset( $_GET['radius_location'] ) ? sanitize_text_field( $_GET['radius_location'] ) : ( $params['radius_location'] ?? '' );
		$distance_raw = isset( $_GET['radius_distance'] ) ? $_GET['radius_distance'] : ( $params['radius_distance'] ?? '' );
		$lat_input = isset( $_GET['radius_lat'] ) ? (float) $_GET['radius_lat'] : ( $params['radius_lat'] ?? 0 );
		$lng_input = isset( $_GET['radius_lng'] ) ? (float) $_GET['radius_lng'] : ( $params['radius_lng'] ?? 0 );

		if ( empty( $location ) && ! $lat_input ) {
			// They did not provide a location point. Even if they selected a distance, 
			// it makes no geographical sense to restrict it. Gracefully bypass radius.
			return $args;
		}

		$distance = absint( $distance_raw ); // '' or 0 corresponds to Exact Location Only


		if ( $lat_input && $lng_input ) {
			$center = [ 'lat' => $lat_input, 'lng' => $lng_input ];
		} else {
			$center = $this->geocode_address( $location );
		}

		if ( ! $center ) {
			$args['post__in'] = [ 0 ];
			return $args;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'jobus_search_index';

		$options = get_option( 'jobus_opt', [] );
		$unit    = $options['radius_unit'] ?? 'mi';

		// Earth's radius
		$earth_radius = ( 'km' === $unit ) ? 6371 : 3959;

		$lat = (float) $center['lat'];
		$lng = (float) $center['lng'];

		$distance_float = max( 0.001, (float) $distance );

		$sql = $wpdb->prepare(
			"SELECT post_id, 
			( %d * acos( cos( radians(%f) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM $table_name 
			HAVING distance <= %f 
			ORDER BY distance ASC",
			$earth_radius,
			$lat,
			$lng,
			$lat,
			$distance_float
		);

		$results = $wpdb->get_col( $sql );

		if ( empty( $results ) ) {
			// Force query to return nothing
			$args['post__in'] = [ 0 ];
		} else {
			// Intersect with existing post__in if any
			if ( ! empty( $args['post__in'] ) ) {
				$args['post__in'] = array_intersect( $args['post__in'], $results );
				if ( empty( $args['post__in'] ) ) {
					$args['post__in'] = [ 0 ];
				}
			} else {
				$args['post__in'] = $results;
			}
			// Maintain ordering by distance if no specific order is set
			if ( empty( $args['orderby'] ) || 'date' === $args['orderby'] ) {
				$args['orderby'] = 'post__in';
			}
		}

		return $args;
	}
}
