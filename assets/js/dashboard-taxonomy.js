/**
 * Handles taxonomy selection, creation, and deletion in frontend dashboard forms.
 * Automatically updates hidden input fields before form submission.
 *
 * @summary   Dashboard taxonomy manager
 * @since     1.0.1
 */

;(function ($) {
    'use strict';

    const JobusDashboardTaxonomy = {

        init: function () {
            // Candidate taxonomies
            this.TaxonomyManager({
                listSelector: '#candidate-category-list',
                inputSelector: '#candidate_categories_input',
                taxonomy: 'jobus_candidate_cat',
                dataAttr: 'category-id'
            });
            this.TaxonomyManager({
                listSelector: '#candidate-location-list',
                inputSelector: '#candidate_locations_input',
                taxonomy: 'jobus_candidate_location',
                dataAttr: 'location-id'
            });
            this.TaxonomyManager({
                listSelector: '#candidate-skills-list',
                inputSelector: '#candidate_skills_input',
                taxonomy: 'jobus_candidate_skill',
                dataAttr: 'skill-id'
            });

            // Job taxonomies
            this.TaxonomyManager({
                listSelector: '#job-category-list',
                inputSelector: '#job_categories_input',
                taxonomy: 'jobus_job_cat',
                dataAttr: 'category-id'
            });
            this.TaxonomyManager({
                listSelector: '#job-location-list',
                inputSelector: '#job_locations_input',
                taxonomy: 'jobus_job_location',
                dataAttr: 'location-id'
            });
            this.TaxonomyManager({
                listSelector: '#job-tag-list',
                inputSelector: '#job_tags_input',
                taxonomy: 'jobus_job_tag',
                dataAttr: 'tag-id'
            });
        },

        /**
         * Taxonomy Manager
         *
         * Handles autocomplete, add, remove, and hidden input sync.
         */
        TaxonomyManager: function (taxonomy) {
            const $list = $(taxonomy.listSelector);
            const $input = $(taxonomy.inputSelector);
            let $inputWrapper = $list.find('.taxonomy-input-wrapper');

            // Track newly created terms
            const tempTerms = [];
            let tempTermCounter = -1; // negative IDs for client-side only terms

            // Ensure AJAX URL is available
            if (!jobus_dashboard_tax_params || !jobus_dashboard_tax_params.ajax_url) {
                console.error('Dashboard parameters not properly initialized');
                return;
            }

            // If no input wrapper exists → create one
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

            /**
             * Show taxonomy input when "+" button clicked
             */
            $list.on('click', '.more_tag button', function (e) {
                e.preventDefault();
                $inputWrapper.show();
                $textInput.val('').focus();
                $suggestions.hide().empty();
            });

            /**
             * Remove a selected tag
             */
            $list.on('click', '.is_tag button', function (e) {
                e.preventDefault();
                const $tag = $(this).closest('.is_tag');

                $tag.fadeOut(200, function () {
                    $(this).remove();

                    // Update hidden input with remaining IDs
                    const remainingIds = [];
                    $list.find('.is_tag').each(function () {
                        remainingIds.push($(this).data(taxonomy.dataAttr));
                    });
                    $input.val(remainingIds.join(','));
                });
            });

            /**
             * Sync hidden input before form submission
             */
            const $form = $list.closest('form');
            $form.on('submit', function () {
                const finalIds = [];
                const finalTerms = [];

                $list.find('.is_tag').each(function () {
                    const $tag = $(this);
                    const id = parseInt($tag.data(taxonomy.dataAttr));
                    const name = $tag.find('button')
                        .clone().children().remove().end()
                        .text().trim();

                    if (id) finalIds.push(id);

                    // Include negative IDs (temporary) or terms tracked in tempTerms
                    if (id < 0 || tempTerms.includes(parseInt(id))) {
                        finalTerms.push({ id: id, name: name });
                    }
                });

                // Always update hidden input
                $input.val(finalIds.join(','));

                // Handle brand new terms (store as hidden JSON input)
                if (finalTerms.length > 0) {
                    const termsFieldName = $input.attr('name') + '_new_terms';
                    $form.find(`input[name="${termsFieldName}"]`).remove();
                    $('<input>', {
                        type: 'hidden',
                        name: termsFieldName,
                        value: JSON.stringify(finalTerms)
                    }).appendTo($form);
                }

                // Debug only
                console.log('[DEBUG] Submitting', taxonomy.taxonomy, 'IDs:', finalIds);
                $('#debug-output').text('Submitting ' + taxonomy.taxonomy + ' IDs: ' + finalIds.join(','));
                console.log('Final Job Categories:', $('#job_categories_input').val());

            });

            /**
             * Fetch suggestions while typing
             */
            $textInput.on('input', function () {
                const query = $textInput.val().trim();
                if (query.length < 1) {
                    $suggestions.hide().empty();
                    return;
                }

                $.ajax({
                    url: jobus_dashboard_tax_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_suggest_taxonomy_terms',
                        security: jobus_dashboard_tax_params.suggest_taxonomy_nonce,
                        taxonomy: taxonomy.taxonomy,
                        term_query: query
                    },
                    beforeSend: () => $textInput.addClass('loading'),
                    success: function (response) {
                        $suggestions.empty();
                        if (response.success && response.data && response.data.length) {
                            response.data.forEach(term => {
                                $suggestions.append(`<li class="dropdown-item" data-term-id="${term.term_id}">${term.name}</li>`);
                            });

                            // Add "create new" option if not an exact match
                            const exactMatch = response.data.some(term => term.name.toLowerCase() === query.toLowerCase());
                            if (!exactMatch) {
                                $suggestions.append(`<li class="dropdown-item create-new-term" data-term-name="${query}"><strong>Create:</strong> "${query}"</li>`);
                            }
                            $suggestions.show();
                        } else {
                            // No results → show create option
                            $suggestions.append(`<li class="dropdown-item create-new-term" data-term-name="${query}"><strong>Create:</strong> "${query}"</li>`);
                            $suggestions.show();
                        }
                    },
                    error: () => console.error(jobus_dashboard_tax_params.texts.taxonomy_suggest_error),
                    complete: () => $textInput.removeClass('loading')
                });
            });

            /**
             * Handle suggestion click
             */
            $suggestions.on('click', 'li', function () {
                const $item = $(this);
                if ($item.hasClass('create-new-term')) {
                    createTerm($item.data('term-name'));
                } else {
                    addTerm($item.data('term-id'), $item.text());
                }
            });

            /**
             * Handle Enter/Escape keys
             */
            $textInput.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const termName = $textInput.val().trim();
                    if (!termName) return;

                    const $exactMatch = $suggestions.find('li:not(.create-new-term)').filter(function () {
                        return $(this).text().toLowerCase() === termName.toLowerCase();
                    });

                    if ($exactMatch.length) {
                        addTerm($exactMatch.data('term-id'), $exactMatch.text());
                    } else {
                        createTerm(termName);
                    }
                } else if (e.key === 'Escape') {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }
            });

            $textInput.on('blur', () => {
                setTimeout(() => {
                    $inputWrapper.hide();
                    $suggestions.hide().empty();
                }, 150);
            });

            /**
             * Helper → create new term via AJAX
             */
            function createTerm(termName) {
                // Check if already exists in current list
                let alreadyExists = false;
                $list.find('.is_tag').each(function () {
                    const existingName = $(this).find('button').text().trim().replace(' ×', '');
                    if (existingName.toLowerCase() === termName.toLowerCase()) {
                        alreadyExists = true;
                        return false;
                    }
                });
                if (alreadyExists) {
                    $textInput.val('');
                    $inputWrapper.hide();
                    return;
                }

                $.ajax({
                    url: jobus_dashboard_tax_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_create_taxonomy_term',
                        security: jobus_dashboard_tax_params.create_taxonomy_nonce,
                        taxonomy: taxonomy.taxonomy,
                        term_name: termName
                    },
                    beforeSend: () => $textInput.addClass('loading'),
                    success: function (response) {
                        if (response.success && response.data) {
                            const termId = response.data.term_id;
                            addTerm(termId, response.data.term_name);
                            tempTerms.push(parseInt(termId)); // ✅ Fix: ensure int push
                        } else {
                            tempTermCounter--;
                            addTerm(tempTermCounter, termName);
                            console.error('Term creation error:', response.data ? response.data.message : 'Unknown error');
                        }
                    },
                    error: function () {
                        tempTermCounter--;
                        addTerm(tempTermCounter, termName);
                        console.error(jobus_dashboard_tax_params.texts.taxonomy_create_error);
                    },
                    complete: () => $textInput.removeClass('loading')
                });
            }

            /**
             * Helper → add a tag to UI and hidden input
             */
            function addTerm(termId, termName) {
                // Prevent duplicates
                let alreadyExists = false;
                $list.find('.is_tag').each(function () {
                    if ($(this).data(taxonomy.dataAttr) == termId) {
                        alreadyExists = true;
                        return false;
                    }
                });
                if (alreadyExists) {
                    $textInput.val('');
                    $inputWrapper.hide();
                    return;
                }

                const newTag = $(`
                    <li class="is_tag" data-${taxonomy.dataAttr}="${termId}">
                        <button type="button">${termName} <i class="bi bi-x"></i></button>
                    </li>
                `);
                $list.find('.more_tag').before(newTag);

                // Update hidden input
                let ids = $input.val() ? $input.val().split(',') : [];
                if (!ids.includes(termId.toString())) {
                    ids.push(termId);
                }
                $input.val(ids.join(','));

                // Reset input
                $textInput.val('');
                $inputWrapper.hide();
                $suggestions.hide().empty();
            }
        },

    };

    $(document).ready(() => JobusDashboardTaxonomy.init());

})(jQuery);