<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$user = wp_get_current_user();

// Handle form submission
if ( isset( $_POST['candidate_name'] ) || isset( $_POST['profile_picture_action'] ) || isset( $_POST['candidate_description'] ) ) {

	// Process name change
	if ( ! empty( $_POST['candidate_name'] ) ) {
		$name = sanitize_text_field( $_POST['candidate_name'] );

		// Update user's display name
		wp_update_user( array(
			'ID'           => $user->ID,
			'display_name' => $name
		) );
	}

	// Process profile picture
	if ( isset( $_POST['profile_picture_action'] ) ) {
		$action = sanitize_text_field( $_POST['profile_picture_action'] );

		// Delete profile picture
		if ( $action === 'delete' ) {
			delete_user_meta( $user->ID, 'candidate_profile_picture' );
			// Success message
			$success_message = __( 'Profile picture deleted successfully.', 'jobus' );
		}

		// Upload new profile picture
		if ( $action === 'upload' && isset( $_FILES['candidate_profile_picture'] ) && $_FILES['candidate_profile_picture']['error'] === 0 ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			// Upload the file and get attachment ID
			$attachment_id = media_handle_upload( 'candidate_profile_picture', 0 );

			if ( ! is_wp_error( $attachment_id ) ) {
				// Get image URL and save to user meta
				$image_url = wp_get_attachment_url( $attachment_id );
				update_user_meta( $user->ID, 'candidate_profile_picture', $image_url );
				// Success message
				$success_message = __( 'Profile updated successfully.', 'jobus' );
			} else {
				// Error message
				$error_message = $attachment_id->get_error_message();
			}
		}
	}

	// Save candidate description (bio)
	if ( isset( $_POST['candidate_description'] ) ) {
		$description = wp_kses_post( $_POST['candidate_description'] );
		update_user_meta( $user->ID, 'description', $description );
	}

	// Refresh user data
	$user = wp_get_current_user();
}

// Check if the user has uploaded a custom profile image
$custom_avatar_url = get_user_meta( $user->ID, 'candidate_profile_picture', true );
$avatar_url        = '';
if ( ! empty( $custom_avatar_url ) ) {
	$avatar_url = $custom_avatar_url;
} else {
	// If no custom avatar, use the default WordPress avatar for users with no image
	$avatar_url = get_avatar_url( 0 );
}

$user_bio = get_user_meta( $user->ID, 'description', true );

//Sidebar Menu
include( 'candidate-templates/sidebar-menu.php' );
?>

<div class="dashboard-body">
    <div class="position-relative">

		<?php include( 'candidate-templates/action-btn.php' ); ?>

        <h2 class="main-title"><?php esc_html_e( 'My Profile', 'jobus' ); ?></h2>

		<?php
		// Display success message
		if ( isset( $success_message ) ) {
			echo '<div class="alert alert-success" role="alert">' . esc_html( $success_message ) . '</div>';
		}

		// Display error message
		if ( isset( $error_message ) ) {
			echo '<div class="alert alert-danger" role="alert">' . esc_html( $error_message ) . '</div>';
		}
		?>

        <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" id="candidateProfileForm" method="post" enctype="multipart/form-data">

            <div class="bg-white card-box border-20">

                <div class="user-avatar-setting d-flex align-items-center mb-30">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img"
                         id="candidate_avatar">
                    <div class="upload-btn position-relative tran3s ms-4 me-3">
						<?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                        <input type="file" id="uploadImg" name="candidate_profile_picture" accept="image/*">
                    </div>

                    <button type="button" name="delete_profile_picture" class="delete-btn tran3s" id="delete_profile_picture">
						<?php esc_html_e( 'Delete', 'jobus' ); ?>
                    </button>
                    <input type="hidden" name="profile_picture_action" id="profile_picture_action" value="">
                </div>

                <div class="dash-input-wrapper mb-30">
                    <label for="candidate_name"><?php esc_html_e( 'Full Name*', 'jobus' ); ?></label>
                    <input type="text" name="candidate_name" id="candidate_name" value="<?php echo esc_attr( $user->display_name ); ?>">
                </div>

                <div class="dash-input-wrapper">
                    <label for="candidate_description"><?php esc_html_e( 'Bio*', 'jobus' ); ?></label>
                    <div class="editor-wrapper">
						<?php
						wp_editor(
							$user_bio,
							'candidate_description',
							array(
								'textarea_name' => 'candidate_description',
								'textarea_rows' => 8,
								'media_buttons' => true,
								'teeny'         => false, // Use full toolbar
								'quicktags'     => true,
								'editor_class'  => 'size-lg',
								'tinymce'       => array(
									'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6',
									'toolbar1'      => 'formatselect bold italic underline bullist numlist blockquote alignleft aligncenter alignright link unlink undo redo wp_adv',
									'toolbar2'      => 'strikethrough hr forecolor pastetext removeformat charmap outdent indent',
								),
							)
						);
						?>
                    </div>
                    <div class="alert-text">
						<?php esc_html_e( 'Brief description for your profile. URLs are hyperlinked.', 'jobus' ); ?>
                    </div>
                </div>
            </div>

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three">
                    <?php esc_html_e( 'Social Media', 'jobus' ); ?>
                </h4>
                <div id="cmb-group-_candidate_socials-0" class="postbox cmb-row cmb-repeatable-grouping closed" data-iterator="0">
                    <button type="button" data-selector="_candidate_socials_repeat" data-confirm=""
                            class="dashicons-before dashicons-no-alt cmb-remove-group-row" title="Remove Network"></button>
                    <div class="cmbhandle" title="Click to toggle"><br></div>
                    <h3 class="cmb-group-title cmbhandle-title">Network 1</h3>

                    <div class="inside cmb-td cmb-nested cmb-field-list">
                        <div class="cmb-row cmb-type-select cmb2-id--candidate-socials-0-network cmb-repeat-group-field" data-fieldtype="select">
                            <div class="cmb-th">
                                <label for="_candidate_socials_1_network">Network</label>
                            </div>
                            <div class="cmb-td">
                                <select class="cmb2_select select2-hidden-accessible" name="_candidate_socials[0][network]" id="_candidate_socials_1_network"
                                        data-hash="3ocd0q82oak0" tabindex="-1" aria-hidden="true">
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter</option>
                                    <option value="linkedin">Linkedin</option>
                                    <option value="dribbble">Dribbble</option>
                                    <option value="tumblr">Tumblr</option>
                                    <option value="pinterest">Pinterest</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">Youtube</option>
                                    <option value="tiktok">Tiktok</option>
                                    <option value="telegram">Telegram</option>
                                    <option value="discord">Discord</option>
                                </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: auto;"><span
                                            class="selection"><span class="select2-selection select2-selection--single" aria-haspopup="true"
                                                                    aria-expanded="false" tabindex="0"
                                                                    aria-labelledby="select2-_candidate_socials_1_network-container" role="combobox"><span
                                                    class="select2-selection__rendered" id="select2-_candidate_socials_1_network-container" role="textbox"
                                                    aria-readonly="true" title="Facebook">Facebook</span><span class="select2-selection__arrow"
                                                                                                               role="presentation"><b
                                                        role="presentation"></b></span></span></span><span class="dropdown-wrapper"
                                                                                                           aria-hidden="true"></span></span>
                            </div>
                        </div>
                        <div class="cmb-row cmb-type-text cmb2-id--candidate-socials-0-url cmb-repeat-group-field table-layout" data-fieldtype="text">
                            <div class="cmb-th">
                                <label for="_candidate_socials_1_url">Url</label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="regular-text" name="_candidate_socials[0][url]" id="_candidate_socials_1_url" value="#"
                                       data-hash="3098diak8qc0">
                            </div>
                        </div>
                        <div class="cmb-row cmb-remove-field-row">
                            <div class="cmb-remove-row">
                                <button type="button" data-selector="_candidate_socials_repeat" data-confirm=""
                                        class="cmb-remove-group-row cmb-remove-group-row-button alignright button-secondary">Remove Network
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Initial Social Media Links -->
                <div class="dash-input-wrapper mb-20">
                    <label for="">Network 1</label>
                    <input type="text" placeholder="https://www.facebook.com/zubayer0145">
                </div>

                <!-- /.dash-input-wrapper -->
                <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more link</a>
            </div>
            <!-- /.card-box -->

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three">Address & Location</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Address*</label>
                            <input type="text" placeholder="Cowrasta, Chandana, Gazipur Sadar">
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Country*</label>
                            <select class="nice-select">
                                <option>Afghanistan</option>
                                <option>Albania</option>
                                <option>Algeria</option>
                                <option>Andorra</option>
                                <option>Angola</option>
                                <option>Antigua and Barbuda</option>
                                <option>Argentina</option>
                                <option>Armenia</option>
                                <option>Australia</option>
                                <option>Austria</option>
                                <option>Azerbaijan</option>
                                <option>Bahamas</option>
                                <option>Bahrain</option>
                                <option>Bangladesh</option>
                                <option>Barbados</option>
                                <option>Belarus</option>
                                <option>Belgium</option>
                                <option>Belize</option>
                                <option>Benin</option>
                                <option>Bhutan</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">City*</label>
                            <select class="nice-select">
                                <option>Dhaka</option>
                                <option>Tokyo</option>
                                <option>Delhi</option>
                                <option>Shanghai</option>
                                <option>Mumbai</option>
                                <option>Bangalore</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Zip Code*</label>
                            <input type="number" placeholder="1708">
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">State*</label>
                            <select class="nice-select">
                                <option>Dhaka</option>
                                <option>Tokyo</option>
                                <option>Delhi</option>
                                <option>Shanghai</option>
                                <option>Mumbai</option>
                                <option>Bangalore</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Map Location*</label>
                            <div class="position-relative">
                                <input type="text" placeholder="XC23+6XC, Moiran, N105">
                                <button class="location-pin tran3s"><img src="../images/lazy.svg" data-src="images/icon/icon_16.svg" alt=""
                                                                         class="lazy-img m-auto"></button>
                            </div>
                            <div class="map-frame mt-30">
                                <div class="gmap_canvas h-100 w-100">
                                    <iframe class="gmap_iframe h-100 w-100"
                                            src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=dhaka collage&amp;t=&amp;z=12&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                </div>
                            </div>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                </div>
            </div>
            <!-- /.card-box -->

            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save', 'jobus' ); ?></button>
                <a href="#" class="dash-cancel-btn tran3s">Cancel</a>
            </div>

        </form>

    </div>
</div>