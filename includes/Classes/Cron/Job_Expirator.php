<?php

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_Expirator {
    public function __construct() {
        add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
        add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'auto_expire_jobs' ] );
    }

    public function auto_expire_jobs() {
        if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
            return;
        }

        $batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );

        $expired_jobs = get_posts( array(
            'post_type'      => 'jobus_job',
            'post_status'    => 'publish',
            'posts_per_page' => $batch_size,
            'meta_query'     => array(
                array(
                    'key'     => 'job_deadline',
                    'value'   => current_time( 'Y-m-d' ),
                    'compare' => '<',
                    'type'    => 'DATE',
                ),
                array(
                    'key'     => 'job_deadline',
                    'value'   => '',
                    'compare' => '!=',
                ),
            ),
            'fields' => 'ids',
        ) );

        if ( empty( $expired_jobs ) ) {
            return;
        }

        foreach ( $expired_jobs as $job_id ) {
            wp_update_post( array(
                'ID'          => $job_id,
                'post_status' => 'draft',
            ) );
            do_action( 'jobus_job_auto_expired', $job_id );
        }

        if ( count( $expired_jobs ) === (int) $batch_size ) {
            if ( ! wp_next_scheduled( 'jobus_auto_expire_jobs_batch_continue' ) ) {
                wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
            }
        }
    }
}
