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
	 * Cron hook that drives the background coordinate backfill.
	 */
	const BACKFILL_HOOK = 'jobus_geo_backfill_batch';

	/**
	 * Number of jobs geocoded per background batch.
	 */
	const BACKFILL_BATCH = 25;

	/**
	 * Init
	 */
	public function __construct() {
		add_action( 'save_post_jobus_job', [ $this, 'sync_job_location' ], 20, 2 );
		add_filter( 'jobus_job_query_args', [ $this, 'apply_radius_filter' ], 10, 2 );
		add_action( self::BACKFILL_HOOK, [ $this, 'run_backfill_batch' ] );
	}

	/**
	 * Queue a full coordinate backfill to run in the background.
	 *
	 * Replaces the old synchronous loop that loaded and geocoded every job inside a
	 * single admin request (an N+1 of get_post() + meta reads + remote geocode calls
	 * that timed out on large sites). Returns the number of jobs queued.
	 *
	 * @return int Total published jobs scheduled for backfill.
	 */
	public static function start_backfill(): int {
		$count_query = new \WP_Query( [
			'post_type'              => 'jobus_job',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		update_option( 'jobus_geo_backfill_offset', 0, false );

		if ( ! wp_next_scheduled( self::BACKFILL_HOOK ) ) {
			wp_schedule_single_event( time() + 5, self::BACKFILL_HOOK );
		}

		return (int) $count_query->found_posts;
	}

	/**
	 * Process one background backfill batch and reschedule until the queue drains.
	 *
	 * @return void
	 */
	public function run_backfill_batch(): void {
		$offset = (int) get_option( 'jobus_geo_backfill_offset', 0 );

		// get_posts() returns full WP_Post objects and primes the post cache for the
		// batch, so sync_job_location() needs no per-id get_post() lookup.
		$jobs = get_posts( [
			'post_type'              => 'jobus_job',
			'post_status'            => 'publish',
			'posts_per_page'         => self::BACKFILL_BATCH,
			'offset'                 => $offset,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => true,
		] );

		if ( empty( $jobs ) ) {
			delete_option( 'jobus_geo_backfill_offset' );
			update_option( 'jobus_radius_setup_completed', true, false );
			return;
		}

		// Prime the meta cache for the whole batch in one query.
		update_postmeta_cache( wp_list_pluck( $jobs, 'ID' ) );

		foreach ( $jobs as $post ) {
			$this->sync_job_location( $post->ID, $post );
		}

		update_option( 'jobus_geo_backfill_offset', $offset + count( $jobs ), false );
		wp_schedule_single_event( time() + MINUTE_IN_SECONDS, self::BACKFILL_HOOK );
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

			$result = $wpdb->replace(
				$table_name,
				[
					'post_id' => $post_id,
					'lat'     => $coords['lat'],
					'lng'     => $coords['lng'],
				],
				[ '%d', '%f', '%f' ]
			);

			// A false return means the write failed (e.g. the unique-index migration
			// hasn't run yet on a freshly-updated site, so REPLACE silently degrades
			// to INSERT and re-creates duplicate rows). Surface it instead of leaving
			// the job silently absent from / duplicated in radius search.
			if ( false === $result ) {
				$this->log_geo_error( sprintf( 'Failed to write search index for post %d: %s', $post_id, $wpdb->last_error ) );
			}
		}
	}

	/**
	 * Log a geolocation failure when debugging is enabled.
	 *
	 * Geocoding/index failures used to be swallowed entirely, so a job would
	 * silently never appear in radius search with no diagnostic trail.
	 *
	 * @param string $message
	 * @return void
	 */
	private function log_geo_error( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[Jobus Geolocation] ' . $message );
		}
	}

	/**
	 * Perform a geocoding HTTP request and return decoded JSON, or null on failure.
	 *
	 * Centralises the error handling the provider branches previously skipped:
	 * a WP_Error transport failure (DNS, timeout, no API key host) or a non-2xx
	 * response now returns null and is logged, instead of being silently fed into
	 * json_decode('') => null and treated as "no result".
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Optional wp_remote_get() args.
	 * @return array|null
	 */
	private function remote_geocode_json( string $url, array $args = [] ) {
		$args = wp_parse_args( $args, [ 'timeout' => 5 ] );
		$res  = wp_remote_get( $url, $args );

		if ( is_wp_error( $res ) ) {
			$this->log_geo_error( 'Geocode request failed: ' . $res->get_error_message() );
			return null;
		}

		$code = (int) wp_remote_retrieve_response_code( $res );
		if ( $code < 200 || $code >= 300 ) {
			$this->log_geo_error( sprintf( 'Geocode request returned HTTP %d for %s', $code, $url ) );
			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $res ), true );

		return is_array( $data ) ? $data : null;
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
				$data = $this->remote_geocode_json( $url );
				if ( null !== $data && 'OK' === ( $data['status'] ?? '' ) && ! empty( $data['results'][0]['geometry']['location'] ) ) {
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
				$data = $this->remote_geocode_json( $url );
				if ( null !== $data && ! empty( $data['features'][0]['center'] ) ) {
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
			$data = $this->remote_geocode_json( $url, [
				'headers' => [
					'User-Agent' => 'Jobus WordPress Plugin (spider-themes)',
				],
			] );
			if ( null !== $data && ! empty( $data[0]['lat'] ) && ! empty( $data[0]['lon'] ) ) {
				$coords = [
					'lat' => (float) $data[0]['lat'],
					'lng' => (float) $data[0]['lon'],
				];
			}
		}

		if ( $coords ) {
			set_transient( $cache_key, $coords, WEEK_IN_SECONDS * 4 );
		} else {
			// Negative-cache failures briefly so a bad address or a throttled/down
			// provider doesn't trigger a fresh remote call on every page load. An
			// empty array is the sentinel (get_transient can't distinguish a stored
			// `false` from "not cached"); it is still falsy for callers.
			set_transient( $cache_key, [], HOUR_IN_SECONDS );
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
		$location = isset( $_GET['radius_location'] ) ? sanitize_text_field( wp_unslash( $_GET['radius_location'] ) ) : ( $params['radius_location'] ?? '' );
		$distance_raw = isset( $_GET['radius_distance'] ) ? wp_unslash( $_GET['radius_distance'] ) : ( $params['radius_distance'] ?? '' );
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

		// Bounding-box prefilter: derive a lat/lng window from the search radius so the
		// composite (lat, lng) index can prune rows in the WHERE clause before the much
		// more expensive haversine distance is computed. Without this the query full-scans
		// the entire index on every radius search.
		$lat_delta = rad2deg( $distance_float / $earth_radius );
		$cos_lat   = cos( deg2rad( $lat ) );
		$lng_delta = abs( $cos_lat ) < 1e-9 ? 180.0 : rad2deg( $distance_float / ( $earth_radius * abs( $cos_lat ) ) );

		$min_lat = $lat - $lat_delta;
		$max_lat = $lat + $lat_delta;
		$min_lng = $lng - $lng_delta;
		$max_lng = $lng + $lng_delta;

		$sql = $wpdb->prepare(
			"SELECT post_id,
			( %d * acos( cos( radians(%f) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( lat ) ) ) ) AS distance
			FROM $table_name
			WHERE lat BETWEEN %f AND %f AND lng BETWEEN %f AND %f
			HAVING distance <= %f
			ORDER BY distance ASC",
			$earth_radius,
			$lat,
			$lng,
			$lat,
			$min_lat,
			$max_lat,
			$min_lng,
			$max_lng,
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
