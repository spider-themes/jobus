<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$user = wp_get_current_user();

// Get candidate post ID for current user
$candidate_id = false;
$args = array(
    'post_type'      => 'jobus_candidate',
    'author'         => $user->ID,
    'posts_per_page' => 1,
    'fields'         => 'ids',
);

$candidate_query = new WP_Query($args);
if ( ! empty($candidate_query->posts) ) {
    $candidate_id = $candidate_query->posts[0];
}

// Handle form submission
if ( isset( $_POST['candidate_name'] ) || isset( $_POST['profile_picture_action'] ) || isset( $_POST['candidate_description'] ) || isset( $_POST['social_icons'] ) ) {

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

	// Save social icons to candidate post meta (inside jobus_meta_candidate_options)
	if ( $candidate_id && isset( $_POST['social_icons'] ) && is_array( $_POST['social_icons'] ) ) {
		$social_icons = array();
		foreach ( $_POST['social_icons'] as $item ) {
			$icon = isset($item['icon']) ? sanitize_text_field($item['icon']) : '';
			$url  = isset($item['url']) ? esc_url_raw($item['url']) : '';
			// Save all items, even if url is empty (to preserve new/empty fields)
			if ( $icon ) {
				$social_icons[] = array('icon' => $icon, 'url' => $url);
			}
		}
		// Load full meta array
		$meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
		if (!is_array($meta)) $meta = [];

		// Update only the social_icons key
		$meta['social_icons'] = $social_icons;
		update_post_meta( $candidate_id, 'jobus_meta_candidate_options', $meta );
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
                <?php
                $available_icons = array(
                    'bi bi-facebook'  => esc_html__( 'Facebook', 'jobus' ),
                    'bi bi-instagram' => esc_html__( 'Instagram', 'jobus' ),
                    'bi bi-twitter'   => esc_html__( 'Twitter', 'jobus' ),
                    'bi bi-linkedin'  => esc_html__( 'LinkedIn', 'jobus' ),
                    'bi bi-github'    => esc_html__( 'GitHub', 'jobus' ),
                    'bi bi-youtube'   => esc_html__( 'YouTube', 'jobus' ),
                    'bi bi-dribbble'  => esc_html__( 'Dribbble', 'jobus' ),
                    'bi bi-behance'   => esc_html__( 'Behance', 'jobus' ),
                    'bi bi-pinterest' => esc_html__( 'Pinterest', 'jobus' ),
                    'bi bi-tiktok'    => esc_html__( 'TikTok', 'jobus' ),
                );
                $user_social_links = array();
                if ( $candidate_id ) {
                    $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
                    if ( is_array($meta) && !empty($meta['social_icons']) ) {
                        $user_social_links = $meta['social_icons'];
                    }
                }
                if ( ! is_array( $user_social_links ) ) {
                    $user_social_links = array();
                }
                ?>
                <div id="social-links-repeater">
                    <?php
                    foreach ( $user_social_links as $index => $item ) :
                        ?>
                        <div class="dash-input-wrapper mb-20 social-link-item d-flex align-items-center gap-2">
                            <label for="" class="me-2 mb-0"><?php echo esc_html__( 'Network', 'jobus' ) . ' ' . esc_html( $index + 1 ); ?></label>
                            <select name="social_icons[<?php echo esc_attr( $index ); ?>][icon]" class="form-select icon-select me-2" style="max-width:140px" aria-label="#">
                                <?php foreach ( $available_icons as $icon_class => $icon_label ) : ?>
                                    <option value="<?php echo esc_attr( $icon_class ); ?>" <?php selected( $item['icon'], $icon_class ); ?>>
                                        <?php echo esc_html( $icon_label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="social_icons[<?php echo esc_attr( $index ); ?>][url]" class="form-control me-2" placeholder="#" value="<?php echo esc_attr( $item['url'] ); ?>" style="min-width:260px">
                            <button type="button" class="btn btn-danger remove-social-link" title="<?php echo esc_attr__( 'Remove Item', 'jobus' ); ?>"><i class="bi bi-x"></i></button>
                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
                <a href="javascript:void(0)" class="dash-btn-one" id="add-social-link">
                    <i class="bi bi-plus"></i>
                    <?php esc_html_e( 'Add more link', 'jobus' ); ?>
                </a>
            </div>

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
                            <label for="">Age*</label>
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

            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save', 'jobus' ); ?></button>
                <a href="#" class="dash-cancel-btn tran3s">Cancel</a>
            </div>

        </form>

    </div>
</div>