(function($) {
    $(document).ready(function() {
        function OpExperimentStats() {
            var that = this;

            /**
             * Init class.
             */
            this.init = function() {
                this.initEvents();
            };

            /**
             * Init DOM events.
             * @return {void}
             */
            this.initEvents = function() {
                $('#op-experiment-stats').on('click', '.op-stats-experiment-details-trigger', this.showExperimentDetails);
                $('#op-stats-edit-page-experiment-container').on('click', '.op-stats-experiment-details-trigger', this.showExperimentDetails);
            };

            /**
             * Show experiment detailed stats in an overlay.
             * @param  {Array} event
             * @return {boolean}
             */
            this.showExperimentDetails = function(event) {
                var experimentId = $(this).attr('data-experiment-id'),
                    template = wp.template('op-stats-experiment-details');

                that.getExperiment(experimentId).then(function(response) {
                    OptimizePress.disable_alert = true;
                    $.fancybox($.extend({}, OptimizePress.fancybox_defaults, {
                        minWidth: 800,
                        minHeight: 500,
                        content: template({
                            experiment: response.data.experiment
                        })
                    }));

                    var barChart = that.initBarChart('#op-experiment-variations-bar', response.data.stats);

                    that.getExperimentStats(experimentId, 'conversions').then(function(response) {
                        var lineChart = that.initLineChart('#op-experiment-variations-line', response.data);

                        var dateRangePicker = that.initDateRangePicker('#op-stats-daterange');
                        dateRangePicker.bind('datepicker-closed', function(event) {
                            var dates = $('#op-stats-daterange').val().split(' - '),
                                stat = $('#op-stats-stat').val();

                            that.getExperimentStats(experimentId, stat, dates[0], dates[1]).then(function(response) {
                                lineChart.destroy();
                                lineChart = that.initLineChart('#op-experiment-variations-line', response.data);
                            });
                        });

                        // Trigger window resize to fix Fancbox not showing scroll-Y
                        $(window).trigger('resize');

                        $('#op-stats-stat').on('change', function(event) {
                            var dates = $('#op-stats-daterange').val().split(' - '),
                                stat = $('#op-stats-stat').val();

                            that.getExperimentStats(experimentId, stat, dates[0], dates[1]).then(function(response) {
                                lineChart.destroy();
                                lineChart = that.initLineChart('#op-experiment-variations-line', response.data);
                            });
                        });
                    });
                });

                return false;
            }

            /**
             * Return experiment through deffered object.
             * @param  {integer} experimentId
             * @return {Deffered}
             */
            this.getExperiment = function(experimentId) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-stats-get-experiment',
                    type: 'GET',
                    data: {
                        experiment_id: experimentId
                    },
                    dataType: 'json'
                });
            };

            /**
             * Return experiment stats through deffered object.
             * @param {integer} experimentId
             * @param {string} stat
             * @param {string} startDate expects date in Y-m-d format
             * @param {string} endDate expects date in Y-m-d format
             * @return {Deffered}
             */
            this.getExperimentStats = function(experimentId, stat, startDate, endDate) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-stats-get-experiment-stats',
                    type: 'GET',
                    data: {
                        experiment_id: experimentId,
                        start_date: startDate,
                        end_date: endDate,
                        stat: stat
                    },
                    dataType: 'json'
                });
            };


            /**
             * Init bar chart and fill it with initial data.
             * @param  {string} selector jQuery selector
             * @param  {Array} data
             * @return {Chart}
             */
            this.initBarChart = function(selector, data) {
                return new Chart($(selector).get(0).getContext("2d"), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: "Views",
                            backgroundColor: OpChartOptions.colors.light[0],
                            borderColor: OpChartOptions.colors.dark[0],
                            borderWidth: 2,
                            data: data.views,
                            lineTension: 0
                        }, {
                            label: "Unique",
                            backgroundColor: OpChartOptions.colors.light[1],
                            borderColor: OpChartOptions.colors.dark[1],
                            borderWidth: 2,
                            data: data.unique,
                            lineTension: 0
                        }, {
                            label: "Conversions",
                            backgroundColor: OpChartOptions.colors.light[2],
                            borderColor: OpChartOptions.colors.dark[2],
                            borderWidth: 2,
                            data: data.conversions,
                            lineTension: 0
                        }]
                    },
                    options: {
                        animation: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            }

            /**
             * Init line chart and fill it with initial data.
             * @param  {string} selector jQuery selector
             * @param  {Array} data
             * @return {Chart}
             */
            this.initLineChart = function(selector, data) {
                return new Chart($(selector).get(0).getContext("2d"), {
                    type: 'line',
                    data: data,
                    options: {
                        animation: false
                    }
                });
            }

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
        };

        var opExperimentStats = new OpExperimentStats;
        opExperimentStats.init();
    });
}(opjq));