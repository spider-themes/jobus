;(function ($) {

    'use strict';


    $(document).ready(function () {


        /**
         * Handles the preview of the profile picture when a new image is selected.
         * Updates the image preview and sets the action for upload.
         */
        function updateProfilePicturePreview() {
            const fileInput = $('#uploadImg');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');

            // Listen for file input change
            fileInput.on('change', function () {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    // When the file is loaded, set the src of the image to the file's data URL
                    reader.onload = function (e) {
                        imgPreview.attr('src', e.target.result);
                        // Mark image as changed
                        profilePictureAction.val('upload');
                    }

                    // Read the image file as a data URL
                    reader.readAsDataURL(file);
                }
            });
        }

        /**
         * Handles the AJAX-based deletion of the candidate's profile picture.
         * Updates the UI instantly and shows feedback messages.
         */
        function handleDeleteProfilePicture() {
            const deleteButton = $('#delete_profile_picture');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const fileInput = $('#uploadImg');

            // Handle delete button click with AJAX implementation
            deleteButton.on('click', function() {
                // Show loading state
                $(this).text(jobus_dashboard_params.deleting_text || 'Deleting...');

                // AJAX request to delete the profile picture
                $.ajax({
                    url: jobus_dashboard_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_candidate_profile_picture',
                        security: jobus_dashboard_params.security,
                    },
                    success: function(response) {
                        if(response.success) {
                            // Update the image with default avatar
                            const defaultAvatar = jobus_dashboard_params.default_avatar || '';
                            imgPreview.attr('src', defaultAvatar);

                            // Set action to empty since we've already processed it
                            profilePictureAction.val('');

                            // Reset the file input
                            fileInput.val('');

                            // Show success message
                            const successElement = $('<div class="alert alert-success" role="alert"></div>').text(response.data.message);
                            $('#candidate-profile-form').before(successElement);

                            // Remove success message after 3 seconds
                            setTimeout(function() {
                                successElement.fadeOut('slow', function() {
                                    $(this).remove();
                            });
                        }, 3000);
                        } else {
                            // Show error message
                            const errorElement = $('<div class="alert alert-danger" role="alert"></div>').text(response.data.message || 'Error deleting image');
                            $('#candidate-profile-form').before(errorElement);

                            // Remove error message after 3 seconds
                            setTimeout(function() {
                                errorElement.fadeOut('slow', function() {
                                    $(this).remove();
                            });
                        }, 3000);
                        }
                    },
                    error: function() {
                        // Show error message
                        const errorElement = $('<div class="alert alert-danger" role="alert"></div>').text('Server error. Please try again.');
                        $('#candidate-profile-form').before(errorElement);

                        // Remove error message after 3 seconds
                        setTimeout(function() {
                            errorElement.fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 3000);
                    },
                    complete: function() {
                        // Reset button text
                        deleteButton.text(jobus_dashboard_params.delete_text || 'Delete');
                    }
                });
            });
        }

        /**
         * Handles the candidate profile form submission.
         * Placeholder for additional form handling logic if needed.
         */
        function handleProfileFormSubmit() {
            // Form submission handling
            $('#candidate-profile-form').on('submit', function(e) {
                // Additional form handling logic can go here if needed
            });
        }

        /**
         * Handles the dynamic repeater for social media links.
         */
        function SocialLinksRepeater() {
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

            // Add new item
            $('#add-social-link').on('click', function (e) {
                e.preventDefault();
                const newItem = $(`
                    <div class="dash-input-wrapper mb-20 social-link-item d-flex align-items-center gap-2">
                        <label class="me-2 mb-0">Network ${index + 1}</label>
                        <select name="social_icons[${index}][icon]" class="form-select icon-select me-2 max-w-140">
                            ${iconOptions}
                        </select>
                        <input type="text" name="social_icons[${index}][url]" class="form-control me-2 min-w-260" placeholder="#">
                        <button type="button" class="btn btn-danger remove-social-link" title="Remove Item"><i class="bi bi-x"></i></button>
                    </div>
                `);
                repeater.append(newItem);
                index++;
            });

            // Remove item
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
        }

        /**
         * Handles the dynamic repeater for candidate specifications (add/remove rows)
         */
        function CandidateSpecificationsRepeater() {
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
        }

        /**
         * Handles the dynamic repeater for education history (add/remove rows)
         */
        function EducationRepeater() {
            const repeater = $('#education-repeater');
            const addBtn = $('#add-education');
            let index = repeater.children('.education-item').length;

            // Form validation function
            function validateEducationForm(form) {
                let isValid = true;
                const requiredFields = form.find('input[required], textarea[required]');

                requiredFields.each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        // Add error message below the field
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

            // Auto-save draft functionality
            let autoSaveTimeout;
            function setupAutoSave() {
                repeater.on('input', 'input, textarea', function() {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function() {
                        const formData = new FormData($('#candidate-resume-form')[0]);
                        formData.append('action', 'save_education_draft');
                        formData.append('security', window.jobus_nonce);

                        $.ajax({
                            url: window.jobus_ajax_url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    // Optional: Show a subtle "Draft saved" message
                                    const savedMsg = $('<div class="text-success small mt-2 fade-out">Draft saved</div>');
                                    $('#add-education').before(savedMsg);
                                    setTimeout(() => savedMsg.fadeOut('slow', function() { $(this).remove(); }), 2000);
                                }
                            }
                        });
                    }, 2000); // Wait 2 seconds after last input before saving
                });
            }

            addBtn.on('click', function(e) {
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
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="education_${index}_sl_num">${window.jobus_education_sl_label || 'Serial Number'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="education[${index}][sl_num]" 
                                                id="education_${index}_sl_num" 
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="education_${index}_title">${window.jobus_education_title_label || 'Title'}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="education[${index}][title]" 
                                                id="education_${index}_title">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="education_${index}_academy">${window.jobus_education_academy_label || 'Academy'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="education[${index}][academy]" 
                                                id="education_${index}_academy" 
                                                required 
                                                placeholder="Google Arts College & University">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="education_${index}_description">${window.jobus_education_description_label || 'Description'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="size-lg form-control" 
                                                name="education[${index}][description]" 
                                                id="education_${index}_description" 
                                                required 
                                                placeholder="Enter your education description"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-education mt-2" title="${window.jobus_remove_text || 'Remove'}">
                                        <i class="bi bi-x"></i> ${window.jobus_remove_text || 'Remove'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                repeater.append(newItem);
                // Update accordion header when title changes
                newItem.find('input[name$="[title]"]').on('input', function() {
                    const title = $(this).val() || (window.jobus_education_default_title || 'Education');
                    $(this).closest('.education-item').find('.accordion-button').text(title);
                });
                index++;
            });

            // Remove education item
            repeater.on('click', '.remove-education', function() {
                const item = $(this).closest('.education-item');
                // Add confirmation dialog
                if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this education item?')) {
                    item.fadeOut('fast', function() {
                        $(this).remove();
                        reindexEducationItems();
                    });
                }
            });

            // Reindex remaining items
            function reindexEducationItems() {
                repeater.children('.education-item').each(function(i) {
                    const item = $(this);
                    // Update IDs and names
                    item.find('input, textarea').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/education\[\d+\]/, 'education[' + i + ']');
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/education_\d+_/, 'education_' + i + '_');
                            $(this).attr('id', id);
                        }
                    });
                    // Update accordion attributes
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

            // Setup validation on form submit
            $('#candidate-resume-form').on('submit', function(e) {
                const isValid = validateEducationForm($(this));
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            // Initialize auto-save
            setupAutoSave();
        }

        /**
         * Handles the dynamic repeater for work experience (add/remove rows)
         */
        function ExperienceRepeater() {
            const repeater = $('#experience-repeater');
            const addBtn = $('#add-experience');
            let index = repeater.children('.experience-item').length;

            // Form validation function
            function validateExperienceForm(form) {
                let isValid = true;
                const requiredFields = form.find('input[required], textarea[required]');

                requiredFields.each(function() {
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

            // Auto-save draft functionality
            let autoSaveTimeout;
            function setupAutoSave() {
                repeater.on('input', 'input, textarea', function() {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(function() {
                        const formData = new FormData($('#candidate-resume-form')[0]);
                        formData.append('action', 'save_experience_draft');
                        formData.append('security', window.jobus_nonce);

                        $.ajax({
                            url: window.jobus_ajax_url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    const savedMsg = $('<div class="text-success small mt-2 fade-out">Draft saved</div>');
                                    $('#add-experience').before(savedMsg);
                                    setTimeout(() => savedMsg.fadeOut('slow', function() { $(this).remove(); }), 2000);
                                }
                            }
                        });
                    }, 2000);
                });
            }

            // Add new experience item
            addBtn.on('click', function(e) {
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
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_sl_num">${window.jobus_experience_sl_label || 'Serial Number'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="experience[${index}][sl_num]" 
                                                id="experience_${index}_sl_num" 
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_title">${window.jobus_experience_title_label || 'Title'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="experience[${index}][title]" 
                                                id="experience_${index}_title"
                                                required
                                                placeholder="${window.jobus_experience_title_placeholder || 'Lead Product Designer'}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_company">${window.jobus_experience_company_label || 'Company'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" 
                                                class="form-control" 
                                                name="experience[${index}][company]" 
                                                id="experience_${index}_company"
                                                required
                                                placeholder="${window.jobus_experience_company_placeholder || 'Google Inc'}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label>${window.jobus_experience_duration_label || 'Duration'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" 
                                                        class="form-control" 
                                                        name="experience[${index}][start_date]" 
                                                        id="experience_${index}_start_date"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" 
                                                        class="form-control" 
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
                                            <label for="experience_${index}_description">${window.jobus_experience_description_label || 'Description'}*</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="size-lg form-control" 
                                                name="experience[${index}][description]" 
                                                id="experience_${index}_description" 
                                                required 
                                                placeholder="${window.jobus_experience_description_placeholder || 'Describe your role and achievements'}"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-experience mt-2" title="${window.jobus_remove_text || 'Remove'}">
                                        <i class="bi bi-x"></i> ${window.jobus_remove_text || 'Remove'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                repeater.append(newItem);

                // Update accordion header when title changes
                newItem.find('input[name$="[title]"]').on('input', function() {
                    const title = $(this).val() || (window.jobus_experience_default_title || 'Experience');
                    $(this).closest('.experience-item').find('.accordion-button').text(title);
                });
                index++;
            });

            // Remove experience item
            repeater.on('click', '.remove-experience', function() {
                const item = $(this).closest('.experience-item');
                if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this experience item?')) {
                    item.fadeOut('fast', function() {
                        $(this).remove();
                        reindexExperienceItems();
                    });
                }
            });

            // Reindex remaining items
            function reindexExperienceItems() {
                repeater.children('.experience-item').each(function(i) {
                    const item = $(this);
                    item.find('input, textarea').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/experience\[\d+\]/, 'experience[' + i + ']');
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/experience_\d+_/, 'experience_' + i + '_');
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

            // Setup validation on form submit
            $('#candidate-resume-form').on('submit', function(e) {
                const isValid = validateExperienceForm($(this));
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
            });

            // Initialize auto-save
            setupAutoSave();
        }

        /**
         * Handles the portfolio gallery functionality.
         */
        function PortfolioManager() {
            const portfolioSection = $('#portfolio-section');

            // Initialize media uploader
            let mediaUploader;

            function setupMediaUploader() {
                $('#add-portfolio-images').on('click', function(e) {
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

                    mediaUploader.on('select', function() {
                        const selection = mediaUploader.state().get('selection');
                        const portfolioContainer = $('#portfolio-items');
                        const portfolioIds = [];

                        // Get existing IDs
                        const existingIds = $('#portfolio_ids').val().split(',').filter(id => id);

                        selection.map(function(attachment) {
                            attachment = attachment.toJSON();
                            portfolioIds.push(attachment.id);

                            // Add new image preview if it doesn't exist
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

                        // Update hidden input with all IDs (existing + new)
                        const allIds = [...new Set([...existingIds, ...portfolioIds])];
                        $('#portfolio_ids').val(allIds.join(','));
                    });

                    mediaUploader.open();
                });
            }

            function setupImageRemoval() {
                $(document).on('click', '.remove-portfolio-image', function(e) {
                    e.preventDefault();
                    const item = $(this).closest('.portfolio-item');
                    const imageId = item.data('id');

                    if (confirm(window.jobus_confirm_remove_text || 'Are you sure you want to remove this image?')) {
                        // Update hidden input
                        const currentIds = $('#portfolio_ids').val().split(',').filter(id => id && id != imageId);
                        $('#portfolio_ids').val(currentIds.join(','));

                        // Remove preview
                        item.fadeOut('fast', function() {
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
        }

        /**
         * Handles the dynamic taxonomy for candidate categories, locations, and skills with suggestions and tag management
         */
        function CandidateTaxonomyManager(taxonomy) {
            const $list = $(taxonomy.listSelector);
            const $input = $(taxonomy.inputSelector);
            let $inputWrapper = $list.find('.taxonomy-input-wrapper');
            if ($inputWrapper.length === 0) {
                $inputWrapper = $('<li class="taxonomy-input-wrapper" style="display:none;"><input type="text" class="taxonomy-input" placeholder="Type and press Enter to add"><ul class="taxonomy-suggestions dropdown-menu"></ul></li>');
                $list.find('.more_tag').before($inputWrapper);
            }
            const $textInput = $inputWrapper.find('input');
            const $suggestions = $inputWrapper.find('.taxonomy-suggestions');

            // Show input on plus click
            $list.on('click', '.more_tag button', function(e) {
                e.preventDefault();
                $inputWrapper.show();
                $textInput.val('').focus();
                $suggestions.hide().empty();
            });

            // Remove tag
            $list.on('click', '.is_tag button', function(e) {
                e.preventDefault();
                const $tag = $(this).closest('.is_tag');
                const termId = $tag.data(taxonomy.dataAttr);
                $tag.remove();
                // Update hidden input
                let ids = $input.val() ? $input.val().split(',') : [];
                ids = ids.filter(id => id != termId);
                $input.val(ids.join(','));
            });

            // Suggest terms as user types
            $textInput.on('input', function() {
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
                    success: function(response) {
                        if (response.success && response.data && response.data.length) {
                            $suggestions.empty();
                            response.data.forEach(function(term) {
                                $suggestions.append('<li class="dropdown-item" data-term-id="'+term.term_id+'">'+term.name+'</li>');
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide().empty();
                        }
                    },
                    error: function() {
                        $suggestions.hide().empty();
                    }
                });
            });

            // Select suggestion
            $suggestions.on('click', 'li', function() {
                const termId = $(this).data('term-id');
                const termName = $(this).text();
                // Add tag
                const newTag = $('<li class="is_tag" data-'+taxonomy.dataAttr+'="'+termId+'"><button type="button">'+termName+' <i class="bi bi-x"></i></button></li>');
                $list.find('.more_tag').before(newTag);
                // Update hidden input
                let ids = $input.val() ? $input.val().split(',') : [];
                if (!ids.includes(termId.toString())) {
                    ids.push(termId);
                }
                $input.val(ids.join(','));
                // Hide and reset input
                $textInput.val('');
                $inputWrapper.hide();
                $suggestions.hide().empty();
            });

            // Create new term on Enter
            $textInput.on('keydown', function(e) {
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
                        success: function(response) {
                            if (response.success) {
                                // Add new tag
                                const newTag = $('<li class="is_tag" data-'+taxonomy.dataAttr+'="'+response.data.term_id+'"><button type="button">'+response.data.term_name+' <i class="bi bi-x"></i></button></li>');
                                $list.find('.more_tag').before(newTag);
                                // Update hidden input
                                let ids = $input.val() ? $input.val().split(',') : [];
                                ids.push(response.data.term_id);
                                $input.val(ids.join(','));
                                // Hide and reset input
                                $textInput.val('').prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                                $inputWrapper.hide();
                                $suggestions.hide().empty();
                            } else {
                                alert(response.data && response.data.message ? response.data.message : 'Error creating term');
                                $textInput.prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                            }
                        },
                        error: function() {
                            alert('Server error. Please try again.');
                            $textInput.prop('disabled', false).attr('placeholder', 'Type and press Enter to add');
                        }
                    });
                } else if (e.key === 'Escape') {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }
            });

            // Hide input on blur
            $textInput.on('blur', function() {
                setTimeout(function() { $inputWrapper.hide(); $suggestions.hide().empty(); }, 150);
            });
        }

        /**
         * Handles the WordPress media uploader for background image selection in the resume video section
         */
        function BgImageMediaUploader() {
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
        }

        // Initialize all handlers
        updateProfilePicturePreview();
        handleDeleteProfilePicture();
        handleProfileFormSubmit();
        SocialLinksRepeater();
        CandidateSpecificationsRepeater();
        EducationRepeater();
        ExperienceRepeater();
        PortfolioManager().init(); // Initialize portfolio manager

        // Initialize dynamic taxonomy managers for category, location, and skill
        CandidateTaxonomyManager({
            listSelector: '#candidate-category-list',
            inputSelector: '#candidate_categories_input',
            taxonomy: 'jobus_candidate_cat',
            dataAttr: 'category-id'
        });
        CandidateTaxonomyManager({
            listSelector: '#candidate-location-list',
            inputSelector: '#candidate_locations_input',
            taxonomy: 'jobus_candidate_location',
            dataAttr: 'location-id'
        });
        CandidateTaxonomyManager({
            listSelector: '#candidate-skills-list',
            inputSelector: '#candidate_skills_input',
            taxonomy: 'jobus_candidate_skill',
            dataAttr: 'skill-id'
        });
        BgImageMediaUploader();
    })


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
