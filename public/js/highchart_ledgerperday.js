var xmin = 0;
var xmax = 0;

const chartoptions = {
    chart: {
        zoomType: 'x',
        type: 'spline',
        styledMode: true,
        height: '20%',
        events: {
            load: function () {
                // highlight weekends
                let chart = this,
                    plotBandAr = [],
                    plotBand = {
                        color: '#aa0000',
                    };
                var day = chart.xAxis[0].dataMin;
                while (day <= chart.xAxis[0].dataMax) {
                    // start from the saturday
                    if (new Date(day).getDay() === 6) {
                        plotBand.from = day
                    }
                    // end on the sunday
                    if (new Date(day).getDay() === 0) {
                        plotBand.to = day
                    }
                    // add plotBand on monday and reset the plotBand object
                    if (new Date(day).getDay() === 1) {
                        plotBandAr.push(plotBand)
                        plotBand = {
                            color: '#aa0000',
                        };
                    }
                    // increment by one day
                    day += (3600 * 1000 * 24);
                }
                chart.xAxis[0].update({
                    plotBands: plotBandAr
                })
                // take zoom parameters from master
                if(xmin && xmax) {
                    chart.xAxis[0].setExtremes(
                        xmin,
                        xmax,
                        undefined,
                        false
                    )
                    chart.showResetZoom();
                }
            }
        },
    },
    xAxis: {
        type: 'datetime',
        crosshair: true,
        events: {
            setExtremes: syncExtremes
        },        
    },
    yAxis: [{ // Primary yAxis
        title: {
            text: 'Value (ISK)',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            formatter: function () {
                return (hmN(this.value));
            },
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        opposite: true,
    }, { // Secondary yAxis
        title: {
            text: 'Ammount (pieces)',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        labels: {
            formatter: function () {
                return (hmN(this.value));
            },
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
    }],
    title: {
        text: 'Mining Ledger per day',
        floating: true,
        align: 'center',
        x: 0,
        y: 16
    },
    subtitle: {
        text: 'select area to zoom in',
        floating: true,
        align: 'center',
        x: 0,
        y: 36
    },
    legend: {
        align: 'left',
        verticalAlign: 'top',
        layout: 'vertical',
        floating: true,
        x: 130,
        y: 0
    },
    tooltip: {
        formatter: function () {
            if (typeof(this.points[1]) ==='undefined') {
                return ('&nbsp;');
            }
            return (
                '<tspan style="font-size: 10px">' + Highcharts.dateFormat('%a, %d %b %y', new Date(this.x + (3600 * 1000 * 24))) + '</tspan>' +
                '<tspan class="highcharts-br" dy="15" x="8">​</tspan>' +
                '<tspan class="highcharts-color-0">●</tspan> ISK: <tspan style="font-weight:bold;">' + hmN(this.points[0].y) + '</tspan>' +
                '<tspan class="highcharts-br" dy="15" x="8">​</tspan>' +
                '<tspan class="highcharts-color-1">●</tspan> Pieces: <tspan style="font-weight:bold;">' + hmN(this.points[1].y) + '</tspan>' +
                '<tspan class="highcharts-br" dy="15" x="8">​</tspan>' +
                '<tspan style="font-size: 10px;"><em>' + this.points[0].point.title + '</em></tspan>'
            );
        },
        shared: true
    },
    series: [{
        data: []
    }]
}

function loadNewChart(url, target, title) {
    $.get(url, function (json) {
        res = formatJsData(json, 'last_updated', 'compositionPrice', 'structures');
        chartoptions.series[0] = {
            name: 'ISK - Refined on 100%',
            data: res,
            yAxis: 0,
            type: 'column'
        };

        res = formatJsData(json, 'last_updated', 'pieces');
        chartoptions.series[1] = {
            name: 'Pieces',
            data: res,
            yAxis: 1,
            type: 'column'
        };

        chartoptions.title.text = 'Mining Ledger';
        if (title) {
            chartoptions.title.text += ' for ' + title;
        }

        Highcharts.chart(target, chartoptions);
    });
}


// add a new child row below the one the button has been clicked and load a chart into it
function openChildRow(e, sid, url, structname) {
    var button = $(e).find(".openclose");

    var table = $(e).parents('table').DataTable();
    var row = table.row('#tblrow' + sid);

    var targetdiv = 'highcharttbldiv' + sid;
    if (row.child.isShown()) {
        toggleCss(button, "glyphicon-remove", "glyphicon-stats");
        toggleCss($(e), "btn-primary", "btn-default")
        row.child(false).remove();
    } else {
        toggleCss(button, "glyphicon-stats", "glyphicon-remove");
        toggleCss($(e), "btn-default", "btn-primary")
        row.child('<div id="' + targetdiv + '"></div>').show();
        loadNewChart(url, targetdiv, structname);
    }
}

/**
 * Synchronize zooming for all charts through the setExtremes event handler.
 * from https://jsfiddle.net/BlackLabel/wre6yo7n/
 */
function syncExtremes(e) {
    var thisChart = this.chart;

    if (e.trigger !== 'syncExtremes') { // Prevent feedback loop
        xmin = e.min;
        xmax = e.max;
        Highcharts.each(Highcharts.charts, function (chart) {
            if (chart !== thisChart) {
                if (chart.xAxis[0].setExtremes) { // It is null while updating
                    chart.xAxis[0].setExtremes(
                        e.min,
                        e.max,
                        undefined,
                        false,
                        { trigger: 'syncExtremes' }
                    );
                    chart.showResetZoom();
                }
            }
        });
    }
}

$(document).ready(function () {
    /* 
     * Here goes the real fun
     */
    loadNewChart('/ledger/chartJson', 'highchartdiv', '');
});


function formatJsData(json, xkey, ykey, nameKey = false) {
    var data = [];

    if (json.range.min) {
        data.push({
            x: new Date(json.range.min),
            y: 1,
            title: '-'
        });
    }

    Object.values(json.data).forEach(val => {
        var xval = val[xkey];
        if (typeof val[xkey] === 'object' && val[xkey].date) {
            xval = val[xkey].date;
        }
        var row = {
            x: Date.parse(xval),
            y: parseInt(val[ykey])
        };
        if (nameKey) {
            row.title = val[nameKey];
        }
        data.push(row);
    });

    if (json.range.max) {
        data.push({
            x: new Date(json.range.max),
            y: 1,
            title: '-'
        });
    }

    return data;
}


// format number to human readable number
// https://stackoverflow.com/questions/9461621/format-a-number-as-2-5k-if-a-thousand-or-more-otherwise-900
var SI_SYMBOL = ["", "thousand", "million", "billion", "trillion", "quadrillion", "quintillion"];

function hmN(number) {
    var tier = Math.log10(Math.abs(number)) / 3 | 0;
    if (tier == 0) return number;
    var suffix = SI_SYMBOL[tier];
    var scale = Math.pow(10, tier * 3);
    var scaled = number / scale;
    return scaled.toFixed(1) + ' ' + suffix;
}

function toggleCss (elem, remclass, addclass) {
    elem.removeClass(remclass);
    elem.addClass(addclass);
}