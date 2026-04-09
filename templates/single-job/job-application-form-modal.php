<?php
/**
 * Job Application Form Modal Template
 * Implements: Choice Gate (guest only) → Application Form → Post-Submit Upsell
 */
?>
<div class="jbs-modal jbs-fade job-application-wrapper" id="filterPopUp" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
    <div class="jbs-modal-dialog jbs-modal-dialog-centered">
        <div class="jbs-modal-content">
            <div class="jbs-modal-header">
                <h5 class="jbs-modal-title" id="applyJobModalLabel"><?php esc_html_e( 'Apply for this Position', 'jobus' ); ?></h5>
                <button type="button" class="jbs-btn-close jbs-pointer" data-jbs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="jbs-modal-body">
                <div class="jbs-container">
                    <?php
                    $btn_class = $job_single_layout == '1' ? 'btn-one' : 'jbs-btn-ten jbs-text-white';

                    // Get current user information
                    $user            = wp_get_current_user();
                    $candidate_fname = get_user_meta( $user->ID, 'first_name', true ) ?: $user->display_name;
                    $candidate_lname = get_user_meta( $user->ID, 'last_name', true ) ?: '';
                    $candidate_email = $user->user_email;

                    // Helper: check if a field is required via plugin settings
                    if ( ! function_exists( 'jobus_is_field_required' ) ) {
                        function jobus_is_field_required( $field_name ) {
                            $setting_key = 'required_' . $field_name;
                            return jobus_opt( $setting_key, $field_name === 'first_name' || $field_name === 'email' );
                        }
                    }
                    ?>

                    <?php if ( ! is_user_logged_in() ) :
                        $guest_btn_label = jobus_opt( 'guest_btn_label', __( 'Continue as Guest', 'jobus' ) );
                    ?>
                    <!-- ===== PANEL 1: Choice Gate (guest only) ===== -->
                    <div id="jbs-apply-gate-panel">
                        <p class="jbs-text-center jbs-text-muted jbs-mb-4 jbs-fs-sm">
                            <?php esc_html_e( 'How would you like to apply?', 'jobus' ); ?>
                        </p>
                        <div class="jbs-d-flex jbs-gap-3 jbs-mb-3">
                            <a href="<?php echo esc_url( $signin_url ); ?>"
                               class="jbs-btn jbs-btn-primary jbs-btn-block">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <?php echo esc_html( $signin_label ); ?>
                            </a>
                            <a href="<?php echo esc_url( $register_url ); ?>"
                               class="jbs-btn jbs-btn-register jbs-btn-block">
                                <i class="bi bi-person-plus"></i>
                                <?php echo esc_html( $register_label ); ?>
                            </a>
                        </div>
                        <div class="jbs-text-center jbs-m2-4 jbs-mb-4">
                            <span class="jbs-text-muted jbs-fs-sm"><?php esc_html_e( '— or —', 'jobus' ); ?></span>
                        </div>
                        <button type="button" id="jbs-continue-as-guest" class="jbs-btn jbs-btn-guest jbs-btn-block">
                            <?php echo esc_html( $guest_btn_label ); ?>
                            <i class="bi bi-arrow-right jbs-ms-1"></i>
                        </button>
                    </div>
                    <!-- ===== END PANEL 1 ===== -->
                    <?php endif; ?>

                    <!-- ===== PANEL 2: Application Form ===== -->
                    <div id="jbs-apply-form-panel"<?php if ( ! is_user_logged_in() ) : ?> style="display:none;"<?php endif; ?>>
                        <form action="#" name="job_application_form" class="job-application-form" id="jobApplicationForm" method="post"
                              enctype="multipart/form-data">
                            <div class="jbs-row jbs-gy-4">
                                <input type="hidden" name="job_application_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                                <input type="hidden" name="job_application_title" value="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>">
                                <input type="hidden" name="job_application_nonce" value="<?php echo esc_attr( wp_create_nonce( 'jobus_job_application' ) ); ?>">

                                <div class="jbs-col-md-6">
                                    <label for="firstName" class="jbs-form-label">
                                        <?php esc_html_e( 'First Name', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'first_name' ) ) echo '*'; ?>
                                    </label>
                                    <input type="text" class="jbs-form-control" id="firstName" name="candidate_fname"
                                           value="<?php echo esc_attr( $candidate_fname ); ?>"
                                           placeholder="<?php esc_attr_e( 'Enter your first name', 'jobus' ); ?>"
                                           <?php echo jobus_is_field_required( 'first_name' ) ? 'required' : ''; ?>>
                                </div>

                                <div class="jbs-col-md-6">
                                    <label for="lastName" class="jbs-form-label">
                                        <?php esc_html_e( 'Last Name', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'last_name' ) ) echo '*'; ?>
                                    </label>
                                    <input type="text" class="jbs-form-control" id="lastName" name="candidate_lname"
                                           value="<?php echo esc_attr( $candidate_lname ); ?>"
                                           placeholder="<?php esc_attr_e( 'Enter your last name', 'jobus' ); ?>"
                                           <?php echo jobus_is_field_required( 'last_name' ) ? 'required' : ''; ?>>
                                </div>

                                <div class="jbs-col-md-12">
                                    <label for="email" class="jbs-form-label">
                                        <?php esc_html_e( 'Email', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'email' ) ) echo '*'; ?>
                                    </label>
                                    <input type="email" class="jbs-form-control" id="email" name="candidate_email"
                                           value="<?php echo esc_attr( $candidate_email ); ?>"
                                           placeholder="<?php esc_attr_e( 'Enter your email address', 'jobus' ); ?>"
                                           <?php echo jobus_is_field_required( 'email' ) ? 'required' : ''; ?>>
                                </div>

                                <div class="jbs-col-md-12">
                                    <label for="phone" class="jbs-form-label">
                                        <?php esc_html_e( 'Phone', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'phone' ) ) echo '*'; ?>
                                    </label>
                                    <input type="tel" class="jbs-form-control" id="phone" name="candidate_phone"
                                           placeholder="<?php esc_attr_e( 'Enter your phone number', 'jobus' ); ?>"
                                           <?php echo jobus_is_field_required( 'phone' ) ? 'required' : ''; ?>>
                                </div>

                                <div class="jbs-col-md-12">
                                    <label for="message" class="jbs-form-label">
                                        <?php esc_html_e( 'Message', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'message' ) ) echo '*'; ?>
                                    </label>
                                    <textarea class="jbs-form-control" id="message" name="candidate_message" rows="4"
                                              placeholder="<?php esc_attr_e( 'Enter your message or cover letter', 'jobus' ); ?>"
                                              <?php echo jobus_is_field_required( 'message' ) ? 'required' : ''; ?>></textarea>
                                </div>

                                <div class="jbs-col-md-12">
                                    <label for="upload_cv" class="jbs-form-label">
                                        <?php esc_html_e( 'Upload CV (PDF or Word)', 'jobus' ); ?>
                                        <?php if ( jobus_is_field_required( 'cv' ) ) echo '*'; ?>
                                    </label>
                                    <input type="file" class="jbs-form-control upload-cv" id="upload_cv" name="candidate_cv"
                                           accept=".pdf,.doc,.docx"
                                           <?php echo jobus_is_field_required( 'cv' ) ? 'required' : ''; ?>>
                                </div>

                                <div class="jbs-col-md-12">
                                    <button type="submit" class="jbs-btn <?php echo esc_attr( $btn_class ) ?>">
                                        <?php esc_html_e( 'Submit Application', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- ===== END PANEL 2 ===== -->

                    <!-- ===== PANEL 3: Success Message ===== -->
                    <div id="applicationSuccessMessage" style="display:none;" class="jbs-mt-3">
                        <div class="jbs-alert jbs-alert-success">
                            <h6 class="jbs-mb-2 jbs-d-flex jbs-align-items-center">
                                <i class="bi bi-check-circle-fill jbs-text-success jbs-me-2"></i>
                                <?php esc_html_e( 'Application Submitted Successfully!', 'jobus' ); ?>
                            </h6>
                            <p class="jbs-mb-0 jbs-fs-sm">
                                <?php esc_html_e( 'Your resume has been sent to the employer.', 'jobus' ); ?>
                            </p>
                        </div>

                        <?php if ( ! is_user_logged_in() ) : ?>
                            <div class="jbs-alert jbs-alert-warning jbs-p-15 jbs-rounded jbs-text-dark jbs-mt-3">
                                <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-between jbs-flex-wrap jbs-gap-3">
                                    <div>
                                        <h6 class="jbs-fw-bold jbs-mb-1"><?php esc_html_e( 'Track Your Application', 'jobus' ); ?></h6>
                                        <p class="jbs-mb-0 jbs-fs-sm">
                                            <?php esc_html_e( 'Want to see if the employer views your resume? Create a free account now.', 'jobus' ); ?>
                                        </p>
                                    </div>
                                    <a href="<?php echo esc_url( $register_url ); ?>" class="jbs-btn jbs-btn-dark jbs-btn-sm jbs-text-white jbs-text-nowrap">
                                        <?php echo esc_html( $register_label ); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- ===== END PANEL 3 ===== -->

                </div>
            </div>
        </div>
    </div>
</div>


<script>
jQuery(document).ready(function($) {

    // ── Choice Gate Panel Switch ──────────────────────────────
    $('#jbs-continue-as-guest').on('click', function () {
        $('#jbs-apply-gate-panel').hide();
        $('#jbs-apply-form-panel').show();
    });

    // ── Form Validation ───────────────────────────────────────
    const form = document.getElementById('jobApplicationForm');
    if (form) {
        const firstName = document.getElementById('firstName');
        if (firstName && firstName.hasAttribute('required')) {
            firstName.addEventListener('invalid', function() {
                this.setCustomValidity('<?php echo esc_js( __( 'Please enter your first name', 'jobus' ) ); ?>');
            });
            firstName.addEventListener('input', function() { this.setCustomValidity(''); });
        }
        
        const lastName = document.getElementById('lastName');
        if (lastName && lastName.hasAttribute('required')) {
            lastName.addEventListener('invalid', function() {
                this.setCustomValidity('<?php echo esc_js( __( 'Please enter your last name', 'jobus' ) ); ?>');
            });
            lastName.addEventListener('input', function() { this.setCustomValidity(''); });
        }
        
        const email = document.getElementById('email');
        if (email && email.hasAttribute('required')) {
            email.addEventListener('invalid', function() {
                if (this.validity.valueMissing) {
                    this.setCustomValidity('<?php echo esc_js( __( 'Please enter your email address', 'jobus' ) ); ?>');
                } else if (this.validity.typeMismatch) {
                    this.setCustomValidity('<?php echo esc_js( __( 'Please enter a valid email address', 'jobus' ) ); ?>');
                }
            });
            email.addEventListener('input', function() { this.setCustomValidity(''); });
        }
        
        const phone = document.getElementById('phone');
        if (phone && phone.hasAttribute('required')) {
            phone.addEventListener('invalid', function() {
                this.setCustomValidity('<?php echo esc_js( __( 'Please enter your phone number', 'jobus' ) ); ?>');
            });
            phone.addEventListener('input', function() { this.setCustomValidity(''); });
        }
        
        const message = document.getElementById('message');
        if (message && message.hasAttribute('required')) {
            message.addEventListener('invalid', function() {
                this.setCustomValidity('<?php echo esc_js( __( 'Please enter your message or cover letter', 'jobus' ) ); ?>');
            });
            message.addEventListener('input', function() { this.setCustomValidity(''); });
        }
        
        const uploadCv = document.getElementById('upload_cv');
        if (uploadCv && uploadCv.hasAttribute('required')) {
            uploadCv.addEventListener('invalid', function() {
                this.setCustomValidity('<?php echo esc_js( __( 'Please upload your CV', 'jobus' ) ); ?>');
            });
            uploadCv.addEventListener('change', function() { this.setCustomValidity(''); });
        }
    }
});
</script>
