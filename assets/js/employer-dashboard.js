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

    const JobusEmployerDashboard = {

        init: function () {
            this.Testimonials();
            this.CompanyWebsiteToggle();
        },


        /**
         * Manages the education section repeater in the candidate dashboard.
         * Handles adding, removing, validating, and auto-saving education items.
         *
         * @function Testimonials
         * @returns {void}
         */
        Testimonials: function () {
            const repeater = $('#company-testimonial-repeater');
            const addBtn = $('#add-company-testimonial');
            let index = repeater.children('.company-testimonial-item').length;

            // Handle image upload for testimonials
            repeater.on('click', '.testimonial-image-upload-btn', function (e) {
                e.preventDefault();
                const $btn = $(this);
                const $container = $btn.closest('.dash-input-wrapper');
                const $hiddenId = $container.find('.testimonial-image-id');
                const $preview = $container.find('.testimonial-image-preview');
                if (!window.wp || !window.wp.media) return;
                const mediaUploader = wp.media({
                    title: 'Select Author Image',
                    button: { text: 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $hiddenId.val(attachment.id);
                    $preview.html('<img src="' + attachment.url + '" alt="Author Image" style="max-width:100px;max-height:100px;">');
                });
                mediaUploader.open();
            });

            addBtn.on('click', function (e) {
                e.preventDefault();
                const testimonialId = `company-testimonial-${index}`;
                const newItem = $(`
                    <div class="accordion-item company-testimonial-item">
                        <div class="accordion-header" id="company-testimonial-heading-${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${testimonialId}"
                                    aria-expanded="false"
                                    aria-controls="${testimonialId}">
                                Testimonial #${index + 1}
                            </button>
                        </div>
                        <div id="${testimonialId}" class="accordion-collapse collapse"
                             aria-labelledby="company-testimonial-heading-${index}"
                             data-bs-parent="#company-testimonial-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-${index}-author-name">Author Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="company_testimonials[${index}][author_name]"
                                                   id="company-testimonial-${index}-author-name" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-${index}-location">Location</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="company_testimonials[${index}][location]"
                                                   id="company-testimonial-${index}-location" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-${index}-review-content">Review</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <textarea name="company_testimonials[${index}][review_content]"
                                                      id="company-testimonial-${index}-review-content" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-${index}-rating">Rating</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <select name="company_testimonials[${index}][rating]"
                                                    id="company-testimonial-${index}-rating" class="form-control">
                                                <option value="">--</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="company-testimonial-${index}-image">Author Image</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <!-- Hidden field for image ID -->
                                            <input type="hidden" name="company_testimonials[${index}][image]"
                                                   id="company-testimonial-${index}-image" class="testimonial-image-id" value="">
                                            <!-- Upload button for WP media -->
                                            <button type="button" class="btn btn-secondary testimonial-image-upload-btn" data-index="${index}">
                                                Upload Image
                                            </button>
                                            <!-- Preview area (always present) -->
                                            <div class="testimonial-image-preview mt-2 mb-2" id="testimonial-image-preview-${index}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-company-testimonial mt-2 mb-2" title="Remove Item">
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

            repeater.on('click', '.remove-company-testimonial', function () {
                $(this).closest('.company-testimonial-item').remove();
                // Re-index all fields
                repeater.children('.company-testimonial-item').each(function (i, el) {
                    const testimonialId = `company-testimonial-${i}`;
                    const $el = $(el);
                    $el.find('.accordion-header').attr('id', `company-testimonial-heading-${i}`);
                    $el.find('.accordion-button')
                        .attr('data-bs-target', `#${testimonialId}`)
                        .attr('aria-controls', testimonialId)
                        .text(`Testimonial #${i + 1}`);
                    $el.find('.accordion-collapse')
                        .attr('id', testimonialId)
                        .attr('aria-labelledby', `company-testimonial-heading-${i}`);
                    $el.find('input[name^="company_testimonials"]').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/company_testimonials\[\d+]/, `company_testimonials[${i}]`);
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/company-testimonial-\d+(-[a-z-]+)/, `company-testimonial-${i}$1`);
                            $(this).attr('id', id);
                        }
                    });
                    $el.find('textarea[name^="company_testimonials"]').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/company_testimonials\[\d+]/, `company_testimonials[${i}]`);
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/company-testimonial-\d+(-[a-z-]+)/, `company-testimonial-${i}$1`);
                            $(this).attr('id', id);
                        }
                    });
                    $el.find('select[name^="company_testimonials"]').each(function() {
                        let name = $(this).attr('name');
                        let id = $(this).attr('id');
                        if (name) {
                            name = name.replace(/company_testimonials\[\d+]/, `company_testimonials[${i}]`);
                            $(this).attr('name', name);
                        }
                        if (id) {
                            id = id.replace(/company-testimonial-\d+(-[a-z-]+)/, `company-testimonial-${i}$1`);
                            $(this).attr('id', id);
                        }
                    });
                    $el.find('label[for^="company-testimonial-"]').each(function() {
                        let htmlFor = $(this).attr('for');
                        if (htmlFor) {
                            htmlFor = htmlFor.replace(/company-testimonial-\d+(-[a-z-]+)/, `company-testimonial-${i}$1`);
                            $(this).attr('for', htmlFor);
                        }
                    });
                });
                index = repeater.children('.company-testimonial-item').length;
            });
        },


        /**
         * Handles toggling the company website fields based on radio selection.
         */
        CompanyWebsiteToggle: function () {
            const $select = $('#is_company_website');
            const $fields = $('#company-website-fields');
            $select.on('change', function () {
                if ($(this).val() === 'custom') {
                    $fields.show();
                } else {
                    $fields.hide();
                }
            });
            // Initial state (in case of dynamic content)
            if ($select.val() === 'custom') {
                $fields.show();
            } else {
                $fields.hide();
            }
        }

    };

    $(document).ready(function () {
        JobusEmployerDashboard.init();
    });

})(jQuery);
