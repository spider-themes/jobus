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

        add_shortcode( 'jobly_register_form', [ $this, 'register_form_shortcode' ] );

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_candidate_nonce']) && wp_verify_nonce($_POST['register_candidate_nonce'], 'register_candidate_action')) {
            // Get form data
            $username = sanitize_user($_POST['candidate_username']);
            $email = sanitize_email($_POST['candidate_email']);
            $password = $_POST['candidate_pass'];
            $confirm_password = $_POST['candidate_confirm_pass'];

            // Check if passwords match
            if ($password !== $confirm_password) {
                echo '<p class="text-center mt-10">' . __('Passwords do not match', 'jobly') . '</p>';
            } else {
                // Check if username or email already exists
                if (username_exists($username) || email_exists($email)) {
                    echo '<p class="text-center mt-10">' . __('Username or email already exists', 'jobly') . '</p>';
                } else {
                    // Create new user
                    $user_id = wp_create_user($username, $password, $email);
                    if (is_wp_error($user_id)) {
                        echo '<p class="text-center mt-10">' . $user_id->get_error_message() . '</p>';
                    } else {
                        // Assign custom role to user
                        $user = new \WP_User($user_id);
                        $user->set_role('jobly_candidate'); // Assign the custom 'jobly_candidate' role

                        // Log the user in
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id);
                        do_action('wp_login', $username, $user);

                        // Redirect to admin panel
                        wp_redirect(admin_url());
                        exit;
                    }
                }
            }
        }



        ob_start();

        if ( is_user_logged_in() ) {
            ?>
            <p class="text-center mt-10">You are already logged in.</p>
            <?php
        } else {
            ?>
            <div class="user-data-form">

                <div class="text-center">
                    <h2>Create Account</h2>
                </div>

                <div class="form-wrapper m-auto">
                    <ul class="nav nav-tabs border-0 w-100 mt-30" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#fc1" role="tab">Candidates</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fc2" role="tab">Employer</button>
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
                                            <label>Name*</label>
                                            <input type="text" name="candidate_username" placeholder="Rashed Kabir">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label>Email*</label>
                                            <input type="email" name="candidate_email" placeholder="rshdkabir@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label>Password*</label>
                                            <input type="password" name="candidate_pass" placeholder="Enter Password" class="pass_log_id">
                                            <span class="placeholder_icon"><span class="passVicon"><img src="images/icon/icon_60.svg" alt=""></span></span>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label>Password*</label>
                                            <input type="password" name="candidate_confirm_pass" placeholder="Enter Confirm Password" class="pass_log_id">
                                            <span class="placeholder_icon"><span class="passVicon"><img src="images/icon/icon_60.svg" alt=""></span></span>
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
                            <form action="#">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label>Name*</label>
                                            <input type="text" placeholder="Zubayer Hasan">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-25">
                                            <label>Email*</label>
                                            <input type="email" placeholder="zubayerhasan@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group-meta position-relative mb-20">
                                            <label>Password*</label>
                                            <input type="password" placeholder="Enter Password" class="pass_log_id">
                                            <span class="placeholder_icon"><span class="passVicon"><img src="images/icon/icon_60.svg" alt=""></span></span>
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
                                        <button class="btn-eleven fw-500 tran3s d-block mt-20">Register</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>

                    <div class="d-flex align-items-center mt-30 mb-10">
                        <div class="line"></div>
                        <span class="pe-3 ps-3">OR</span>
                        <div class="line"></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <a href="#" class="social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10">
                                <img src="images/icon/google.png" alt="">
                                <span class="ps-2">Signup with Google</span>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="#" class="social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10">
                                <img src="images/icon/facebook.png" alt="">
                                <span class="ps-2">Signup with Facebook</span>
                            </a>
                        </div>
                    </div>

                    <p class="text-center mt-10">Have an account? <a href="#" class="fw-500" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a></p>

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
