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

$intro_video = $_POST['video_title'];
// Handle form submission
if ( isset($intro_video) ) {

	$meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
	if (!is_array($meta)) $meta = array();

	// Handle Portfolio form submission
	if (isset($_POST['portfolio_title']) || isset($_POST['portfolio'])) {
		if (!isset($meta) || !is_array($meta)) {
			$meta = array();
		}

		$has_changes = false;

		// Update portfolio title
		if (isset($_POST['portfolio_title'])) {
			$new_title = sanitize_text_field($_POST['portfolio_title']);
			if (!isset($meta['portfolio_title']) || $meta['portfolio_title'] !== $new_title) {
				$meta['portfolio_title'] = $new_title;
				$has_changes = true;
			}
		}

		// Update portfolio gallery
		if (isset($_POST['portfolio'])) {
			$portfolio_ids = array_filter(
				explode(',', sanitize_text_field($_POST['portfolio'])),
				function($id) { return is_numeric($id) && $id > 0; }
			);

			// Check if portfolio images have changed
			$existing_portfolio = isset($meta['portfolio']) ? (array)$meta['portfolio'] : array();
			if (array_diff($portfolio_ids, $existing_portfolio) || array_diff($existing_portfolio, $portfolio_ids)) {
				$meta['portfolio'] = $portfolio_ids;
				$has_changes = true;
			}
		}

		if ($has_changes) {
			$updated = update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
			if ($updated !== false) {
				$success_message = esc_html__('Portfolio updated successfully.', 'jobus');
			} else {
				$error_message = esc_html__('Failed to update portfolio.', 'jobus');
			}
		}
	}

	// Handle Category, Location, and Skill taxonomy save
	if (isset($_POST['candidate_categories'])) {
		$cat_ids = array_filter(array_map('intval', explode(',', sanitize_text_field($_POST['candidate_categories']))));
		wp_set_object_terms($candidate_id, $cat_ids, 'jobus_candidate_cat');
	}
	if (isset($_POST['candidate_locations'])) {
		$location_ids = array_filter(array_map('intval', explode(',', sanitize_text_field($_POST['candidate_locations']))));
		wp_set_object_terms($candidate_id, $location_ids, 'jobus_candidate_location');
	}
	if (isset($_POST['candidate_skills'])) {
		$skill_ids = array_filter(array_map('intval', explode(',', sanitize_text_field($_POST['candidate_skills']))));
		wp_set_object_terms($candidate_id, $skill_ids, 'jobus_candidate_skill');
	}
} // End of form submission check

//Sidebar Menu
include ('candidate-templates/sidebar-menu.php');
?>
<div class="dashboard-body">
	<div class="position-relative">

		<h2 class="main-title"><?php esc_html_e('My Resume', 'jobus'); ?></h2>

		<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" name="candidate-resume-form" id="candidate-resume-form" method="post" enctype="multipart/form-data">

			<div class="bg-white card-box border-20 mt-40" id="candidate-taxonomy">

				<!-- Add Categories -->
				<div class="dash-input-wrapper mb-40 mt-20">
					<label for="candidate-category-list"><?php esc_html_e('Categories', 'jobus'); ?></label>
					<div class="skills-wrapper">
						<?php
						$current_categories = array();
						if (isset($candidate_id) && $candidate_id) {
							$current_categories = wp_get_object_terms($candidate_id, 'jobus_candidate_cat');
						}
						?>
						<ul id="candidate-category-list" class="style-none d-flex flex-wrap align-items-center">
							<?php if (!empty($current_categories) && !is_wp_error($current_categories)): ?>
								<?php foreach ($current_categories as $cat): ?>
									<li class="is_tag" data-category-id="<?php echo esc_attr($cat->term_id); ?>">
										<button type="button"><?php echo esc_html($cat->name); ?> <i class="bi bi-x"></i></button>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
							<li class="more_tag"><button type="button">+</button></li>
						</ul>
						<input type="hidden" name="candidate_categories" id="candidate_categories_input" value="<?php echo !empty($current_categories) && !is_wp_error($current_categories) ? esc_attr(implode(',', wp_list_pluck($current_categories, 'term_id'))) : ''; ?>">
					</div>
				</div>

				<!-- Add Locations -->
				<div class="dash-input-wrapper mb-40 mt-20">
					<label for="candidate-location-list"><?php esc_html_e('Locations', 'jobus'); ?></label>
					<div class="skills-wrapper">
						<?php
						$current_locations = array();
						if (isset($candidate_id) && $candidate_id) {
							$current_locations = wp_get_object_terms($candidate_id, 'jobus_candidate_location');
						}
						?>
						<ul id="candidate-location-list" class="style-none d-flex flex-wrap align-items-center">
							<?php if (!empty($current_locations) && !is_wp_error($current_locations)): ?>
								<?php foreach ($current_locations as $loc): ?>
									<li class="is_tag" data-location-id="<?php echo esc_attr($loc->term_id); ?>">
										<button type="button"><?php echo esc_html($loc->name); ?> <i class="bi bi-x"></i></button>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
							<li class="more_tag"><button type="button">+</button></li>
						</ul>
						<input type="hidden" name="candidate_locations" id="candidate_locations_input" value="<?php echo !empty($current_locations) && !is_wp_error($current_locations) ? esc_attr(implode(',', wp_list_pluck($current_locations, 'term_id'))) : ''; ?>">
					</div>
				</div>

				<!-- Add Skills -->
				<div class="dash-input-wrapper mb-40 mt-20">
					<label for="candidate-skills-list"><?php esc_html_e('Add Skills*', 'jobus'); ?></label>
					<div class="skills-wrapper">
						<?php
						$current_skills = array();
						if ( isset( $candidate_id ) && $candidate_id ) {
							$current_skills = wp_get_object_terms($candidate_id, 'jobus_candidate_skill');
						}
						?>
						<ul id="candidate-skills-list" class="style-none d-flex flex-wrap align-items-center">
							<?php if (!empty($current_skills) && !is_wp_error($current_skills)): ?>
								<?php foreach ($current_skills as $skill): ?>
									<li class="is_tag" data-skill-id="<?php echo esc_attr($skill->term_id); ?>">
										<button type="button"><?php echo esc_html($skill->name); ?> <i class="bi bi-x"></i></button>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
							<li class="more_tag"><button type="button">+</button></li>
						</ul>
						<input type="hidden" name="candidate_skills" id="candidate_skills_input" value="<?php echo !empty($current_skills) && !is_wp_error($current_skills) ? esc_attr(implode(',', wp_list_pluck($current_skills, 'term_id'))) : ''; ?>">
					</div>
				</div>
			</div>

			<div class="bg-white card-box border-20 mt-40" id="portfolio-section">
				<h4 class="dash-title-three"><?php esc_html_e('Portfolio Gallery', 'jobus'); ?></h4>
				<div class="dash-input-wrapper mb-30">
					<label for="portfolio_title"><?php esc_html_e('Portfolio Title', 'jobus'); ?></label>
					<input type="text"
					       id="portfolio_title"
					       name="portfolio_title"
					       value="<?php echo esc_attr($meta['portfolio_title'] ?? ''); ?>"
					       class="form-control"
					       placeholder="<?php esc_attr_e('My Portfolio', 'jobus'); ?>">
				</div>

				<div class="row" id="portfolio-items">
					<?php
					$portfolio_ids = isset($meta['portfolio']) ? (array)$meta['portfolio'] : array();

					foreach($portfolio_ids as $image_id) :
						$image_url = wp_get_attachment_image_url($image_id);
						if ($image_url):
							?>
							<div class="col-lg-3 col-md-4 col-6 portfolio-item mb-30" data-id="<?php echo esc_attr($image_id); ?>">
								<div class="portfolio-image-wrapper position-relative">
									<img src="<?php echo esc_url($image_url); ?>" class="img-fluid" alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>">
									<button type="button" class="remove-portfolio-image btn-close position-absolute" aria-label="<?php esc_attr_e('Remove', 'jobus'); ?>"></button>
								</div>
							</div>
						<?php
						endif;
					endforeach;
					?>
				</div>

				<input type="hidden" name="portfolio_ids" id="portfolio_ids" value="<?php echo esc_attr(implode(',', $portfolio_ids)); ?>">
				<button type="button" id="add-portfolio-images" class="dash-btn-one mt-3">
					<i class="bi bi-plus"></i> <?php esc_html_e('Add Portfolio Images', 'jobus'); ?>
				</button>
			</div>

			<div class="button-group d-inline-flex align-items-center mt-30">
				<button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e('Save', 'jobus'); ?></button>
			</div>
		</form>

	</div>
</div>
