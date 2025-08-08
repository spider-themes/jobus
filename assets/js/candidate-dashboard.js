/**
 * Jobus - Frontend AJAX Controller
 *
 * Handles all public-facing AJAX actions used across the Jobus plugin.
 * This script manages the following user interactions via AJAX:
 *
 * It ensures secure data transmission using nonce verification and utilizes
 * WordPress's built-in admin-ajax.php endpoint.
 *
 * @summary   Modular JS controller for handling public AJAX actions
 * @author    spider-themes
 * @since     1.0.0
 * @package   Jobus
 * @license   GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://developer.wordpress.org/plugins/javascript/ajax/
 */

;(function ($) {
    'use strict';

    const JobusCandidateDashboard = {

        init: function () {
            this.updateProfilePicturePreview();
            this.SocialLinksRepeater();
            this.CandidateSpecificationsRepeater();
            this.EducationRepeater();
            this.ExperienceRepeater();
            this.VideoBgImage();

            this.CandidateTaxonomyManager({
                listSelector: '#candidate-category-list',
                inputSelector: '#candidate_categories_input',
                taxonomy: 'jobus_candidate_cat',
                dataAttr: 'category-id'
            });
            this.CandidateTaxonomyManager({
                listSelector: '#candidate-location-list',
                inputSelector: '#candidate_locations_input',
                taxonomy: 'jobus_candidate_location',
                dataAttr: 'location-id'
            });
            this.CandidateTaxonomyManager({
                listSelector: '#candidate-skills-list',
                inputSelector: '#candidate_skills_input',
                taxonomy: 'jobus_candidate_skill',
                dataAttr: 'skill-id'
            });

            this.CandidatePassword();
            this.checkPasswordRedirect();
        },

        /**
         * Updates the profile picture preview and handles media library selection.
         * This function initializes the media uploader and sets up the
         */
        updateProfilePicturePreview: function () {
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const mediaBtn = $('#candidate_profile_picture_upload');
            const hiddenId = $('#candidate_profile_picture_id');
            const originalAvatarUrl = imgPreview.attr('src');  // Store the original avatar URL
            let tempImageUrl = null;  // Store temporary image URL for preview
            const defaultAvatarUrl = jobus_dashboard_params && jobus_dashboard_params.default_avatar_url ? 
                jobus_dashboard_params.default_avatar_url : 
                'https://secure.gravatar.com/avatar/?s=96&d=mm&r=g'; // Fallback to WordPress default avatar

            // Ensure the media library is functional
            if (!mediaBtn.length || !window.wp || !window.wp.media) return;

            // Open media library on button click
            mediaBtn.on('click', function(e) {
                e.preventDefault();
                let mediaUploader = wp.media({
                    title: 'Select Profile Picture',
                    button: { text: 'Use this image' },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    tempImageUrl = attachment.url;  // Store temporary URL
                    imgPreview.attr('src', tempImageUrl);  // Update preview
                    hiddenId.val(attachment.id);  // Store the new image ID
                    profilePictureAction.val('upload');  // Mark the action as 'upload'
                });

                mediaUploader.open();
            });

            // Delete the avatar (revert to default avatar)
            $('#delete_profile_picture').on('click', function(e) {
                e.preventDefault();
                
                // Set the image to default avatar or original avatar if available
                imgPreview.attr('src', defaultAvatarUrl);
                
                // Clear the image ID
                hiddenId.val('');
                
                // Mark the action as 'delete'
                profilePictureAction.val('delete');
                
                // Clear temporary URL
                tempImageUrl = null;
            });

            // Handle form reset/cancel - revert to original image
            $('#candidate-profile-form').on('reset', function() {
                if (tempImageUrl) {
                    imgPreview.attr('src', originalAvatarUrl);
                    hiddenId.val('');
                    profilePictureAction.val('');
                    tempImageUrl = null;
                }
            });
        },


        /**
         * Handles background image selection using the WordPress media uploader.
         * Allows users to select an image from the media library and set it as background.
         *
         * @function VideoBgImage
         * @returns {void}
         */
        VideoBgImage: function () {
            const uploadBtn = $('#video_bg_img_upload_btn'); // This is the button we want to target
            const preview = $('#bg-img-preview');
            const previewFilename = $('#video-bg-image-uploaded-filename');
            const uploadBtnWrapper = $('#bg-img-upload-btn-wrapper');
            const imgIdInput = $('#video_bg_img_id');
            const imgUrlInput = $('#video_bg_img_url');
            const actionInput = $('#video_bg_img_action');
            const removeBtn = $('#remove-uploaded-bg-img');

            if (!window.wp || !window.wp.media) return;

            // Handle remove button click
            removeBtn.on('click', function(e) {
                e.preventDefault();

                // Set action to delete
                actionInput.val('delete');

                // Clear hidden fields
                imgIdInput.val('');
                imgUrlInput.val('');

                // Clear the filename
                previewFilename.text('');

                // Hide preview and show upload button
                preview.addClass('hidden');
                uploadBtnWrapper.removeClass('hidden');
            });

            // Handle WordPress media uploader
            uploadBtn.on('click', function(e) {
                e.preventDefault();

                const mediaUploader = wp.media({
                    title: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.bg_upload_title || 'Select Background Image',
                    button: {
                        text: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.bg_select_text || 'Use this image'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });

                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();

                    // Set action to upload
                    actionInput.val('upload');

                    // Update hidden fields
                    imgIdInput.val(attachment.id);
                    imgUrlInput.val(attachment.url);

                    // Update the filename in preview
                    previewFilename.text(attachment.url);

                    // Show preview, hide upload button
                    preview.removeClass('hidden');
                    uploadBtnWrapper.addClass('hidden');
                });

                mediaUploader.open();
            });
        },


        /**
         * Manages the dynamic addition and removal of social media links in the candidate dashboard form.
         * Handles UI updates and re-indexing of social link items.
         *
         * @function SocialLinksRepeater
         * @returns {void}
         */
        SocialLinksRepeater: function () {
            const icons = [
                { value: 'bi bi-facebook', label: 'Facebook' },
                { value: 'bi bi-instagram', label: 'Instagram' },
                { value: 'bi bi-twitter', label: 'Twitter' },
                { value: 'bi bi-linkedin', label: 'LinkedIn' },
                { value: 'bi bi-github', label: 'GitHub' },
                { value: 'bi bi-youtube', label: 'YouTube' },
                { value: 'bi bi-dribbble', label: 'Dribbble' },
                { value: 'bi bi-behance', label: 'Behance' },
                { value: 'bi bi-pinterest', label: 'Pinterest' },
                { value: 'bi bi-tiktok', label: 'TikTok' }
            ];

            const iconOptions = icons.map(icon =>
                `<option value="${icon.value}">${icon.label}</option>`
            ).join('');

            const repeater = $('#social-links-repeater');
            let index = repeater.children('.social-link-item').length;

            $('#add-social-link').on('click', function (e) {
                e.preventDefault();

                const accordionId = `social-link-${index}`;
                const newItem = $(`
                    <div class="accordion-item social-link-item">
                        <div class="accordion-header" id="heading-${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Social Network #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="heading-${index}"
                             data-bs-parent="#social-links-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_${index}_icon">Icon</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <select name="social_icons[${index}][icon]" id="social_${index}_icon" class="nice-select">
                                                ${iconOptions}
                                            </select>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_${index}_url">URL</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="social_icons[${index}][url]" id="social_${index}_url" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
        
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-social-link mt-2 mb-2" title="Remove Item">
                                        <i class="bi bi-x"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                repeater.append(newItem);
                index++;
            });

            repeater.on('click', '.remove-social-link', function () {
                $(this).closest('.social-link-item').remove();

                // Re-index all fields
                repeater.children('.social-link-item').each(function (i, el) {
                    const accordionId = `social-link-${i}`;
                    const $el = $(el);

                    $el.find('.accordion-header').attr('id', `heading-${i}`);
                    $el.find('.accordion-button')
                        .attr('data-bs-target', `#${accordionId}`)
                        .attr('aria-controls', accordionId)
                        .text(`Social Network #${i + 1}`);

                    $el.find('.accordion-collapse')
                        .attr('id', accordionId)
                        .attr('aria-labelledby', `heading-${i}`);

                    $el.find('select').attr('name', `social_icons[${i}][icon]`).attr('id', `social_${i}_icon`);
                    $el.find('input[name$="[title]"]').attr('name', `social_icons[${i}][title]`).attr('id', `social_${i}_title`);
                    $el.find('input[name$="[url]"]').attr('name', `social_icons[${i}][url]`).attr('id', `social_${i}_url`);
                });

                index = repeater.children('.social-link-item').length;
            });
        },


        /**
         * Handles the dynamic addition and removal of candidate specification fields.
         * Updates the DOM and manages event listeners for specification items.
         *
         * @function CandidateSpecificationsRepeater
         * @returns {void}
         */
        CandidateSpecificationsRepeater: function () {
            let repeater = document.getElementById('specifications-repeater');
            let addBtn = document.getElementById('add-specification');
            if (repeater && addBtn) {
                addBtn.addEventListener('click', function() {
                    let idx = repeater.querySelectorAll('.specification-item').length;
                    let div = document.createElement('div');
                    div.className = 'dash-input-wrapper mb-20 specification-item d-flex align-items-center gap-2';
                    div.innerHTML = '<input type="text" name="candidate_specifications['+idx+'][title]" class="form-control me-2" placeholder="Title" style="min-width:180px">' +
                        '<input type="text" name="candidate_specifications['+idx+'][value]" class="form-control me-2" placeholder="Value" style="min-width:180px">' +
                        '<button type="button" class="btn btn-danger remove-specification" title="Remove"><i class="bi bi-x"></i></button>';
                    repeater.appendChild(div);
                });
                repeater.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-specification')) {
                        e.target.closest('.specification-item').remove();
                    }
                });
            }
        },


        /**
         * Manages the education section repeater in the candidate dashboard.
         * Handles adding, removing, validating, and auto-saving education items.
         *
         * @function EducationRepeater
         * @returns {void}
         */
        EducationRepeater: function () {
            const repeater = $('#education-repeater');
            const addBtn = $('#add-education');
            let index = repeater.children('.education-item').length;

            addBtn.on('click', function (e) {
                e.preventDefault();
                const accordionId = `education-${index}`;
                const newItem = $(`
                    <div class="accordion-item education-item">
                        <div class="accordion-header" id="heading-${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Education #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="heading-${index}"
                             data-bs-parent="#education-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_sl_num">Serial Number</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][sl_num]" 
                                                   id="education_${index}_sl_num" 
                                                   class="form-control"
                                                   placeholder="Enter serial number" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_title">Title</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][title]" 
                                                   id="education_${index}_title" 
                                                   class="form-control"
                                                   placeholder="Enter education title" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_academy">Academy</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][academy]" 
                                                   id="education_${index}_academy" 
                                                   class="form-control"
                                                   placeholder="Enter academy name" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_description">Description</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <textarea name="education[${index}][description]" 
                                                      id="education_${index}_description" 
                                                      class="form-control"
                                                      placeholder="Enter description" 
                                                      rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-education mt-2 mb-2" title="Remove Item">
                                        <i class="bi bi-x"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                repeater.append(newItem);
                index++;
            });

            repeater.on('click', '.remove-education', function () {
                $(this).closest('.education-item').remove();

                // Re-index all fields
                repeater.children('.education-item').each(function (i) {
                    const accordionId = `education-${i}`;
                    const $el = $(this);

                    $el.find('.accordion-header').attr('id', `heading-${i}`);
                    $el.find('.accordion-button')
                        .attr('data-bs-target', `#${accordionId}`)
                        .attr('aria-controls', accordionId)
                        .text(`Education #${i + 1}`);

                    $el.find('.accordion-collapse')
                        .attr('id', accordionId)
                        .attr('aria-labelledby', `heading-${i}`);

                    $el.find('input, textarea').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/education\[\d+\]/, `education[${i}]`);
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/education_\d+_/, `education_${i}_`);
                            $(this).attr('id', id);
                            // Update associated label
                            $el.find(`label[for^="education_"]`).attr('for', id);
                        }
                    });
                });

                index = repeater.children('.education-item').length;
            });
        },


        /**
         * Manages the experience section repeater in the candidate dashboard.
         * Handles adding, removing, validating, and auto-saving experience items.
         *
         * @function ExperienceRepeater
         * @returns {void}
         */
        ExperienceRepeater: function () {
            const repeater = $('#experience-repeater');
            const addBtn = $('#add-experience');
            let index = repeater.children('.experience-item').length;

            addBtn.on('click', function (e) {
                e.preventDefault();
                const accordionId = `experience-${index}`;
                const newItem = $(`
                    <div class="accordion-item experience-item">
                        <div class="accordion-header" id="headingExp-${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Experience #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="headingExp-${index}"
                             data-bs-parent="#experience-repeater">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_sl_num">Serial Number</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control" 
                                                   name="experience[${index}][sl_num]" 
                                                   id="experience_${index}_sl_num"
                                                   placeholder="Enter serial number">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_title">Title</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="experience[${index}][title]"
                                                   id="experience_${index}_title"
                                                   placeholder="Enter experience title">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label>Duration</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="form-control"
                                                           name="experience[${index}][start_date]"
                                                           id="experience_${index}_start_date">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="form-control"
                                                           name="experience[${index}][end_date]"
                                                           id="experience_${index}_end_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_description">Description</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="form-control"
                                                      name="experience[${index}][description]"
                                                      id="experience_${index}_description"
                                                      placeholder="Enter description"
                                                      rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-experience mt-2 mb-2" title="Remove Item">
                                        <i class="bi bi-x"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                repeater.append(newItem);
                index++;
            });

            repeater.on('click', '.remove-experience', function () {
                $(this).closest('.experience-item').remove();

                // Re-index all fields
                repeater.children('.experience-item').each(function (i) {
                    const accordionId = `experience-${i}`;
                    const $el = $(this);

                    $el.find('.accordion-header').attr('id', `headingExp-${i}`);
                    $el.find('.accordion-button')
                        .attr('data-bs-target', `#${accordionId}`)
                        .attr('aria-controls', accordionId)
                        .text(`Experience #${i + 1}`);

                    $el.find('.accordion-collapse')
                        .attr('id', accordionId)
                        .attr('aria-labelledby', `headingExp-${i}`);

                    $el.find('input, textarea').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/experience\[\d+\]/, `experience[${i}]`);
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/experience_\d+_/, `experience_${i}_`);
                            $(this).attr('id', id);
                            // Update associated label
                            $el.find(`label[for^="experience_"]`).attr('for', id);
                        }
                    });
                });

                index = repeater.children('.experience-item').length;
            });
        },


        /**
         * Handles candidate taxonomy management (categories, locations, skills) in the dashboard.
         * Provides autocomplete, creation, and removal of taxonomy terms using AJAX and UI updates.
         *
         * @function CandidateTaxonomyManager
         * @param {Object} taxonomy - Configuration object for the taxonomy manager.
         * @param {string} taxonomy.listSelector - Selector for the taxonomy list container.
         * @param {string} taxonomy.inputSelector - Selector for the hidden input storing selected term IDs.
         * @param {string} taxonomy.taxonomy - The taxonomy name (e.g., 'jobus_candidate_cat').
         * @param {string} taxonomy.dataAttr - The data attribute used for term IDs (e.g., 'category-id').
         * @returns {void}
         */
        CandidateTaxonomyManager: function (taxonomy) {
            const $list = $(taxonomy.listSelector);
            const $input = $(taxonomy.inputSelector);
            let $inputWrapper = $list.find('.taxonomy-input-wrapper');
            // Track new terms that haven't been saved to database yet
            const tempTerms = [];
            let tempTermCounter = -1; // Use negative IDs for temporary terms

            if (!jobus_dashboard_params || !jobus_dashboard_params.ajax_url) {
                console.error('Dashboard parameters not properly initialized');
                return;
            }

            if ($inputWrapper.length === 0) {
                $inputWrapper = $(`
                    <li class="taxonomy-input-wrapper" style="display:none;">
                        <input type="text" class="taxonomy-input" placeholder="Type and press Enter to add">
                        <ul class="taxonomy-suggestions dropdown-menu"></ul>
                    </li>
                `);
                $list.find('.more_tag').before($inputWrapper);
            }

            const $textInput = $inputWrapper.find('input');
            const $suggestions = $inputWrapper.find('.taxonomy-suggestions');

            // Show input on plus button click
            $list.on('click', '.more_tag button', function (e) {
                e.preventDefault();
                $inputWrapper.show();
                $textInput.val('').focus();
                $suggestions.hide().empty();
            });

            // Remove tag on click
            $list.on('click', '.is_tag button', function (e) {
                e.preventDefault();
                const $tag = $(this).closest('.is_tag');
                const termId = $tag.data(taxonomy.dataAttr);

                // Remove the tag from UI
                $tag.fadeOut(200, function() {
                    $(this).remove();

                    // Update hidden input value by getting all remaining term IDs
                    const remainingIds = [];
                    $list.find('.is_tag').each(function() {
                        remainingIds.push($(this).data(taxonomy.dataAttr));
                    });
                    $input.val(remainingIds.join(','));
                });
            });

            // Handle form submission
            const $form = $list.closest('form');
            $form.on('submit', function() {
                // Update hidden input one final time before submission
                const finalIds = [];
                const finalTerms = [];

                $list.find('.is_tag').each(function() {
                    const $tag = $(this);
                    const id = $tag.data(taxonomy.dataAttr);
                    const name = $tag.find('button').text().trim().replace(' ×', '').replace(/\s*<i.*<\/i>\s*$/, '');

                    finalIds.push(id);

                    // If this is a temporary term (negative ID) or a newly created term that hasn't been processed yet
                    if (id < 0 || tempTerms.includes(id)) {
                        finalTerms.push({
                            id: id,
                            name: name
                        });
                    }
                });

                // Store both IDs and new terms data
                if (finalIds.length > 0) {
                    $input.val(finalIds.join(','));
                } else {
                    $input.val(''); // Ensure empty value if no terms
                }

                // Add a hidden field with new term data if there are any
                if (finalTerms.length > 0) {
                    const termsFieldName = $input.attr('name') + '_new_terms';
                    // Remove any existing field with this name to prevent duplicates
                    $form.find(`input[name="${termsFieldName}"]`).remove();

                    // Add new hidden field with JSON data of new terms
                    const hiddenField = $('<input>').attr({
                        type: 'hidden',
                        name: termsFieldName,
                        value: JSON.stringify(finalTerms)
                    });
                    $form.append(hiddenField);
                }
            });

            // Handle input for suggestions
            $textInput.on('input', function () {
                const query = $textInput.val().trim();
                if (query.length < 1) {
                    $suggestions.hide().empty();
                    return;
                }

                $.ajax({
                    url: jobus_dashboard_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_suggest_taxonomy_terms',
                        security: jobus_dashboard_params.suggest_taxonomy_nonce,
                        taxonomy: taxonomy.taxonomy,
                        term_query: query
                    },
                    beforeSend: function() {
                        $textInput.addClass('loading');
                    },
                    success: function (response) {
                        if (response.success && response.data && response.data.length) {
                            $suggestions.empty();
                            response.data.forEach(function (term) {
                                $suggestions.append(`<li class="dropdown-item" data-term-id="${term.term_id}">${term.name}</li>`);
                            });
                            // Add "Create new" option if no exact match
                            const exactMatch = response.data.some(term => term.name.toLowerCase() === query.toLowerCase());
                            if (!exactMatch && query.length > 0) {
                                $suggestions.append(`<li class="dropdown-item create-new-term" data-term-name="${query}"><strong>Create:</strong> "${query}"</li>`);
                            }
                            $suggestions.show();
                        } else {
                            $suggestions.empty();
                            // If no results, show create option
                            if (query.length > 0) {
                                $suggestions.append(`<li class="dropdown-item create-new-term" data-term-name="${query}"><strong>Create:</strong> "${query}"</li>`);
                                $suggestions.show();
                            } else {
                                $suggestions.hide();
                            }
                        }
                    },
                    error: function () {
                        console.error(jobus_dashboard_params.texts.taxonomy_suggest_error);
                        $suggestions.hide().empty();
                    },
                    complete: function() {
                        $textInput.removeClass('loading');
                    }
                });
            });

            // Handle suggestion click (both existing terms and create new)
            $suggestions.on('click', 'li', function () {
                const $item = $(this);

                if ($item.hasClass('create-new-term')) {
                    // Handle creating a new term
                    const termName = $item.data('term-name');
                    createTerm(termName);
                } else {
                    // Handle selecting an existing term
                    const termId = $item.data('term-id');
                    const termName = $item.text();
                    addTerm(termId, termName);
                }
            });

            // Handle term creation on Enter
            $textInput.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const termName = $textInput.val().trim();
                    if (!termName) return;

                    // Check if there's a visible suggestion that's an exact match
                    const $exactMatch = $suggestions.find('li:not(.create-new-term)').filter(function() {
                        return $(this).text().toLowerCase() === termName.toLowerCase();
                    });

                    if ($exactMatch.length) {
                        // Use the existing term if there's an exact match
                        const termId = $exactMatch.data('term-id');
                        addTerm(termId, $exactMatch.text());
                    } else {
                        // Otherwise create a new term
                        createTerm(termName);
                    }
                }
                else if (e.key === 'Escape') {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }
            });

            // Handle input blur
            $textInput.on('blur', function () {
                setTimeout(function () {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }, 150);
            });

            // Helper function to create a term via AJAX
            function createTerm(termName) {
                // First check if we already have this term in the list
                let alreadyExists = false;
                $list.find('.is_tag').each(function() {
                    const existingName = $(this).find('button').text().trim().replace(' ×', '').replace(/\s*<i.*<\/i>\s*$/, '');
                    if (existingName.toLowerCase() === termName.toLowerCase()) {
                        alreadyExists = true;
                        return false; // break the loop
                    }
                });

                if (alreadyExists) {
                    $textInput.val('');
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                    return;
                }

                // Try to create the term via AJAX
                $.ajax({
                    url: jobus_dashboard_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_create_taxonomy_term',
                        security: jobus_dashboard_params.create_taxonomy_nonce,
                        taxonomy: taxonomy.taxonomy,
                        term_name: termName
                    },
                    beforeSend: function() {
                        $textInput.addClass('loading');
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Add the newly created term
                            const termId = response.data.term_id;
                            addTerm(termId, response.data.term_name);
                            // Add to tempTerms array to track it for form submission
                            tempTerms.push(termId);
                        } else {
                            // If server-side creation fails, fall back to temporary ID
                            tempTermCounter--;
                            addTerm(tempTermCounter, termName);
                            console.error('Term creation error:', response.data ? response.data.message : 'Unknown error');
                        }
                    },
                    error: function() {
                        // On error, use a temporary ID
                        tempTermCounter--;
                        addTerm(tempTermCounter, termName);
                        console.error(jobus_dashboard_params.texts.taxonomy_create_error);
                    },
                    complete: function() {
                        $textInput.removeClass('loading');
                    }
                });
            }

            // Helper function to add a term to the list
            function addTerm(termId, termName) {
                // First check if we already have this term ID in the list
                let alreadyExists = false;
                $list.find('.is_tag').each(function() {
                    if ($(this).data(taxonomy.dataAttr) == termId) {
                        alreadyExists = true;
                        return false; // break the loop
                    }
                });

                if (alreadyExists) {
                    $textInput.val('');
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                    return;
                }

                const newTag = $(`
                    <li class="is_tag" data-${taxonomy.dataAttr}="${termId}">
                        <button type="button">${termName} <i class="bi bi-x"></i></button>
                    </li>
                `);
                $list.find('.more_tag').before(newTag);

                let ids = $input.val() ? $input.val().split(',') : [];
                if (!ids.includes(termId.toString())) {
                    ids.push(termId);
                }
                $input.val(ids.join(','));

                $textInput.val('');
                $inputWrapper.hide();
                $suggestions.hide().empty();
            }
        },


        /**
         * Handles candidate password management in the dashboard.
         * Provides functionality for checking password strength, matching new passwords,
         * showing/hiding password fields, and updating the UI accordingly.
         * @function CandidatePassword
         * @constructor
         */
        CandidatePassword:function () {
            const $form = $('#candidate-password-form');
            const $currentPassword = $('#current_password');
            const $newPassword = $('#new_password');
            const $confirmPassword = $('#confirm_password');
            const $passwordStrength = $('#password-strength');
            const $passwordMatchStatus = $('#password-match-status');

            // Check password strength
            function checkPasswordStrength() {
                const password = $newPassword.val().trim();
                if (!password) {
                    $passwordStrength.removeClass('text-success text-warning text-danger').empty();
                    return;
                }

                // Simple password strength indicator
                let strength = 0;
                if (password.length >= 8) strength += 1;
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[a-z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;

                // Display strength indicator
                if (strength < 3) {
                    $passwordStrength.removeClass('text-success text-warning').addClass('text-danger')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_weak || 'Weak password');
                } else if (strength < 5) {
                    $passwordStrength.removeClass('text-success text-danger').addClass('text-warning')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_medium || 'Medium strength password');
                } else {
                    $passwordStrength.removeClass('text-warning text-danger').addClass('text-success')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_strong || 'Strong password');
                }
            }

            // Check if passwords match
            function checkPasswordsMatch() {
                const newPass = $newPassword.val().trim();
                const confirmPass = $confirmPassword.val().trim();

                if (!confirmPass) {
                    $passwordMatchStatus.removeClass('text-success text-danger').empty();
                    return;
                }

                if (newPass === confirmPass) {
                    $passwordMatchStatus.removeClass('text-danger').addClass('text-success')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.passwords_match || 'Passwords match');
                } else {
                    $passwordMatchStatus.removeClass('text-success').addClass('text-danger')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.passwords_mismatch || 'Passwords do not match');
                }
            }

            // Show/hide password toggle
            $form.find('.passVicon').on('click', function() {
                const $input = $(this).closest('.dash-input-wrapper').find('input');
                const type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
            });

            // Attach input event listeners
            $currentPassword.on('input', checkPasswordsMatch);
            $newPassword.on('input', function() {
                checkPasswordStrength();
                checkPasswordsMatch();
            });
            $confirmPassword.on('input', function() {
                checkPasswordsMatch();
            });
        },

        /**
         * Checks for password change success and triggers redirect if needed
         */
        checkPasswordRedirect: function() {

            let $passwordChange = $('#password-change-success');
            if ( $passwordChange.length ) {
                const $redirectUrl = $passwordChange.data('redirect-url');
                if ($redirectUrl) {
                    setTimeout(function() {
                        window.location.href = $redirectUrl;
                    }, 1000);
                }
            }
        },

    };

    $(document).ready(function () {
        JobusCandidateDashboard.init();
    });

})(jQuery);


document.addEventListener('DOMContentLoaded', function() {

    /**
     * Enhanced CV upload functionality for the candidate resume form
     * Handles file selection, preview display, and removal
     */
    function cvUploadHandler() {
        let preview = document.getElementById('cv-upload-preview');
        let filenameSpan = document.getElementById('cv-uploaded-filename');
        let removeBtn = document.getElementById('remove-uploaded-cv');
        let uploadBtnWrapper = document.getElementById('cv-upload-btn-wrapper');
        let fileInfo = document.getElementById('cv-file-info');
        let cvActionField = document.getElementById('profile_cv_action');
        let uploadBtn = document.getElementById('upload_cv_button');
        let cvAttachmentId = document.getElementById('cv_attachment_id');

        if (uploadBtn && window.wp && window.wp.media) {
            // Media library integration
            uploadBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Create the media frame
                const mediaUploader = wp.media({
                    title: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.cv_upload_title || 'Select CV Document',
                    button: {
                        text: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.cv_select_text || 'Use this document'
                    },
                    library: {
                        type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                    },
                    multiple: false
                });

                // When a file is selected
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();

                    // Update hidden field with attachment ID
                    if (cvAttachmentId) {
                        cvAttachmentId.value = attachment.id;
                    }

                    // Update the display
                    if (filenameSpan) {
                        filenameSpan.textContent = attachment.title || attachment.filename;
                    }

                    // Show preview, hide upload button
                    if (preview) {
                        preview.classList.remove('hidden');
                    }
                    if (uploadBtnWrapper) {
                        uploadBtnWrapper.classList.add('hidden');
                    }
                    if (fileInfo) {
                        fileInfo.classList.add('hidden');
                    }

                    // Set action to upload for form processing
                    if (cvActionField) {
                        cvActionField.value = 'upload';
                    }
                });

                // Open the media library dialog
                mediaUploader.open();
            });
        }

        // File removal handler
        if(removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Reset file input and ID field
                if(cvAttachmentId) cvAttachmentId.value = '';

                // Hide preview and show upload button
                if(preview) preview.classList.add('hidden');
                if(uploadBtnWrapper) uploadBtnWrapper.classList.remove('hidden');
                if(fileInfo) fileInfo.classList.remove('hidden');

                // Set the action to delete for form processing
                if(cvActionField) cvActionField.value = 'delete';
            });
        }
    }


    /**
     * Portfolio Gallery functionality for the candidate dashboard
     * Handles image uploads, selection, and removal for candidate portfolios
     */
    function PortfolioGallery() {
        // Basic DOM elements
        const portfolioContainer = document.getElementById('portfolio-items');
        const portfolioIdsField = document.getElementById('portfolio_ids');
        const addButton = document.getElementById('add-portfolio-images');
        let buttonsContainer;

        // Exit if required elements don't exist
        if (!portfolioContainer || !portfolioIdsField || !addButton || !window.wp?.media) return;

        // Function to update button states based on gallery content
        function updateGalleryButtons() {
            const hasImages = portfolioIdsField.value.length > 0;

            // Create buttons container if it doesn't exist
            if (!buttonsContainer) {
                buttonsContainer = document.createElement('div');
                buttonsContainer.className = 'portfolio-buttons-container mt-3 d-flex gap-2';
                addButton.parentNode.insertBefore(buttonsContainer, addButton.nextSibling);
            }

            // Update buttons visibility
            if (hasImages) {
                // Show Edit and Clear buttons, hide Add Gallery
                addButton.style.display = 'none';

                // Clear the container and add the edit and clear buttons
                buttonsContainer.innerHTML = `
                    <button type="button" id="edit-portfolio-images" class="dash-btn-two">
                        <i class="bi bi-pencil"></i> ${jobus_dashboard_params?.texts?.edit_portfolio || 'Edit Gallery'}
                    </button>
                    <button type="button" id="clear-portfolio-images" class="dash-btn-danger">
                        <i class="bi bi-trash"></i> ${jobus_dashboard_params?.texts?.clear_portfolio || 'Clear'}
                    </button>
                `;

                // Add event listeners to the new buttons
                document.getElementById('edit-portfolio-images').addEventListener('click', openMediaGallery);
                document.getElementById('clear-portfolio-images').addEventListener('click', clearGallery);
            } else {
                // Show Add Gallery, hide Edit and Clear buttons
                addButton.style.display = '';
                buttonsContainer.innerHTML = '';
            }
        }

        // Function to clear the gallery
        function clearGallery(e) {
            if (e) e.preventDefault();

            // Confirm before clearing
            if (confirm(jobus_dashboard_params?.texts?.confirm_clear_gallery || 'Are you sure you want to clear the entire gallery?')) {
                // Clear the hidden field
                portfolioIdsField.value = '';

                // Clear the gallery container
                const listContainer = portfolioContainer.querySelector('ul.portfolio-image-list');
                if (listContainer) {
                    listContainer.innerHTML = '';
                }

                // Update buttons
                updateGalleryButtons();
            }
        }

        // Function to open the media gallery for editing
        function openMediaGallery(e) {
            if (e) e.preventDefault();

            // Create the media frame with gallery view
            const frame = wp.media({
                frame: 'post',  // Use post frame which includes gallery view
                state: 'gallery-edit',  // Start in the gallery edit state
                title: jobus_dashboard_params?.texts?.portfolio_upload_title || 'Edit Portfolio Images',
                button: {
                    text: jobus_dashboard_params?.texts?.portfolio_select_text || 'Update Gallery'
                },
                library: {
                    type: 'image'
                },
                multiple: true
            });

            // When media frame opens, pre-select existing images and set to GRID view
            frame.on('open', function() {
                // Set view to grid/gallery mode
                if (frame.content.get() !== null) {
                    frame.content.get().collection.props.set('display', 'grid');
                    frame.content.get().options.mode = 'grid';
                }

                // Get current selection
                const selection = frame.state().get('selection');

                // If we have existing image IDs, pre-select them
                if (portfolioIdsField.value) {
                    const ids = portfolioIdsField.value.split(',').filter(id => id.trim());

                    ids.forEach(function(id) {
                        const attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment);
                    });
                }
            });

            // When images are selected
            frame.on('select', function() {
                // Get selected images
                const selection = frame.state().get('selection').toJSON();

                // Show loading state
                const editButton = document.getElementById('edit-portfolio-images');
                if (editButton) {
                    editButton.classList.add('processing');
                    editButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                }

                // Clear previous selection
                portfolioIdsField.value = '';
                const listContainer = getOrCreateListContainer();
                listContainer.innerHTML = '';

                // Debug log to verify selection
                console.log('Selected images:', selection.length, selection);

                // If there are selected images
                if (selection.length > 0) {
                    const allIds = [];

                    // Process each selected image
                    selection.forEach(function(attachment) {
                        const id = attachment.id.toString();
                        allIds.push(id);

                        // Get the best available image size
                        const imageUrl = attachment.sizes?.thumbnail?.url ||
                                       attachment.sizes?.medium?.url ||
                                       attachment.url;

                        // Create list item with image
                        const listItem = document.createElement('li');
                        listItem.setAttribute('data-id', id);
                        
                        // Add image to the list item
                        listItem.innerHTML = `<img src="${imageUrl}" alt="${attachment.alt || attachment.title || ''}">`;
                        
                        // Append the list item to the container
                        listContainer.appendChild(listItem);
                    });

                    // Update the hidden field with all image IDs
                    portfolioIdsField.value = allIds.join(',');
                    
                    // Debug log to verify hidden field value
                    console.log('Portfolio IDs field value:', portfolioIdsField.value);

                    // Ensure the preview container is visible
                    portfolioContainer.style.display = 'block';
                    
                    // Make sure the parent preview wrapper is visible too
                    const previewWrapper = portfolioContainer.closest('.portfolio-preview-wrapper');
                    if (previewWrapper) {
                        previewWrapper.style.display = 'block';
                    }
                }

                // Reset the button and update gallery buttons
                if (editButton) {
                    editButton.classList.remove('processing');
                    editButton.innerHTML = `<i class="bi bi-pencil"></i> ${jobus_dashboard_params?.texts?.edit_portfolio || 'Edit Gallery'}`;
                }

                updateGalleryButtons();
            });

            // Open the media frame
            frame.open();
        }

        // Create or get the list container
        function getOrCreateListContainer() {
            // Check if the list container already exists
            let listContainer = portfolioContainer.querySelector('ul.portfolio-image-list');
            if (!listContainer) {
                // Create a list container if it doesn't exist
                listContainer = document.createElement('ul');
                listContainer.className = 'portfolio-image-list';
                portfolioContainer.innerHTML = ''; // Clear existing content
                portfolioContainer.appendChild(listContainer);
            }
            return listContainer;
        }

        // Add button click handler - now just calls openMediaGallery
        addButton.addEventListener('click', openMediaGallery);

        // Initialize the portfolio container and buttons
        function initializePortfolioContainer() {
            if (!portfolioIdsField.value) {
                updateGalleryButtons();
                return;
            }

            const ids = portfolioIdsField.value.split(',').filter(id => id.trim());
            if (ids.length === 0) {
                updateGalleryButtons();
                return;
            }

            // Get or create the list container
            const listContainer = getOrCreateListContainer();

            // For each ID, try to get the attachment and create a list item
            ids.forEach(id => {
                // Skip if this ID is already in the list
                if (listContainer.querySelector(`li[data-id="${id}"]`)) return;

                const attachment = wp.media.attachment(id);
                attachment.fetch({
                    success: function() {
                        const imageUrl = attachment.get('sizes')?.thumbnail?.url ||
                                        attachment.get('sizes')?.medium?.url ||
                                        attachment.get('url');

                        // Create list item without remove button
                        const listItem = document.createElement('li');
                        listItem.setAttribute('data-id', id);

                        // Add image to the list item
                        listItem.innerHTML = `<img src="${imageUrl}" alt="${attachment.get('alt') || attachment.get('title') || ''}">`;

                        // Append the list item to the container
                        listContainer.appendChild(listItem);

                        // Update buttons after adding images
                        updateGalleryButtons();
                    }
                });
            });

            // Ensure buttons are updated even if images don't load
            updateGalleryButtons();
        }

        // Initialize on page load
        if (window.wp?.media) {
            initializePortfolioContainer();
        }
    }

    // Initialize handlers
    cvUploadHandler();
    PortfolioGallery();
});
