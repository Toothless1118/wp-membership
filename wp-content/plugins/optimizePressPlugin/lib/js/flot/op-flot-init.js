jQuery(function() {
    var data = OpStats.data,
        options = {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.9,
                    align: "center",
                    fill: true,
                    fillColor: "rgba(0,74,128,0.8)",
                    lineWidth: 0
                },
                color: "rgba(0,74,128,0.8)",
                highlightColor: "rgba(0,74,128,1.0)"
            },
            xaxis: {
                mode: "categories",
                tickLength: 0
            },
            grid: {
                show: true,
                borderWidth: 0,
                hoverable: true,
                autoHighlight: true
            }
        };

    jQuery(window).on('resize', _.debounce(function() {
        jQuery.plot("#optin_stats_chart", [data], options);
    }, 100)).trigger('resize');
});

var previousPoint = null, previousLabel = null;

function showTooltip(x, y, color, contents) {
    jQuery('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 10,
        left: x - 20,
        border: '1px solid ' + color,
        padding: '3px',
        'font-size': '9px',
        'background-color': '#fff',
        opacity: 0.9
    }).appendTo("body").fadeIn(200);
}

jQuery('#optin_stats_chart').on("plothover", function (event, pos, item) {
    if (item) {
        if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
            previousPoint = item.dataIndex;
            previousLabel = item.series.label;
            jQuery("#tooltip").remove();

            var x = item.datapoint[0];
            var y = item.datapoint[1];

            var color = item.series.color;

            //console.log(item.series.xaxis.ticks[x].label);

            showTooltip(item.pageX,
            item.pageY,
            color,
            y);
        }
    } else {
        jQuery("#tooltip").remove();
        previousPoint = null;
    }
});