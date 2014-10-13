function buildChart(targetDivId, seriesData, ticks, legendLabels, titleHtml, seriesColors){
    var numRows = seriesData[0].length;
    var rendererOptions = {
        barDirection: 'horizontal',
        highlightMouseDown:true
    };
    if(numRows < 5){
        rendererOptions.barWidth = 70;
    }        
    var plot = $.jqplot(targetDivId, seriesData, {
        // Tell the plot to stack the bars.
        stackSeries: true,
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: rendererOptions,
            pointLabels: {
                show: true, 
                hideZeros:true, 
                edgeTolerance:5
            },
            shadow:false,
            fillAlpha:0.5
            
        },
        axes: {

            yaxis: {
                // Don't pad out the bottom of the data range.  By default,
                // axes scaled as if data extended 10% above and below the
                // actual range to prevent data points right on grid boundaries.
                // Don't want to do that here.
                padMin: 0,
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks,
                tickOptions: {
                    fontSize: '12pt'
                }
            }
        },
        legend: {
            show: true,
            location: 's',
            placement: 'outsideGrid',
            labels:legendLabels,
            renderer: $.jqplot.EnhancedLegendRenderer,
            rendererOptions: {
                numberRows: 1,
                seriesToggle:false
            }            
        },
        title:{
            text:titleHtml
        },
        seriesColors: seriesColors,
        grid:{
            shadow:false
        }
    });
    return plot;
    
}


/**
 * process ordered names to create labels linked to the doc
 * */
function getLegendLabels(orderedTimeNames){
    var labels = [];
    for(var i = 0; i < orderedTimeNames.length; i++){
        var timeName = orderedTimeNames[i];
        var label;
        switch(timeName){
            case "Deserialization": 
            case "Serialization":
                label = timeName  + " <a target='_blank' href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/#serialization'>( ? )</a>";
                break;
            case "Request VO Conversion":
            case "Response VO Conversion":
                label = timeName  + " <a target='_blank' href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/#value object conversion'>( ? )</a>";
                break;
            case "Request Charset Conversion":
            case "Response Charset Conversion":
                label = timeName  + " <a target='_blank' href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/#charset conversion'>( ? )</a>";
                break;
            case "Service Call":
               label = timeName  + " <a target='_blank' href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/#service call'>( ? )</a>";
                break;
            default:
                label = "CUSTOM " + timeName + " (<a target='_blank' href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/#more data'>( ? )</a>)";
        }
        labels.push(label);
    }
    return labels;
}



/**
 * get some colors from the series
 * some are predefined as they are standard for amfphp.
 * Some are generated using stringToColor
 * */
function getSeriesColors(orderedTimeNames){
    
    var colors = [];
    var customTimeToggle = false;
    for(var i = 0; i < orderedTimeNames.length; i++){
        var timeName = orderedTimeNames[i];
        var color;
        switch(timeName){
            case "Deserialization":
                color = "#00C800";
                break;
            case "Request VO Conversion":
                color = "#168DE6";
                break;
            case "Request Charset Conversion":
                color = "#FF9978";
                break;
            case "Service Call":
                color = "#FFCC01";
                break;
            case "Response Charset Conversion":
                color = "#FF551C";
                break;
            case "Response VO Conversion":
                color = "#104E80";
                break;
            case "Serialization":
                color = "#009100";
                break;
            default:
                //alternate these 2 colors for custom times
                if(customTimeToggle){
                    color = "#E506D3"; 
                }else{
                    color = "#950079"; 
                } 
                customTimeToggle = !customTimeToggle;
        }
        colors.push(color);
    }
    return colors;
    
}



