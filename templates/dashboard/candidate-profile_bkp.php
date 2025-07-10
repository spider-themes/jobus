<?php
/**
 * Template for the candidate profile page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current logged-in user object
$user = wp_get_current_user();
if ( ! $user || ! in_array( 'jobus_candidate', $user->roles, true ) ) {
	wp_die( esc_html__( 'Access denied. You must be a candidate to view this page.', 'jobus' ) );
}

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

// Include Sidebar Menu
include( 'candidate-templates/sidebar-menu.php' );
?>

<div class="dashboard-body">
    <div class="position-relative">
        <h2 class="main-title"><?php esc_html_e( 'My Profile', 'jobus' ); ?></h2>

        <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" id="candidate-profile-form" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="candidate_profile_form_submitted" value="1" />

            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3 rounded-3"><?php esc_html_e( 'Save', 'jobus' ); ?></button>
            </div>

        </form>


        <div class="bg-white card-box border-20" id="candidate-profile-description">

            <div class="user-avatar-setting d-flex align-items-center mb-30">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img"
                     id="candidate_avatar">
                <div class="upload-btn position-relative tran3s ms-4 me-3">
				    <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                    <input type="hidden" id="candidate_profile_picture_id" name="candidate_profile_picture_id" value="<?php echo esc_attr( get_user_meta($user->ID, 'candidate_profile_picture_id', true) ); ?>">
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




    </div>
</div>