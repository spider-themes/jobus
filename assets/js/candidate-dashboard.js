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
            this.handleDeleteProfilePicture();
            this.SocialLinksRepeater();
            this.CandidateSpecificationsRepeater();
            this.EducationRepeater();
            this.ExperienceRepeater();
            this.PortfolioManager().init();
            this.BgImageMediaUploader();

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

            if (!mediaBtn.length || !window.wp || !window.wp.media) return;

            mediaBtn.on('click', function(e) {
                e.preventDefault();
                let mediaUploader = wp.media({
                    title: 'Select Profile Picture',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    imgPreview.attr('src', attachment.url);
                    hiddenId.val(attachment.id);
                    profilePictureAction.val('upload');
                });
                mediaUploader.open();
            });
        },


        /**
         * Handles the deletion of the profile picture.
         * Sets the default avatar, clears the hidden ID field,
         */
        handleDeleteProfilePicture: function () {
            const deleteButton = $('#delete_profile_picture');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const hiddenId = $('#candidate_profile_picture_id');
            const defaultAvatar = jobus_dashboard_params.default_avatar || '';

            deleteButton.on('click', function() {
                imgPreview.attr('src', defaultAvatar);
                hiddenId.val('');
                profilePictureAction.val('delete');
            });
        },


        /**
         * Handles background image selection using the WordPress media uploader.
         * Allows users to select an image from the media library and set it as background.
         *
         * @function BgImageMediaUploader
         * @returns {void}
         */
        BgImageMediaUploader: function () {
            const selectBtn = document.getElementById('select_bg_img');
            const imgIdInput = document.getElementById('bg_img_id');
            const imgUrlInput = document.getElementById('bg_img_url');
            if (!selectBtn || !window.wp || !window.wp.media) return;
            selectBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let mediaUploader = wp.media({
                    title: 'Select Background Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    if (imgIdInput) imgIdInput.value = attachment.id;
                    if (imgUrlInput) imgUrlInput.value = attachment.url;
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

            const iconOptions = icons.map(icon => `<option value="${icon.value}">${icon.label}</option>`).join('');
            const repeater = $('#social-links-repeater');
            let index = repeater.children('.social-link-item').length;

            // Add new social link
            $('#add-social-link').on('click', function (e) {
                e.preventDefault();
                const newItem = $(`
                    <div class="dash-input-wrapper mb-20 social-link-item d-flex align-items-center gap-2">
                        <label class="me-2 mb-0">Network ${index + 1}</label>
                        <select name="social_icons[${index}][icon]" class="form-select icon-select me-2 max-w-140">
                            ${iconOptions}
                        </select>
                        <input type="text" name="social_icons[${index}][url]" class="form-control me-2 min-w-260" placeholder="#">
                        <button type="button" class="btn btn-danger remove-social-link" title="Remove Item">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `);
                repeater.append(newItem);
                index++;
            });

            // Remove social link
            repeater.on('click', '.remove-social-link', function () {
                $(this).closest('.social-link-item').remove();

                // Re-index remaining items
                repeater.children('.social-link-item').each(function (i, el) {
                    $(el).find('label').text(`Network ${i + 1}`);
                    $(el).find('select').attr('name', `social_icons[${i}][icon]`);
                    $(el).find('input[type="text"]').attr('name', `social_icons[${i}][url]`);
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

            function validateEducationForm(form) {
                let isValid = true;
                const requiredFields = form.find('input[required], textarea[required]');
                requiredFields.each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after(`<div class="invalid-feedback">${window.jobus_required_field_text || 'This field is required'}</div>`);
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }
                });
                return isValid;
            }

            function setupAutoSave() {
                let autoSaveTimeout;
                repeater.on('input', 'input, textarea', function () {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function () {
                        const formData = new FormData($('#candidate-resume-form')[0]);
                        formData.append('action', 'save_education_draft');
                        formData.append('security', window.jobus_nonce);

                        $.ajax({
                            url: window.jobus_ajax_url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response.success) {
                                    const savedMsg = $('<div class="text-success small mt-2 fade-out">Draft saved</div>');
                                    $('#add-education').before(savedMsg);
                                    setTimeout(() => savedMsg.fadeOut('slow', function () { $(this).remove(); }), 2000);
                                }
                            }
                        });
                    }, 2000);
                });
            }

            addBtn.on('click', function (e) {
                e.preventDefault();
                const newItem = $(`
                    <div class="accordion-item education-item">
                        <div class="accordion-header" id="headingOne-${index}">
                            <button class="accordion-button" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseOne-${index}" 
                                aria-expanded="true" 
                                aria-controls="collapseOne-${index}">
                                ${window.jobus_education_default_title || 'Education'}
                            </button>
                        </div>
                        <div id="collapseOne-${index}" class="accordion-collapse collapse show" 
                            aria-labelledby="headingOne-${index}" 
                            data-bs-parent="#education-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <input type="text" class="form-control mb-2" name="education[${index}][sl_num]" placeholder="Serial Number" required />
                                        <input type="text" class="form-control mb-2" name="education[${index}][title]" placeholder="Title" />
                                        <input type="text" class="form-control mb-2" name="education[${index}][academy]" placeholder="Academy" required />
                                        <textarea class="form-control" name="education[${index}][description]" placeholder="Description" required></textarea>
                                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-education"><i class="bi bi-x"></i> ${window.jobus_remove_text || 'Remove'}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                repeater.append(newItem);

                newItem.find('input[name$="[title]"]').on('input', function () {
                    const title = $(this).val() || (window.jobus_education_default_title || 'Education');
                    $(this).closest('.education-item').find('.accordion-button').text(title);
                });

                index++;
            });

            repeater.on('click', '.remove-education', function () {
                const item = $(this).closest('.education-item');
                if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this education item?')) {
                    item.fadeOut('fast', function () {
                        $(this).remove();
                        reindexEducationItems();
                    });
                }
            });

            function reindexEducationItems() {
                repeater.children('.education-item').each(function (i) {
                    const item = $(this);
                    item.find('input, textarea').each(function () {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/education\\[\\d+\\]/, 'education[' + i + ']');
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/education_\\d+_/, 'education_' + i + '_');
                            $(this).attr('id', id);
                        }
                    });
                    item.find('.accordion-header')
                        .attr('id', 'headingOne-' + i)
                        .find('.accordion-button')
                        .attr('data-bs-target', '#collapseOne-' + i)
                        .attr('aria-controls', 'collapseOne-' + i);

                    item.find('.accordion-collapse')
                        .attr('id', 'collapseOne-' + i)
                        .attr('aria-labelledby', 'headingOne-' + i);
                });
                index = repeater.children('.education-item').length;
            }

            $('#candidate-resume-form').on('submit', function (e) {
                const isValid = validateEducationForm($(this));
                if (!isValid) {
                    e.preventDefault();
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            setupAutoSave();
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

            function validateExperienceForm(form) {
                let isValid = true;
                const requiredFields = form.find('input[required], textarea[required]');
                requiredFields.each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        if (!$(this).next('.invalid-feedback').length) {
                            $(this).after(`<div class="invalid-feedback">${window.jobus_required_field_text || 'This field is required'}</div>`);
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }
                });
                return isValid;
            }

            function setupAutoSave() {
                let autoSaveTimeout;
                repeater.on('input', 'input, textarea', function () {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function () {
                        const formData = new FormData($('#candidate-resume-form')[0]);
                        formData.append('action', 'save_experience_draft');
                        formData.append('security', window.jobus_nonce);

                        $.ajax({
                            url: window.jobus_ajax_url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response.success) {
                                    const savedMsg = $('<div class="text-success small mt-2 fade-out">Draft saved</div>');
                                    $('#add-experience').before(savedMsg);
                                    setTimeout(() => savedMsg.fadeOut('slow', function () { $(this).remove(); }), 2000);
                                }
                            }
                        });
                    }, 2000);
                });
            }

            addBtn.on('click', function (e) {
                e.preventDefault();
                const newItem = $(`
                    <div class="accordion-item experience-item">
                        <div class="accordion-header" id="headingExp-${index}">
                            <button class="accordion-button" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseExp-${index}" 
                                aria-expanded="true" 
                                aria-controls="collapseExp-${index}">
                                ${window.jobus_experience_default_title || 'Experience'}
                            </button>
                        </div>
                        <div id="collapseExp-${index}" class="accordion-collapse collapse show" 
                            aria-labelledby="headingExp-${index}" 
                            data-bs-parent="#experience-repeater">
                            <div class="accordion-body">
                                <input type="text" class="form-control mb-2" name="experience[${index}][sl_num]" placeholder="Serial Number" required />
                                <input type="text" class="form-control mb-2" name="experience[${index}][title]" placeholder="Title" required />
                                <input type="text" class="form-control mb-2" name="experience[${index}][company]" placeholder="Company" required />
                                <div class="row g-2 mb-2">
                                    <div class="col">
                                        <input type="date" class="form-control" name="experience[${index}][start_date]" required />
                                    </div>
                                    <div class="col">
                                        <input type="date" class="form-control" name="experience[${index}][end_date]" />
                                    </div>
                                </div>
                                <textarea class="form-control" name="experience[${index}][description]" placeholder="Description" required></textarea>
                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-experience">
                                    <i class="bi bi-x"></i> ${window.jobus_remove_text || 'Remove'}
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                repeater.append(newItem);

                newItem.find('input[name$="[title]"]').on('input', function () {
                    const title = $(this).val() || (window.jobus_experience_default_title || 'Experience');
                    $(this).closest('.experience-item').find('.accordion-button').text(title);
                });

                index++;
            });

            repeater.on('click', '.remove-experience', function () {
                const item = $(this).closest('.experience-item');
                if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this experience item?')) {
                    item.fadeOut('fast', function () {
                        $(this).remove();
                        reindexExperienceItems();
                    });
                }
            });

            function reindexExperienceItems() {
                repeater.children('.experience-item').each(function (i) {
                    const item = $(this);
                    item.find('input, textarea').each(function () {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/experience\\[\\d+\\]/, 'experience[' + i + ']');
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/experience_\\d+_/, 'experience_' + i + '_');
                            $(this).attr('id', id);
                        }
                    });
                    item.find('.accordion-header')
                        .attr('id', 'headingExp-' + i)
                        .find('.accordion-button')
                        .attr('data-bs-target', '#collapseExp-' + i)
                        .attr('aria-controls', 'collapseExp-' + i);

                    item.find('.accordion-collapse')
                        .attr('id', 'collapseExp-' + i)
                        .attr('aria-labelledby', 'headingExp-' + i);
                });
                index = repeater.children('.experience-item').length;
            }

            $('#candidate-resume-form').on('submit', function (e) {
                const isValid = validateExperienceForm($(this));
                if (!isValid) {
                    e.preventDefault();
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            setupAutoSave();
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
                        security: jobus_dashboard_params.security,
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
                            security: jobus_dashboard_params.security,
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

