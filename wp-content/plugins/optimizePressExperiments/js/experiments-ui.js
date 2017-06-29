(function($) {
    $(document).ready(function() {

        function OpExperiments() {
            var selectedPages = [],
                that = this;

            /**
             * Init class.
             */
            this.init = function() {
                this._initEvents();
            };

            /**
             * Init DOM events.
             * @return {void}
             */
            this._initEvents = function() {
                $(document).on('click', '.op-experiments-new-variant', this.newVariant);
                $(document).on('click', '.op-experiments-new-clone-variant', this.cloneOriginal);
                $(document).on('click', '.op-experiments-variant-delete', this.removeVariant);

                $('.op-experiments-stats-main-content').on('click', '.op-experiments-edit', this.editExperimentForm);
                $('.op-experiments-edit-experiment').on('click', this.editExperimentForm);

                $('.op-experiments-stats-main-content').on('click', '.op-experiments-new-experiment', this.createExperimentForm);
                $('#op-pagebuilder-container').on('click', '.op-experiments-new-experiment', this.createExperimentForm);

                $(document).on('click', '.op-experiments-save-experiment', this.saveExperiment);

                $('.op-experiments-stats-main-content').on('click', '.op-experiments-switch-status', this.switchExperimentStatus);
                $('#op-pagebuilder-container').on('click', '.op-experiments-switch-status', this.switchExperimentStatus);

                $('.op-experiments-stats-main-content').on('click', '.op-delete-experiment', this.deleteExperiment);

                $(document).on('change', '#op_sections_experiments_goal_type', this.toggleGoalTypeFields);

                $(document).on('experiments:taken', '#op-experiments-experiment-form', this.disableSelectedPages);
            };

            /**
             * Disable options in dropdowns that are already taken by current experiment.
             * @param  {Object} event
             * @param  {Object} element
             * @param  {string} value
             * @return {void}
             */
            this.disableSelectedPages = function(event, element, value) {
                var $elements = $('.op-experiments-original-page-autocomplete, #op_sections_experiments_goal_page, .op-experiments-variant-page-autocomplete').not(element).filter(':visible');

                // Reseting disabled states
                $elements.find('option').attr('disabled', false);

                // Disabling options
                if (selectedPages.length > 0) {
                    $elements.find('option').filter(function(index) {
                        return $.inArray($(this).attr('value'), selectedPages) >= 0 && 'selected' !== $(this).attr('selected');
                    }).attr('disabled', true);
                }

                // Re-init select2
                $elements.select2({
                    placeholder: OPSE.l10n.select_page
                });
            };

            /**
             * Bind autocomplete handler to jQuery UI AutoComplete.
             * @param  {Object} $elements
             * @apram  {bool} filter
             * @return {void}
             */
            this.bindAutoComplete = function($elements, filter) {

                filter = typeof filter !== 'undefined' ? filter : true;

                var withoutFilter = '';
                if (filter === false) {
                    withoutFilter = '&no-filter=1';
                }

                var experimentId = $('#op_sections_experiments_experiment_id').val();

                $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-page-list&experiment_id=' + experimentId + withoutFilter,
                    type: 'GET',
                    dataType: 'json'
                }).then(function(response) {
                    // Appending options
                    $.each(response.data, function(index, item) {
                        $elements.append($('<option></option>').attr('value', item.id).text(item.text));
                    });

                    // Reseting disabled states
                    $elements.find('option').attr('disabled', false);

                    // Disabling already selected pages
                    if (selectedPages.length > 0) {
                        $elements.find('option').filter(function(index) {
                            return $.inArray($(this).attr('value'), selectedPages) >= 0 && $(this).attr('value') !== $(this).parent().prev().val();
                        }).attr('disabled', true);
                    }

                    // Initializing select2
                    $elements.select2({
                        placeholder: OPSE.l10n.select_page
                    }).on('select2:selecting', function(event) {
                        var value = event.currentTarget.value;
                        if (value !== '') {
                            var index = $.inArray(value, selectedPages);
                            if (index >= 0) {
                                selectedPages.splice(index, 1);
                            }
                        }
                    }).on('select2:select', function(event) {
                        // Add current value to list of selected pages
                        selectedPages.push(event.currentTarget.value);

                        // Set selected attribute needed for not disabling active option
                        // $(event.currentTarget).find('option[value=' + event.currentTarget.value + ']').attr('selected', true);

                        // Trigger taken event which disables options
                        $('#op-experiments-experiment-form').trigger('experiments:taken', [event.currentTarget, event.currentTarget.value]);

                        // Set hidden field value
                        $(event.currentTarget).prev().val(event.params.data.id);
                    }).on('change', function(event) {
                        // Set selected attribute needed for not disabling active option
                        if (event.currentTarget.value !== '') {
                            $(event.currentTarget).find('option[value=' + event.currentTarget.value + ']').attr('selected', true);
                        }
                    });

                    // Setting a value
                    $elements.each(function () {
                        $(this).val($(this).prev().val()).trigger('change');
                    });
                });
            };

            /**
             * Init date range picker.
             * @param  {string} selector jQuery selector
             * @return {dateRangePicker}
             */
            this.initDateRangePicker = function(selector) {
                return $(selector).dateRangePicker({
                    autoClose: true,
                    singleDate : true,
                    showShortcuts: false,
                    singleMonth: true,
                    startDate: moment().format('YYYY-MM-DD')
                });
            };

            /**
             * Duplicates last variant form inputs and binds autocomplete handlers.
             * @param  {Array} event
             * @return {boolean}
             */
            this.newVariant = function(event) {
                // Get last variant & clone it
                var $newVariant = $('.op-experiments-variant:last').clone();

                // Clear values
                $newVariant.find('input').val('');

                // Remove select2
                $newVariant.find('.select2').remove();

                // Attach autocomplete
                that.bindAutoComplete($newVariant.find('.op-experiments-variant-page-autocomplete'));

                // Append it
                $('.op-experiments-variants').append($newVariant);

                return false;
            };

            /**
             * Clone original page and add it as a variant.
             * @param  {Array} event
             * @return {boolean}
             */
            this.cloneOriginal = function(event) {
                var originalPageId = $('#op_sections_experiments_original_page_id').val();

                that.clonePage(originalPageId).then(function(response) {
                    var $lastVariant = $('#op-experiments-experiment-form .op-experiments-variant:last'),
                        count = $('#op-experiments-experiment-form .op-experiments-variant').length;

                    if ($lastVariant.find('.op-experiments-page-id').val() !== '') {
                        that.newVariant([]);
                        $lastVariant = $('#op-experiments-experiment-form .op-experiments-variant:last');
                        count += 1;
                    }

                    that._fillVariantFields($lastVariant, {name: 'Variation #' + count, page_title: response.data.title, page_id: response.data.id});
                });

                return false;
            };

            /**
             * Get page data through deffered.
             * @param  {string} pageId
             * @return {Deffered}
             */
            this.getPage = function(pageId) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-get-page',
                    type: 'GET',
                    data: {
                        page_id: pageId
                    },
                    dataType: 'json'
                });
            }

            /**
             * Clone page and return its ID and title through deffered.
             * @param  {string} pageId
             * @return {Deffered}
             */
            this.clonePage = function(pageId) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-clone-page',
                    type: 'GET',
                    data: {
                        page_id: pageId
                    },
                    dataType: 'json'
                });
            }

            /**
             * Removes variant (all but one, it clear the last one).
             * @param  {Array} event
             * @return {void}
             */
            this.removeVariant = function(event) {
                if ($('.op-experiments-variant').length > 1) {
                    // If more then one variant exists simply delete it
                    $(event.currentTarget).parent().remove();
                } else {
                    // Clear/reset data from the last variant
                    $('.op-experiments-variant:last').find('input').val('');
                }

                return false;
            }

            /**
             * Render new experiment form.
             * @param  {Array} event
             * @return {boolean}
             */
            this.createExperimentForm = function(event) {
                var experimentId = 0,
                    template = wp.template('op-experiment-edit');

                // Reseting selected pages
                selectedPages = [];

                $.fancybox($.extend({}, OptimizePress.fancybox_defaults, {
                    minWidth: 400,
                    content: template({
                        form_title: "New experiment",
                        container_class: "op-new-experiment",
                        action_label: "Create"
                    })
                }));

                $('.fancybox-inner #op_sections_experiments_goal_type').trigger('change');
                that.bindAutoComplete($('.fancybox-inner .op-experiments-original-page-autocomplete, .fancybox-inner .op-experiments-variant-page-autocomplete'));

                var startDatePicker = that.initDateRangePicker('#op_sections_experiments_start_date');
                var endDatePicker = that.initDateRangePicker('#op_sections_experiments_end_date');

                if ($(this).attr('data-page-id')) {
                    that.getPage($(this).attr('data-page-id')).then(function(response) {
                        if (response.success === false) {
                            alert(response.data.message);
                            return;
                        }

                        $('#op_sections_experiments_original_page').val(response.data.title);
                        $('#op_sections_experiments_original_page_id').val(response.data.id);
                    });
                }

                return false;
            };

            /**
             * Render edit experiment form in a Fancybox popup.
             * @param  {Array} event
             * @return {boolean}
             */
            this.editExperimentForm = function(event) {
                var experimentId = $(this).attr('data-experiment-id') || $(this).parents('tr').attr('data-experiment-id'),
                    template = wp.template('op-experiment-edit');

                // Reseting selected pages
                selectedPages = [];

                $.fancybox($.extend({}, OptimizePress.fancybox_defaults, {
                    minWidth: 400,
                    content: template({
                        form_title: "Edit experiment",
                        container_class: "op-edit-experiment",
                        action_label: "Update"
                    })
                }));

                $('#op_sections_experiments_experiment_id').val(experimentId);

                var startDatePicker = that.initDateRangePicker('#op_sections_experiments_start_date');
                var endDatePicker = that.initDateRangePicker('#op_sections_experiments_end_date');

                that.getExperiment(experimentId).then(function(response) {
                    if (response.success === false) {
                        alert(response.data.message);
                        return;
                    }

                    var experiment = response.data.experiment,
                        variations = response.data.variations;

                    $('#op_sections_experiments_experiment_status').val(experiment.status);
                    $('#op_sections_experiments_experiment_name').val(experiment.name);
                    $('#op_sections_experiments_start_date').val(experiment.start_date);
                    $('#op_sections_experiments_end_date').val(experiment.end_date);
                    $('#op_sections_experiments_original_page').val(experiment.page_id);
                    $('#op_sections_experiments_original_page_id').val(experiment.page_id);
                    $('#op_sections_experiments_goal_type').val(experiment.goal_type).trigger('change');
                    $('#op_sections_experiments_goal_page').val(experiment.goal_page_id);
                    $('#op_sections_experiments_goal_page_id').val(experiment.goal_page_id);

                    // Add selected page IDS to a list of disabled options
                    selectedPages.push(experiment.page_id);
                    selectedPages.push(experiment.goal_page_id);

                    that.bindAutoComplete($('.fancybox-inner .op-experiments-original-page-autocomplete'));

                    $.each(variations, function(index, variation) {
                        if (index !== 0) {
                            that.newVariant();
                        }

                        // Add selected page IDS to a list of disabled options
                        selectedPages.push(variation.page_id);

                        that._fillVariantFields($('#op-experiments-experiment-form .op-experiments-variant:last'), variation);
                    });

                });
                return false;
            };

            /**
             * Return experiment through deffered object.
             * @param  {integer} experimentId
             * @return {Deffered}
             */
            this.getExperiment = function(experimentId) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-get-experiment',
                    type: 'GET',
                    data: {
                        experiment_id: experimentId
                    },
                    dataType: 'json'
                });
            };

            /**
             * Save new experiment data to DB via AJAX.
             * @param  {Array} event
             * @return {boolean}
             */
            this.saveExperiment = function(event) {
                var variations = [],
                    status = $('#op_sections_experiments_experiment_status').val(),
                    endDate = $('#op_sections_experiments_end_date').val(),
                    goalType = $('#op_sections_experiments_goal_type').val(),
                    startDate = $('#op_sections_experiments_start_date').val(),
                    goalPageId = $('#op_sections_experiments_goal_page_id').val(),
                    experimentId = $('#op_sections_experiments_experiment_id').val(),
                    experimentName = $('#op_sections_experiments_experiment_name').val(),
                    originalPageId = $('#op_sections_experiments_original_page_id').val();

                $('.op-experiments-variant').each(function (i, item) {
                    variations.push({
                        'id': $(item).find('input[name*="variation_page_id"]').val(),
                        'label': $(item).find('input[name*="variation_label"]').val()
                    });
                });

                // TODO: Validation

                $.ajax({
                    url: OptimizePress.ajaxurl +'?action=op-experiments-save-experiment',
                    type: 'POST',
                    data: {
                        status: status,
                        end_date: endDate,
                        goal_type: goalType,
                        start_date: startDate,
                        variations: variations,
                        goal_page_id: goalPageId,
                        experiment_id: experimentId,
                        experiment_name: experimentName,
                        original_page_id: originalPageId
                    },
                    success: function(response) {
                        // Reset the form
                        that.resetTheForm($('#op-experiments-experiment-form'));

                        // Check where from are we triggering this
                        if ($('.op-experiments-stats-main-content').length) {
                            // If we are triggering this from dedicated experiments page add new/modify experiment row to UI table
                            that._updateExperimentRow({
                                status: status,
                                end_date: endDate,
                                goal_type: goalType,
                                start_date: startDate,
                                experiment_id: response.data.experiment_id,
                                experiment_name: experimentName,
                                original_page_id: originalPageId,
                                original_page_url: response.data.original_page_url,
                                original_page_title: response.data.original_page_title
                            });
                        } else {
                            // If we are triggering this from edit page screen
                            $('.op-experiments-new-experiment').hide();
                            $('.op-experiments-edit-experiment').show().attr('data-experiment-id', response.data.experiment_id);

                            that._updateExperimentStatusButton(response.data.experiment_id, status, $('.op-experiments-switch-status'));
                            $('.op-experiments-experiment-status').show();
                        }

                        OptimizePress.disable_alert = true;
                        $.fancybox.close();
                        OptimizePress.disable_alert = false;
                    },
                    dataType: 'json'
                });

                return false;
            };

            /**
             * Delete experiment after user has confirmed that he wants to delete it.
             * @param  {Array} event
             * @return {boolean}
             */
            this.deleteExperiment = function(event) {
                if (confirm(OPSE.l10n.are_you_sure_you_want_to_delete_experiment)) {
                    var $parent = $(this).parents('tr'),
                    experimentId = $parent.attr('data-experiment-id');

                    that.removeExperiment(experimentId).then(function(response) {
                        if (response.success === false) {
                            return;
                        }

                        // Remove table row and decrease rowspan
                        $parent.remove();
                        that._decrementRowspan();

                        // Remove chart box
                        $('#op-experiment-charts-' + experimentId).remove();

                        if ($('#op-experiments-table tbody tr').length < 1) {
                            // Remove table
                            $('#op-experiments-table, #op-experiment-stats').remove();

                            // Show no experiments template
                            $('.op-experiments-stats-main-content').append(wp.template('op-experiment-no-experiments')());
                        } else if ($('#op-experiments-table .op-experiments-new-experiment').length === 0) {
                            // Append add experiment template
                            $('#op-experiments-table tbody tr')
                                .first()
                                .append('<td rowspan="' + $('#op-experiments-table tr').length + '">' + wp.template('op-experiment-add')() + '</td>');
                        }

                        // Adjust clearfix divs
                        $('#op-experiment-stats .cf').remove();
                        $('.op-experiment-overview:odd').after('<div class="cf"></div>');
                    });
                }

                return false;
            }

            /**
             * Remove experiment data and stats from DB.
             * @param  {integer} experimentId
             * @return {Deffered}
             */
            this.removeExperiment = function(experimentId) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-remove-experiment',
                    type: 'GET',
                    data: {
                        experiment_id: experimentId
                    },
                    dataType: 'json'
                });
            };

            /**
             * Switch experiment status. 0 for stopped, 1 for paused and 2 for running.
             * @param  {Array} event
             * @return {boolean}
             */
            this.switchExperimentStatus = function(event) {
                var $parent = $(this).parents('tr'),
                    experimentId = $(this).attr('data-experiment-id'),
                    currentStatus = parseInt($(this).attr('data-status')),
                    newStatus = 0;

                if (currentStatus === 0) {
                    newStatus = 2;
                }

                $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-experiments-switch-status',
                    type: 'POST',
                    data: {
                        experiment_id: experimentId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success === true) {
                            if ($(event.currentTarget).hasClass('op-experiment-status-icon')) {
                                that._updateExperimentStatusUi(experimentId, newStatus, $parent);
                            } else {
                                that._updateExperimentStatusButton(experimentId, newStatus, $(event.currentTarget));
                            }
                        }
                    },
                    dataType: 'json'
                });

                return false;
            };

            /**
             * Reset the new/edit variation form
             * @param  {Object} $container
             * @return {void}
             */
            this.resetTheForm = function($container) {
                // Reset input data
                $container.find('input').val('');

                // Remove variations (leave the first one)
                $container.find('op-experiments-variant:not(:first)').remove();
            };

            /**
             * Show/hide fields depending on goal type.
             * @param  {Array} event
             * @return {void}
             */
            this.toggleGoalTypeFields = function(event) {
                if ($(this).val() === 'optin') {
                    $('.op-experiment-goal-visit').hide();

                    // Destroy select2 on hide
                    if ($('#op_sections_experiments_goal_page').data('select2')) {
                        $('#op_sections_experiments_goal_page').select2('destroy');
                    }
                } else {
                    $('.op-experiment-goal-visit').show();
                    that.bindAutoComplete($('#op_sections_experiments_goal_page'), false);
                }
            };

            /**
             * Update experiment status UI.
             * @param  {integer} experimentId
             * @param  {integer} status
             * @param  {jQuery} $parent
             * @return {void}
             */
            this._updateExperimentStatusUi = function(experimentId, status, $parent) {
                var $labelElement = $parent.find('.op-experiments-experiment-status-label'),
                    $buttonElement = $parent.find('.op-experiments-experiment-status-button'),
                    label = parseInt(status) === 2 ? 'Live' : 'Paused';

                $labelElement.html('<span class="op-experiment-status-' + status + '" data-status="' + status + '" data-experiment-id="' + experimentId + '">' + label + '</span>');
                $buttonElement.html('<a href="#switch-experiment-status" class="op-experiments-switch-status op-experiment-status-icon op-experiment-status-icon-' + status + '" data-experiment-id="' + experimentId + '" data-status="' + status + '"></a>');
            };

            /**
             * Update experiment status edit page button.
             * @param  {integer} experimentId
             * @param  {integer} status
             * @param  {jQuery} $element
             * @return {void}
             */
            this._updateExperimentStatusButton = function(experimentId, status, $element) {
                var label = parseInt(status) === 2 ? 'Live' : 'Paused',
                    icon =  parseInt(status) === 2 ? 'dashicons-controls-pause' : 'dashicons-controls-play';

                $element
                    .attr('data-experiment-id', experimentId)
                    .attr('data-status', status)
                    .html(label + '<span class="dashicons ' + icon + '"></span>');
            }

            /**
             * Update experiment row or append new one at the end of the table.
             * @param  {Array} data
             * @return {void}
             */
            this._updateExperimentRow = function(data) {
                var template = wp.template('op-experiment-row'),
                    $row = $('#op-experiments-table tr[data-experiment-id="' + data.experiment_id + '"]');

                if ($row.length) {
                    // Edit
                    $row.replaceWith(template(data));
                    this._updateExperimentStatusUi(data.experiment_id, data.status, $('#op-experiments-table tr[data-experiment-id="' + data.experiment_id + '"]'));
                } else if ($('#op-experiments-table').length) {
                    // Add
                    $('#op-experiments-table tbody').append(template(data));
                    this._updateExperimentStatusUi(data.experiment_id, data.status, $('#op-experiments-table tr[data-experiment-id="' + data.experiment_id + '"]'));
                    this._incrementRowspan();
                } else {
                    // First experiment
                    this._addExperimentTable();
                    $('#op-experiments-table tbody').append(template(data));
                    this._updateExperimentStatusUi(data.experiment_id, data.status, $('#op-experiments-table tr[data-experiment-id="' + data.experiment_id + '"]'));
                    $('#op-experiments-table tr[data-experiment-id="' + data.experiment_id + '"]').append('<td rowspan="1">' + wp.template('op-experiment-add')() + '</td>');
                }
            };

            /**
             * Increment rowspan.
             * @return {void}
             */
            this._incrementRowspan = function() {
                var $cell = $('#op-experiments-table td[rowspan]');
                $cell.attr('rowspan', parseInt($cell.attr('rowspan')) + 1);
            };

            /**
             * Decrement rowspan.
             * @return {void}
             */
            this._decrementRowspan = function() {
                var $cell = $('#op-experiments-table td[rowspan]');
                $cell.attr('rowspan', parseInt($cell.attr('rowspan')) - 1);
            };

            /**
             * Add experiment table markup (for first experiment).
             * @return {void}
             */
            this._addExperimentTable = function() {
                $('.op-experiments-stats-main-content').html(wp.template('op-experiment-table')());
            };

            /**
             * Fill variant $container with data.
             * @param  {jQuery} $container
             * @param  {Array} data
             * @return {void}
             */
            this._fillVariantFields = function($container, data) {
                $container.find('.op-experiments-page-label').val(data.name);
                $container.find('.op-experiments-variant-page-autocomplete').val(data.page_id);
                $container.find('.op-experiments-page-id').val(data.page_id);

                $container.find('.select2').remove();

                // Attach autocomplete
                that.bindAutoComplete($container.find('.op-experiments-variant-page-autocomplete'));
            };
        };

        var opExperiments = new OpExperiments;
        opExperiments.init();
    });
}(opjq));