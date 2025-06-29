<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current logged-in user object
$user = wp_get_current_user();

// Retrieve the candidate post ID for the current user (if exists)
$candidate_id = false;
$args = array(
    'post_type'      => 'jobus_candidate', // Custom post type for candidates
    'author'         => $user->ID,         // Filter by current user as author
    'posts_per_page' => 1,                 // Only need one post (should be unique)
    'fields'         => 'ids',             // Only get post IDs
);

$candidate_query = new WP_Query($args);
if ( ! empty($candidate_query->posts) ) {
    $candidate_id = $candidate_query->posts[0];
}

// Set default candidate location values (used if no meta is set)
$candidate_location = array(
    'address'   => 'Dhaka Division, Bangladesh',
    'latitude'  => '23.9456166',
    'longitude' => '90.2526382',
    'zoom'      => '20',
);

// If candidate meta exists, override location defaults with saved values
if ( $candidate_id ) {
    $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
    if ( is_array($meta) && isset($meta['jobus_candidate_location']) && is_array($meta['jobus_candidate_location']) ) {
        $candidate_location = wp_parse_args($meta['jobus_candidate_location'], $candidate_location);
    }
}

// Handle location update on form submission
if ( $candidate_id && isset($_POST['candidate_location_address']) ) {
    $location = array(
        'address'   => sanitize_text_field($_POST['candidate_location_address']),
        'latitude'  => sanitize_text_field($_POST['candidate_location_lat']),
        'longitude' => sanitize_text_field($_POST['candidate_location_lng']),
        'zoom'      => sanitize_text_field($_POST['candidate_location_zoom']),
    );
    $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
    if (!is_array($meta)) $meta = array();
    $meta['jobus_candidate_location'] = $location;
    update_post_meta( $candidate_id, 'jobus_meta_candidate_options', $meta );

    // Also update a single meta field for compatibility (if needed)
    update_post_meta( $candidate_id, 'jobus_candidate_location', $location );

    // Update the local variable for immediate UI feedback
    $candidate_location = $location;
}

// Initialize specification variables
$candidate_specifications = array();
$candidate_age = '';
$candidate_mail = '';

// Dynamic select fields (Expert Level, Qualification, etc.)
$candidate_dynamic_fields = array();
// If candidate meta exists, override defaults for specifications
if ( $candidate_id ) {
    $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
    if ( is_array($meta) ) {
        $candidate_specifications = isset($meta['candidate_specifications']) && is_array($meta['candidate_specifications']) ? $meta['candidate_specifications'] : array();
        $candidate_age = $meta['candidate_age'] ?? '';
        $candidate_mail = $meta['candidate_mail'] ?? '';

        // Dynamic select fields (Expert Level, Qualification, etc.)
        $candidate_dynamic_fields = array();
        if (function_exists('jobus_opt')) {
            $candidate_spec_fields = jobus_opt('candidate_specifications');
            if (!empty($candidate_spec_fields)) {
                foreach ($candidate_spec_fields as $field) {
                    $meta_key = $field['meta_key'] ?? '';
                    if ($meta_key) {
                        $candidate_dynamic_fields[$meta_key] = $meta[ $meta_key ] ?? '';
                    }
                }
            }
        }
    }
}

// On form submit, update the candidate specification meta fields
if ( $candidate_id && (isset($_POST['candidate_mail']) || isset($_POST['candidate_age'])) ) {
    $meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
    if (!is_array($meta)) $meta = array();
    $meta['candidate_age'] = isset($_POST['candidate_age']) ? sanitize_text_field($_POST['candidate_age']) : '';
    $meta['candidate_mail'] = isset($_POST['candidate_mail']) ? sanitize_email($_POST['candidate_mail']) : '';

    // Handle dynamic select fields
    if (function_exists('jobus_opt')) {
        $candidate_spec_fields = jobus_opt('candidate_specifications');
        if (!empty($candidate_spec_fields)) {
            foreach ($candidate_spec_fields as $field) {
                $meta_key = $field['meta_key'] ?? '';
                if ($meta_key && isset($_POST[$meta_key])) {
                    $meta[$meta_key] = is_array($_POST[$meta_key]) ? array_map('sanitize_text_field', $_POST[$meta_key]) : sanitize_text_field($_POST[$meta_key]);
                }
            }
        }
    }
    // Save repeater fields for additional specifications
    $specs = array();
    if (isset($_POST['candidate_specifications']) && is_array($_POST['candidate_specifications'])) {
        foreach ($_POST['candidate_specifications'] as $spec) {
            $title = isset($spec['title']) ? sanitize_text_field($spec['title']) : '';
            $value = isset($spec['value']) ? sanitize_text_field($spec['value']) : '';
            if ($title !== '' || $value !== '') {
                $specs[] = array('title' => $title, 'value' => $value);
            }
        }
    }
    $meta['candidate_specifications'] = $specs;
    update_post_meta( $candidate_id, 'jobus_meta_candidate_options', $meta );
    $candidate_specifications = $specs;
    $candidate_age = $meta['candidate_age'] ?? '';
    $candidate_mail = $meta['candidate_mail'] ?? '';
    $candidate_dynamic_fields = $candidate_dynamic_fields ?? [];
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

		// Update candidate post title if exists
		if ( $candidate_id ) {
			wp_update_post( array(
				'ID'         => $candidate_id,
				'post_title' => $name
			) );
		}
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

		// Only update candidate post content
		if ( $candidate_id ) {
			wp_update_post( array(
				'ID'           => $candidate_id,
				'post_content' => $description
			) );
		}
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

// Set $description to the candidate post content if available
$description = '';
if ( $candidate_id ) {
    $candidate_post = get_post( $candidate_id );
    if ( $candidate_post ) {
        $description = $candidate_post->post_content;
    }
}

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
        <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" id="candidate-profile-form" method="post" enctype="multipart/form-data">

            <div class="bg-white card-box border-20" id="candidate-profile-description">

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
                    <label for="candidate_description"><?php esc_html_e( 'Description', 'jobus' ); ?></label>
                    <div class="editor-wrapper">
						<?php
						wp_editor(
							$description,
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
                    <?php esc_html_e( 'Add Social Item', 'jobus' ); ?>
                </a>
            </div>

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Specifications', 'jobus'); ?></h4>
                <div class="row">
                    <?php
                    if ( !empty($candidate_age) ) { ?>
                        <div class="col-lg-3">
                            <div class="dash-input-wrapper mb-25">
                                <label for="candidate_age"><?php esc_html_e('Date of Birth (Age)', 'jobus'); ?></label>
                                <input type="date" name="candidate_age" id="candidate_age" class="form-control" value="<?php echo esc_attr($candidate_age); ?>">
                            </div>
                        </div>
                        <?php
                    }
                    if ( !empty($candidate_mail) ) { ?>
                        <div class="col-lg-3">
                            <div class="dash-input-wrapper mb-25">
                                <label for="candidate_mail"><?php esc_html_e('Candidate Email', 'jobus'); ?></label>
                                <input type="email" name="candidate_mail" id="candidate_mail" class="form-control" value="<?php echo esc_attr($candidate_mail); ?>">
                            </div>
                        </div>
                        <?php
                    }

			        // Dynamic fields for candidate specifications
			        if (function_exists('jobus_opt')) {
				        $candidate_spec_fields = jobus_opt('candidate_specifications');
				        if (!empty($candidate_spec_fields)) {
					        foreach ($candidate_spec_fields as $field) {
						        $meta_key = $field['meta_key'] ?? '';
						        $meta_name = $field['meta_name'] ?? '';
						        $meta_value = $candidate_dynamic_fields[ $meta_key ] ?? '';
						        $meta_values = $field['meta_values_group'] ?? array();
						        echo '<div class="col-lg-3"><div class="dash-input-wrapper mb-25">';
						        echo '<label for="' . esc_attr($meta_key) . '">' . esc_html($meta_name) . '</label>';
						        echo '<select name="' . esc_attr($meta_key) . '[]" id="' . esc_attr($meta_key) . '" class="nice-select" multiple>';
						        foreach ($meta_values as $option) {
							        $val = strtolower(preg_replace('/[\s,]+/', '@space@', $option['meta_values']));
							        $selected = (is_array($meta_value) && in_array($val, $meta_value)) ? 'selected' : '';
							        echo '<option value="' . esc_attr($val) . '" ' . $selected . '>' . esc_html($option['meta_values']) . '</option>';
						        }
						        echo '</select></div></div>';
					        }
				        }
			        }
			        ?>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label><?php esc_html_e('Additional Specifications', 'jobus'); ?></label>
                            <div id="specifications-repeater">
						        <?php
                                if (!empty($candidate_specifications)) {
							        foreach ($candidate_specifications as $i => $spec) {
                                        ?>
                                        <div class="dash-input-wrapper mb-20 specification-item d-flex align-items-center gap-2">
                                            <input type="text" name="candidate_specifications[<?php echo esc_attr($i); ?>][title]" class="form-control me-2" placeholder="<?php esc_attr_e('Title', 'jobus'); ?>" value="<?php echo esc_attr($spec['title']); ?>" style="min-width:180px">
                                            <input type="text" name="candidate_specifications[<?php echo esc_attr($i); ?>][value]" class="form-control me-2" placeholder="<?php esc_attr_e('Value', 'jobus'); ?>" value="<?php echo esc_attr($spec['value']); ?>" style="min-width:180px">
                                            <button type="button" class="btn btn-danger remove-specification" title="<?php esc_attr_e('Remove', 'jobus'); ?>"><i class="bi bi-x"></i></button>
                                        </div>
							            <?php
                                    }
                                }
                                ?>
                            </div>
                            <a href="javascript:void(0)" class="dash-btn-one" id="add-specification">
                                <i class="bi bi-plus"></i> <?php esc_html_e('Add Specification', 'jobus'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Address & Location', 'jobus'); ?></h4>
                <div class="row">
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label for="candidate_location_address"><?php esc_html_e('Map Location*', 'jobus') ?></label>
                            <div class="position-relative">
                                <input type="text" name="candidate_location_address" id="candidate_location_address" placeholder="<?php esc_attr_e('XC23+6XC, Moiran, N105', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['address']); ?>">
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="candidate_location_lat" id="candidate_location_lat" placeholder="<?php esc_attr_e('Latitude', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['latitude']); ?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="candidate_location_lng" id="candidate_location_lng" placeholder="<?php esc_attr_e('Longitude', 'jobus'); ?>" value="<?php echo esc_attr($candidate_location['longitude']); ?>">
                                </div>
                            </div>
	                        <?php
	                        $lat = trim($candidate_location['latitude']);
	                        $lng = trim($candidate_location['longitude']);
	                        $zoom = !empty($candidate_location['zoom']) ? intval($candidate_location['zoom']) : 15;
	                        $is_http = is_ssl() ? 'https://' : 'http://';
	                        $iframe_url = $is_http . "maps.google.com/maps?q={$lat},{$lng}&z={$zoom}&output=embed";
	                        ?>
                            <div class="map-frame mt-30">
                                <iframe class="gmap_iframe h-100 w-100"
                                        id="candidate_gmap_iframe"
                                        src="<?php echo esc_url($iframe_url); ?>"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save', 'jobus' ); ?></button>
            </div>

        </form>

    </div>
</div>
