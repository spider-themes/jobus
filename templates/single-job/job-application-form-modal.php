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
                    $candidate_fname = '';
                    $candidate_lname = '';
                    $candidate_email = '';

                    // Fallback to display name if first/last names are missing
                    $candidate_fname = get_user_meta( $user->ID, 'first_name', true ) ?: $user->display_name;
                    $candidate_lname = get_user_meta( $user->ID, 'last_name', true ) ?: '';
                    $candidate_email = $user->user_email;
                    ?>
                    <form action="#" name="job_application_form" class="job-application-form" id="jobApplicationForm" method="post"
                          enctype="multipart/form-data">
                        <div class="jbs-row jbs-gy-4">

                            <input type="hidden" name="job_application_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                            <input type="hidden" name="job_application_title" value="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>">
                            <input type="hidden" name="job_application_nonce" value="<?php echo esc_attr( wp_create_nonce( 'jobus_job_application' ) ); ?>">

                            <div class="jbs-col-md-6">
                                <label for="firstName" class="jbs-form-label"><?php esc_html_e( 'First Name*', 'jobus' ); ?></label>
                                <input type="text" class="jbs-form-control" id="firstName" name="candidate_fname"
                                       value="<?php echo esc_attr( $candidate_fname ); ?>"
                                       placeholder="<?php esc_attr_e( 'Enter your first name', 'jobus' ); ?>" required>
                            </div>

                            <div class="jbs-col-md-6">
                                <label for="lastName" class="jbs-form-label"><?php esc_html_e( 'Last Name', 'jobus' ); ?></label>
                                <input type="text" class="jbs-form-control" id="lastName" name="candidate_lname"
                                       value="<?php echo esc_attr( $candidate_lname ); ?>"
                                       placeholder="<?php esc_attr_e( 'Enter your last name', 'jobus' ); ?>">
                            </div>

                            <div class="jbs-col-md-12">
                                <label for="email" class="jbs-form-label"><?php esc_html_e( 'Email*', 'jobus' ); ?></label>
                                <input type="email" class="jbs-form-control" id="email" name="candidate_email"
                                       value="<?php echo esc_attr( $candidate_email ); ?>"
                                       placeholder="<?php esc_attr_e( 'Enter your email address', 'jobus' ); ?>" required>
                            </div>

                            <div class="jbs-col-md-12">
                                <label for="phone" class="jbs-form-label"><?php esc_html_e( 'Phone', 'jobus' ); ?></label>
                                <input type="tel" class="jbs-form-control" id="phone" name="candidate_phone"
                                       placeholder="<?php esc_attr_e( 'Enter your phone number', 'jobus' ); ?>">
                            </div>

                            <div class="jbs-col-md-12">
                                <label for="message" class="jbs-form-label"><?php esc_html_e( 'Message', 'jobus' ); ?></label>
                                <textarea class="jbs-form-control" id="message" name="candidate_message" rows="4"
                                          placeholder="<?php esc_attr_e( 'Enter your message or cover letter', 'jobus' ); ?>"></textarea>
                            </div>

                            <div class="jbs-col-md-12">
                                <label for="upload_cv" class="jbs-form-label"><?php esc_html_e( 'Upload CV (PDF or Word)', 'jobus' ); ?></label>
                                <input type="file" class="jbs-form-control upload-cv" id="upload_cv" name="candidate_cv" accept=".pdf,.doc,.docx">
                            </div>

                            <div class="jbs-col-md-12">
                                <button type="submit" class="jbs-btn <?php echo esc_attr( $btn_class ) ?>">
                                    <?php esc_html_e( 'Submit Application', 'jobus' ); ?>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="applicationSuccessMessage" style="display:none;" class="jbs-alert jbs-alert-success jbs-mt-3">
                        <?php esc_html_e( 'Your application has been submitted successfully.', 'jobus' ); ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>