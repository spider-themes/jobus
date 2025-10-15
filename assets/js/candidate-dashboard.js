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
            this.CandidateSpecificationsRepeater();
            this.EducationRepeater();
            this.ExperienceRepeater();
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
                    div.className = 'dash-input-wrapper jbs-mb-20 specification-item jbs-d-flex jbs-align-items-center jbs-gap-2';
                    div.innerHTML = '<input type="text" name="candidate_specifications['+idx+'][title]" class="jbs-form-control jbs-me-2" placeholder="Title" style="min-width:180px">' +
                        '<input type="text" name="candidate_specifications['+idx+'][value]" class="jbs-form-control jbs-me-2" placeholder="Value" style="min-width:180px">' +
                        '<button type="button" class="jbs-btn jbs-btn-danger remove-specification" title="Remove"><i class="bi bi-x"></i></button>';
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
                                    data-jbs-toggle="collapse"
                                    data-jbs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Education #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="heading-${index}"
                             data-jbs-parent="#education-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_sl_num">Serial Number</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][sl_num]" 
                                                   id="education_${index}_sl_num" 
                                                   class="jbs-form-control"
                                                   placeholder="Enter serial number" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_title">Title</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][title]" 
                                                   id="education_${index}_title" 
                                                   class="jbs-form-control"
                                                   placeholder="Enter education title" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_academy">Academy</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="education[${index}][academy]" 
                                                   id="education_${index}_academy" 
                                                   class="jbs-form-control"
                                                   placeholder="Enter academy name" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="education_${index}_description">Description</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <textarea name="education[${index}][description]" 
                                                      id="education_${index}_description" 
                                                      class="jbs-form-control"
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
                        .attr('data-jbs-target', `#${accordionId}`)
                        .attr('aria-controls', accordionId)
                        .text(`Education #${i + 1}`);

                    $el.find('.accordion-collapse')
                        .attr('id', accordionId)
                        .attr('aria-labelledby', `heading-${i}`);

                    $el.find('input, textarea').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/education\[\d+]/, `education[${i}]`);
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
                                    data-jbs-toggle="collapse"
                                    data-jbs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Experience #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="headingExp-${index}"
                             data-jbs-parent="#experience-repeater">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_sl_num">Serial Number</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="jbs-form-control" 
                                                   name="experience[${index}][sl_num]" 
                                                   id="experience_${index}_sl_num"
                                                   placeholder="Enter serial number">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_title">Title</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="jbs-form-control"
                                                   name="experience[${index}][title]"
                                                   id="experience_${index}_title"
                                                   placeholder="Enter experience title">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label>Duration</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="row">
                                            <div class="jbs-col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="jbs-form-control"
                                                           name="experience[${index}][start_date]"
                                                           id="experience_${index}_start_date">
                                                </div>
                                            </div>
                                            <div class="jbs-col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="jbs-form-control"
                                                           name="experience[${index}][end_date]"
                                                           id="experience_${index}_end_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="jbs-col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_${index}_description">Description</label>
                                        </div>
                                    </div>
                                    <div class="jbs-col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="jbs-form-control"
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
                        .attr('data-jbs-target', `#${accordionId}`)
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
            frame.on('update', function(selection) {
                // Convert selection to JSON
                const attachments = selection.toJSON();
                // Clear previous selection
                portfolioIdsField.value = '';
                const listContainer = getOrCreateListContainer();
                listContainer.innerHTML = '';

                if (attachments.length > 0) {
                    const allIds = [];

                    attachments.forEach(function(attachment) {
                        const id = attachment.id.toString();
                        allIds.push(id);
                        const imageUrl = attachment.sizes?.thumbnail?.url || attachment.sizes?.medium?.url || attachment.url;
                        const listItem = document.createElement('li');
                        listItem.setAttribute('data-id', id);
                        listItem.innerHTML = `<img src="${imageUrl}" alt="${attachment.alt || attachment.title || ''}">`;
                        listContainer.appendChild(listItem);
                    });

                    portfolioIdsField.value = allIds.join(',');
                    portfolioContainer.style.display = 'block';
                    const previewWrapper = portfolioContainer.closest('.portfolio-preview-wrapper');
                    if (previewWrapper) previewWrapper.style.display = 'block';
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
