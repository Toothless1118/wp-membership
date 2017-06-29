(function($) {
    $(document).ready(function() {
        function OpEditPageStats() {
            var that = this;

            /**
             * Init class.
             */
            this.init = function(pageStats) {
                var viewsChart = that.initLineChart('#page_views', pageStats);

                this.initDateRangePicker('#op-page-stats-daterange').bind('datepicker-closed', function(event, object) {
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
            };

            /**
             * Return page stats through deffered object.
             * @param  {integer} pageId
             * @param {string} startDate expects date in Y-m-d format
             * @param {string} endDate expects date in Y-m-d format
             * @return {Deffered}
             */
            this.getPageStats = function(pageId, startDate, endDate) {
                return $.ajax({
                    url: OptimizePress.ajaxurl + '?action=op-page-stats-get-page',
                    type: 'GET',
                    data: {
                        page_id: pageId,
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json'
                });
            };
        };

        var opEditPageStats = new OpEditPageStats;
        opEditPageStats.init(opInitialPageStats);
    });
}(opjq));