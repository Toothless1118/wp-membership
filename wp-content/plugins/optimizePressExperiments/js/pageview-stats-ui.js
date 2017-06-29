(function($) {
    $(document).ready(function() {
        function OpPageViewStats() {
            var that = this,
                itemsPerPage = 20;

            /**
             * Init class.
             */
            this.init = function() {
                this.initEvents();
                this.initListFilter();
            };

            /**
             * Init DOM events.
             * @return {void}
             */
            this.initEvents = function() {
                $('#op-stats-pageviews').on('click', '.op-stats-pageviews-page-details', this.showPageDetails);
                $('.op-stats-pageviews-container').on('click', '.first-page, .prev-page, .next-page, .last-page', this.paginate);
            };

            /**
             * Init page list filter (using date range picker).
             * @return {void}
             */
            this.initListFilter = function() {
                var dateRangePicker = this.initDateRangePicker('#op-stats-list-daterange');
                dateRangePicker.bind('datepicker-closed', function(event, object) {
                    var dates = $(event.currentTarget).val().split(' - ');

                    that.showPagesStats(dates[0], dates[1], 0);
                });
            };

            this.paginate = function(event) {
                var dates = $('#op-stats-list-daterange').val().split(' - '),
                    offset = parseInt($(event.currentTarget).attr('data-page')) * itemsPerPage - itemsPerPage;

                that.showPagesStats(dates[0], dates[1], offset);

                return false;
            }

            this.showPagesStats = function(startDate, endDate, offset) {
                var $table = $('#op-stats-pageviews tbody'),
                    currentPage = parseInt((itemsPerPage + offset) / itemsPerPage);

                // Clear data
                $table.find('tr').remove();
                $('.tablenav').remove();

                that.getStats(startDate, endDate, offset).then(function(response) {
                    if (false === response.success) {
                        var noDataTemplate = wp.template('op-stats-page-list-no-data');

                        $table.append(noDataTemplate());
                    } else {
                        var itemTemplate = wp.template('op-stats-page-list-row'),
                            totalPages = parseInt(Math.ceil(response.data.total / itemsPerPage));

                        // Render each data row
                        $.each(response.data.items, function(index, item) {
                            $table.append(itemTemplate(item));
                        });

                        // Show pagination
                        if (response.data.total > itemsPerPage) {
                            var paginationTemplate = wp.template('op-stats-page-list-pagination');
                            $table.parent().after(paginationTemplate({currentPage: currentPage, totalPages: totalPages}));
                        }
                    }
                });
            };

            /**
             * Init date range picker.
             * @param  {string} selector jQuery selector
             * @return {dateRangePicker}
             */
            this.initDateRangePicker = function(selector) {
                return $(selector).dateRangePicker({
                    separator: ' - ',
                    autoClose: true,
                    showShortcuts: true,
                    shortcuts: {
                        'prev-days': [7, 30],
                        'next-days': null,
                        'next': null,
                        'prev': ['week', 'month']
                    }
                });
            };

            /**
             * Show page stats in an overlay.
             * @param  {Array} event
             * @return {boolean}
             */
            this.showPageDetails = function(event) {
                var pageId = $(this).attr('data-page-id'),
                    template = wp.template('op-stats-page-details');

                that.getPageStats(pageId).then(function(response) {
                    if (response.success === false) {
                        alert('No data for current time period.');
                        return;
                    }

                    OptimizePress.disable_alert = true;
                    $.fancybox($.extend({}, OptimizePress.fancybox_defaults, {
                        minWidth: 800,
                        minHeight: 500,
                        content: template({
                            form_title: "Stats for: " + response.data.page.title,
                            page_id: pageId,
                            views: response.data.stats.sum.views,
                            unique: response.data.stats.sum.unique,
                            conversions: response.data.stats.sum.conversions,
                            conversion_rate: response.data.stats.sum.conversion_rate
                        })
                    }));

                    var viewsChart = that.initLineChart('#page_views', response.data.stats);

                    var dateRangePicker = that.initDateRangePicker('#op-stats-daterange');
                    dateRangePicker.bind('datepicker-closed', function(event, object) {
                        var dates = $(event.currentTarget).val().split(' - '),
                            pageId = $(event.currentTarget).attr('data-page-id');

                        that.getPageStats(pageId, dates[0], dates[1]).then(function(response) {
                            $('#op-stats-page-sum-views').html(response.data.stats.sum.views);
                            $('#op-stats-page-sum-unique').html(response.data.stats.sum.unique);
                            $('#op-stats-page-sum-conversions').html(response.data.stats.sum.conversions);
                            $('#op-stats-page-sum-conversion-rate').html(response.data.stats.sum.conversion_rate + '%');

                            viewsChart.destroy();
                            viewsChart = that.initLineChart('#page_views', response.data.stats);
                        });
                    });
                });

                return false;
            }

            /**
             * Init chart and fill it with initial data.
             * @param  {string} selector jQuery selector
             * @param  {Array} data
             * @return {Chart}
             */
            this.initLineChart = function(selector, data) {
                return new Chart($(selector).get(0).getContext("2d"), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: "Views",
                            backgroundColor: OpChartOptions.colors.light[0],
                            borderColor: OpChartOptions.colors.dark[0],
                            data: data.views,
                            lineTension: 0
                        }, {
                            label: "Unique",
                            backgroundColor: OpChartOptions.colors.light[1],
                            borderColor: OpChartOptions.colors.dark[1],
                            data: data.unique,
                            lineTension: 0
                        }, {
                            label: "Conversions",
                            backgroundColor: OpChartOptions.colors.light[2],
                            borderColor: OpChartOptions.colors.dark[2],
                            data: data.conversions,
                            lineTension: 0
                        }]
                    },
                    options: {
                        animation: false
                    }
                });
            }

            /**
             * Return page stats through deffered object.
             * @param  {integer} pageId
             * @param {string} startDate expects date in Y-m-d format
             * @param {string} endDate expects date in Y-m-d format
             * @return {Deffered}
             */
            this.getPageStats = function(pageId, startDate, endDate) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-stats-get-page',
                    type: 'GET',
                    data: {
                        page_id: pageId,
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json'
                });
            };

            /**
             * Return list of pages with their stats between given dates (through deffered object).
             * @param  {string} startDate expects date in Y-m-d format
             * @param  {string} endDate   expects date in Y-m-d format
             * @return {Deffered}
             */
            this.getStats = function(startDate, endDate, offset, limit) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-stats-get-pages',
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        offset: offset || 0,
                        limit: limit || itemsPerPage
                    },
                    dataType: 'json'
                });
            };
        };

        var opPageViewStats = new OpPageViewStats;
        opPageViewStats.init();
    });
}(opjq));