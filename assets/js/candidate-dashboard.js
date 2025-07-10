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
            this.PortfolioManager().init();
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
        },

        /**
         * Updates the profile picture preview and handles media library selection.
         * This function initializes the media uploader and sets up the
         */
        updateProfilePicturePreview: function () {
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const mediaBtn = $('#open_media_library');
            const hiddenId = $('#candidate_profile_picture_id');
            const originalAvatarUrl = imgPreview.attr('src');  // Store the original avatar URL
            let tempImageUrl = null;  // Store temporary image URL for preview

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

            // Delete the avatar (revert to original avatar)
            $('#delete_profile_picture').on('click', function(e) {
                e.preventDefault();
                imgPreview.attr('src', originalAvatarUrl);  // Revert to the original avatar
                hiddenId.val('');  // Clear the image ID
                profilePictureAction.val('delete');  // Mark the action as 'delete'
                tempImageUrl = null;  // Clear temporary URL
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
            const uploadBtn = $('#video_bg_img');
            const preview = $('#bg-img-preview');
            const previewImg = preview.find('img');
            const uploadBtnWrapper = $('#bg-img-upload-btn-wrapper');
            const fileInfo = $('#bg-img-file-info');
            const imgIdInput = $('#video_bg_img_id');
            const imgUrlInput = $('#video_bg_img_url');
            const removeBtn = $('#remove-uploaded-bg-img');

            if (!window.wp || !window.wp.media) return;

            // Handle file input change
            uploadBtn.on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Check if file is an image
                    if (!file.type.match('image.*')) {
                        alert('Please select an image file (.jpg, .jpeg, .png)');
                        return;
                    }

                    // Create a temporary URL for the file
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Show preview
                        previewImg.attr('src', e.target.result);
                        preview.removeClass('hidden');
                        uploadBtnWrapper.addClass('hidden');
                        fileInfo.addClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Handle remove button click
            removeBtn.on('click', function(e) {
                e.preventDefault();

                // Clear file input and hidden fields
                uploadBtn.val('');
                imgIdInput.val('');
                imgUrlInput.val('');

                // Reset UI
                preview.addClass('hidden');
                uploadBtnWrapper.removeClass('hidden');
                fileInfo.removeClass('hidden');
                previewImg.attr('src', '');
            });

            // Handle WordPress media uploader
            uploadBtn.on('click', function(e) {
                e.preventDefault();

                const mediaUploader = wp.media({
                    title: 'Select Background Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();

                    // Update hidden fields
                    imgIdInput.val(attachment.id);
                    imgUrlInput.val(attachment.url);

                    // Update preview
                    previewImg.attr('src', attachment.url);
                    preview.removeClass('hidden');
                    uploadBtnWrapper.addClass('hidden');
                    fileInfo.addClass('hidden');
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
                                            <label for="experience_${index}_company">Company</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="experience[${index}][company]"
                                                   id="experience_${index}_company"
                                                   placeholder="Enter company name">
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
         * Manages the portfolio section for candidates.
         * Handles image uploads via the WordPress media library, image removal, and UI updates.
         *
         * @function PortfolioManager
         * @returns {{init: function}} An object with an init method to initialize the portfolio manager.
         */
        PortfolioManager: function () {
            const portfolioSection = $('#portfolio-section');
            let mediaUploader;

            function setupMediaUploader() {
                $('#add-portfolio-images').on('click', function (e) {
                    e.preventDefault();

                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media({
                        title: window.jobus_portfolio_upload_title || 'Select Portfolio Images',
                        button: {
                            text: window.jobus_portfolio_select_text || 'Add to Portfolio'
                        },
                        multiple: true
                    });

                    mediaUploader.on('select', function () {
                        const selection = mediaUploader.state().get('selection');
                        const portfolioContainer = $('#portfolio-items');
                        const portfolioIds = [];

                        const existingIds = $('#portfolio_ids').val().split(',').filter(id => id);

                        selection.map(function (attachment) {
                            attachment = attachment.toJSON();
                            portfolioIds.push(attachment.id);

                            if (!existingIds.includes(attachment.id.toString())) {
                                portfolioContainer.append(`
                            <div class="col-lg-3 col-md-4 col-6 portfolio-item mb-30" data-id="${attachment.id}">
                                <div class="portfolio-image-wrapper position-relative">
                                    <img src="${attachment.sizes.thumbnail.url}" class="img-fluid" alt="${attachment.title}">
                                    <button type="button" class="remove-portfolio-image btn-close position-absolute" aria-label="Remove"></button>
                                </div>
                            </div>
                        `);
                            }
                        });

                        const allIds = [...new Set([...existingIds, ...portfolioIds])];
                        $('#portfolio_ids').val(allIds.join(','));
                    });

                    mediaUploader.open();
                });
            }

            function setupImageRemoval() {
                $(document).on('click', '.remove-portfolio-image', function (e) {
                    e.preventDefault();
                    const item = $(this).closest('.portfolio-item');
                    const imageId = item.data('id');

                    if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this image?')) {
                        const currentIds = $('#portfolio_ids').val().split(',').filter(id => id && id != imageId);
                        $('#portfolio_ids').val(currentIds.join(','));

                        item.fadeOut('fast', function () {
                            $(this).remove();
                        });
                    }
                });
            }

            function init() {
                if (portfolioSection.length) {
                    setupMediaUploader();
                    setupImageRemoval();
                }
            }

            return { init };
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

            $list.on('click', '.more_tag button', function (e) {
                e.preventDefault();
                $inputWrapper.show();
                $textInput.val('').focus();
                $suggestions.hide().empty();
            });

            $list.on('click', '.is_tag button', function (e) {
                e.preventDefault();
                const $tag = $(this).closest('.is_tag');
                const termId = $tag.data(taxonomy.dataAttr);
                $tag.remove();
                let ids = $input.val() ? $input.val().split(',') : [];
                ids = ids.filter(id => id != termId);
                $input.val(ids.join(','));
            });

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
                        taxonomy: taxonomy.taxonomy,
                        term_query: query
                    },
                    success: function (response) {
                        if (response.success && response.data && response.data.length) {
                            $suggestions.empty();
                            response.data.forEach(function (term) {
                                $suggestions.append(`<li class="dropdown-item" data-term-id="${term.term_id}">${term.name}</li>`);
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide().empty();
                        }
                    },
                    error: function () {
                        $suggestions.hide().empty();
                    }
                });
            });

            $suggestions.on('click', 'li', function () {
                const termId = $(this).data('term-id');
                const termName = $(this).text();
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
            });

            $textInput.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const termName = $textInput.val().trim();
                    if (!termName) return;
                    $textInput.prop('disabled', true).attr('placeholder', 'Creating...');
                    $.ajax({
                        url: jobus_dashboard_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'jobus_create_taxonomy_term',
                            term_name: termName,
                            taxonomy: taxonomy.taxonomy
                        },
                        success: function (response) {
                            if (response.success) {
                                const newTag = $(`
                            <li class="is_tag" data-${taxonomy.dataAttr}="${response.data.term_id}">
                                <button type="button">${response.data.term_name} <i class="bi bi-x"></i></button>
                            </li>
                        `);
                                $list.find('.more_tag').before(newTag);
                                let ids = $input.val() ? $input.val().split(',') : [];
                                ids.push(response.data.term_id);
                                $input.val(ids.join(','));
                                $textInput.val('').prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                                $inputWrapper.hide();
                                $suggestions.hide().empty();
                            } else {
                                alert(response.data?.message || 'Error creating term');
                                $textInput.prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                            }
                        },
                        error: function () {
                            alert('Server error. Please try again.');
                            $textInput.prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                        }
                    });
                } else if (e.key === 'Escape') {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }
            });

            $textInput.on('blur', function () {
                setTimeout(function () {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }, 150);
            });
        }

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
        let cvInput = document.getElementById('cv_attachment');
        let preview = document.getElementById('cv-upload-preview');
        let filenameSpan = document.getElementById('cv-uploaded-filename');
        let removeBtn = document.getElementById('remove-uploaded-cv');
        let uploadBtnWrapper = document.getElementById('cv-upload-btn-wrapper');
        let fileInfo = document.getElementById('cv-file-info');
        let cvActionField = document.getElementById('profile_cv_action');

        if (cvInput) {
            // File selection handler
            cvInput.addEventListener('change', function(e) {
                if (cvInput.files.length > 0) {
                    let file = cvInput.files[0];

                    // Update the filename display
                    filenameSpan.textContent = file.name;

                    // Show preview section and hide upload button
                    preview.style.display = 'flex';
                    if(uploadBtnWrapper) uploadBtnWrapper.style.display = 'none';
                    if(fileInfo) fileInfo.style.display = 'none';

                    // Set the action to upload for form processing
                    if(cvActionField) cvActionField.value = 'upload';
                }
            });
        }

        // File removal handler
        if(removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Reset file input
                if(cvInput) cvInput.value = '';

                // Hide preview and show upload button
                if(preview) preview.style.display = 'none';
                if(uploadBtnWrapper) uploadBtnWrapper.style.display = 'inline-block';
                if(fileInfo) fileInfo.style.display = 'block';

                // Set the action to delete for form processing
                if(cvActionField) cvActionField.value = 'delete';
            });
        }
    }

    // Initialize handlers
    cvUploadHandler();
});

