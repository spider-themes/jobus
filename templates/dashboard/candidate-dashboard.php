<?php
// candidate-dashboard.php
$user_id = get_current_user_id();
?>

<div class="jobly-candidate-dashboard">
    <h2><?php _e('Candidate Dashboard', 'jobly'); ?></h2>
    <p><?php _e('Welcome, ', 'jobly'); ?><?php echo esc_html(get_user_meta($user_id, 'display_name', true)); ?></p>

    <section class="jobly-resume-section">
        <h3><?php _e('Your Resume', 'jobly'); ?></h3>
        <?php if ($resume_id = get_user_meta($user_id, 'candidate_resume_id', true)): ?>
            <p><a href="<?php echo get_permalink($resume_id); ?>"><?php _e('View Your Resume', 'jobly'); ?></a></p>
        <?php else: ?>
            <p><?php _e('No resume uploaded yet.', 'jobly'); ?></p>
            <a href="<?php echo site_url('/upload-resume/'); ?>" class="jobly-button"><?php _e('Upload Resume', 'jobly'); ?></a>
        <?php endif; ?>
    </section>

    <section class="jobly-applied-jobs-section">
        <h3><?php _e('Applied Jobs', 'jobly'); ?></h3>
        <?php
        $applied_jobs = new WP_Query([
            'post_type' => 'job_application',
            'meta_key' => 'candidate_id',
            'meta_value' => $user_id,
            'posts_per_page' => 10,
        ]);

        if ($applied_jobs->have_posts()): ?>
            <ul>
                <?php while ($applied_jobs->have_posts()): $applied_jobs->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <p><?php _e('Status: ', 'jobly'); ?><?php echo esc_html(get_post_meta(get_the_ID(), 'application_status', true)); ?></p>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        <?php else: ?>
            <p><?php _e('You haven\'t applied for any jobs yet.', 'jobly'); ?></p>
        <?php endif; ?>
    </section>

    <section class="jobly-job-alerts-section">
        <h3><?php _e('Job Alerts', 'jobly'); ?></h3>
        <?php
        $job_alerts = new WP_Query([
            'post_type' => 'job_alert',
            'meta_key' => 'candidate_id',
            'meta_value' => $user_id,
            'posts_per_page' => 5,
        ]);

        if ($job_alerts->have_posts()): ?>
            <ul>
                <?php while ($job_alerts->have_posts()): $job_alerts->the_post(); ?>
                    <li>
                        <p><?php _e('Alert: ', 'jobly'); ?><?php the_title(); ?></p>
                        <p><?php echo esc_html(get_post_meta(get_the_ID(), 'alert_frequency', true)); ?></p>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        <?php else: ?>
            <p><?php _e('You have no active job alerts.', 'jobly'); ?></p>
        <?php endif; ?>
    </section>
</div>