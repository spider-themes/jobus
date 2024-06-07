<?php
namespace Jobly\Frontend;

/**
 * Class Shortcode
 */
class Shortcode {

    /**
     * Shortcode constructor.
     *
     * Registers shortcodes for job and company archives.
     */
    public function __construct() {

        add_shortcode( 'jobly_job_archive', [ $this, 'job_page_shortcode' ] );
        add_shortcode( 'jobly_company_archive', [ $this, 'company_page_shortcode' ] );
        add_shortcode( 'jobly_candidate_archive', [ $this, 'candidate_page_shortcode' ] );

        // Register Form Candidate & Employers
        //add_shortcode( 'jobly_register_form', [ $this, 'register_form_shortcode' ] );

    }

    /**
     * Job Page Shortcode Handler.
     *
     * Generates the HTML content for the job archive page.
     *
     * @param array  $atts     Shortcode attributes.
     * @param string $content  Shortcode content.
     * @return string          Generated HTML content.
     */
    public function job_page_shortcode( $atts, $content = '' ) {

        ob_start();
        self::job_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;
    }


    /**
     * Company Page Shortcode Handler.
     *
     * Generates the HTML content for the company archive page.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string Generated HTML content.
     */
    public function company_page_shortcode( $atts, $content = '' )
    {
        ob_start();
        self::company_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;

    }


    /**
     * Candidate Page Shortcode Handler.
     *
     * Generates the HTML content for the Candidate archive page.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string Generated HTML content.
     */
    public function candidate_page_shortcode( $atts, $content = '' )
    {
        ob_start();
        self::candidate_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;

    }

    public function register_form_shortcode( $atts, $content = '' ) {

        ob_start();

        if (is_user_logged_in()) {
            ?>
            <p class="text-center mt-10"><?php esc_html_e('You are already logged in.', 'jobly'); ?></p>
            <?php
        } else {
            ?>
            <div class="user-data-form">

                <div class="text-center">
                    <h2><?php esc_html_e('Create Account', 'jobly'); ?></h2>
                </div>

                <div class="form-wrapper m-auto">
                    <ul class="nav nav-tabs border-0 w-100 mt-30" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#fc1" role="tab"><?php esc_html_e('Candidates', 'jobly'); ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fc2" role="tab"><?php esc_html_e('Employer', 'jobly'); ?></button>
                        </li>
                    </ul>
                    <div class="tab-content mt-40">
                        <div class="tab-pane fade show active" role="tabpanel" id="fc1">

                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name="jobly-candidate-registration-form" id="jobly-candidate-registration-form" method="post">
                                <?php wp_nonce_field('register_candidate_action', 'register_candidate_nonce'); ?>
                                <input type="hidden" name="action" value="register_candidate">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label for="candidate_username"> Name* </label>
                                            <input type="text" name="candidate_username" id="candidate_username" placeholder="Enter Your Username" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label for="candidate_email">Email*</label>
                                            <input type="email" name="candidate_email" id="candidate_email" placeholder="Enter Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label for="candidate_pass">Password*</label>
                                            <input type="password" name="candidate_pass" id="candidate_pass" placeholder="Enter Your Password" class="pass_log_id" required>
                                            <span class="placeholder_icon">
                                                <span class="passVicon">
                                                    <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobly'); ?>">
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label for="candidate_confirm_pass">Confirm Password*</label>
                                            <input type="password" name="candidate_confirm_pass" id="candidate_confirm_pass" placeholder="Enter Your Confirm Password" class="pass_log_id" required>
                                            <span class="placeholder_icon">
                                                <span class="passVicon">
                                                    <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobly'); ?>">
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                            <div>
                                                <input type="checkbox" id="remember">
                                                <label for="remember">By hitting the "Register" button, you agree to the <a href="#">Terms conditions</a> & <a href="#">Privacy Policy</a></label>
                                            </div>
                                        </div> <!-- /.agreement-checkbox -->
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn-eleven fw-500 tran3s d-block mt-20">Register</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane fade" role="tabpanel" id="fc2">
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name="jobly-employer-registration-form" id="jobly-employer-registration-form" method="post">

                                <?php wp_nonce_field('register_employer_action', 'register_employer_nonce'); ?>
                                <input type="hidden" name="action" value="register_employer">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label for="employer_username">Name*</label>
                                            <input type="text" name="employer_username" id="employer_username" placeholder="Enter Your Name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label for="employer_email">Email*</label>
                                            <input type="email" name="employer_email" id="employer_email" placeholder="Enter Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label for="employer_pass">Password*</label>
                                            <input type="password" name="employer_pass" id="employer_pass" placeholder="Enter Your Password" class="pass_log_id" required>
                                            <span class="placeholder_icon">
                                                <span class="passVicon">
                                                    <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobly'); ?>">
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label for="employer_confirm_pass">Password*</label>
                                            <input type="password" name="employer_confirm_pass" id="employer_confirm_pass" placeholder="Enter Your Confirm Password" class="pass_log_id" required>
                                            <span class="placeholder_icon">
                                                <span class="passVicon">
                                                    <img src="<?php echo JOBLY_IMG . '/icons/icon-eye.svg' ?>" alt="<?php esc_attr_e('eye', 'jobly'); ?>">
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="agreement-checkbox d-flex justify-content-between align-items-center">
                                            <div>
                                                <input type="checkbox" id="remember">
                                                <label for="remember">By hitting the "Register" button, you agree to the <a href="#">Terms conditions</a> & <a href="#">Privacy Policy</a></label>
                                            </div>
                                        </div> <!-- /.agreement-checkbox -->
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn-eleven fw-500 tran3s d-block mt-20"><?php esc_html_e('Register', 'jobly'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>

                    <div class="d-flex align-items-center mt-30 mb-10">
                        <div class="line"></div>
                    </div>

                    <p class="text-center mt-10"><?php esc_html_e('Have an account?', 'jobly'); ?>
                        <a href="#" class="fw-500" data-bs-toggle="modal" data-bs-target="#loginModal"><?php esc_html_e('Sign In', 'jobly'); ?></a>
                    </p>

                </div>

            </div>
            <?php
        }

        ?>

        <?php

        return ob_get_clean();

    }


    /**
     * Displays the job archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function job_page_layout( $args = [] ) {

        if ( ! is_admin() ) {
            jobly_get_template( 'archive-job.php', [
                'jobly_job_archive_layout' => $args['job_layout'],
            ] );
        }

    }


    /**
     * Displays the company archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function company_page_layout( $args = [] ) {

        if ( ! is_admin() ) {
            jobly_get_template( 'archive-company.php', [
                'jobly_company_archive_layout' => $args['company_layout'],
            ] );
        }

    }


    /**
     * Displays the candidate archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function candidate_page_layout( $args = [] )
    {

        if (!is_admin()) {
            jobly_get_template('archive-candidate.php', [
                'jobly_candidate_archive_layout' => $args['candidate_layout'],
            ]);
        }

    }

}
