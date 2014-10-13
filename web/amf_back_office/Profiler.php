<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * 
 * note: It would be nice to have logarithmic time rendering, but this doesn't seem to work nicely with horizontal graphs
 * @package Amfphp_BackOffice_Profiler
 * 
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
$config = new Amfphp_BackOffice_Config();
?>

<html>
    <title>Amfphp Back Office - Profiler</title>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="js/jquery.jqplot.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.barRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.categoryAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.enhancedLegendRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.pointLabels.js"></script>
        <script type="text/javascript" src="js/performanceCharting.js"></script>
        <script type="text/javascript" src="js/services.js"></script>

        <script type="text/javascript">
<?php
echo 'var amfphpVersion = "' . AMFPHP_VERSION . "\";\n";
echo 'var amfphpEntryPointUrl = "' . $config->resolveAmfphpEntryPointUrl() . "\";\n";
if ($config->fetchAmfphpUpdates) {
    echo "var shouldFetchUpdates = true;\n";
} else {
    echo "var shouldFetchUpdates = false;\n";
}
?>
        </script>  

    </head>
    <body>
        <div class="page-wrap">
        <?php
        require_once(dirname(__FILE__) . '/Header.inc.php');
        ?>
            <div id='main' >
                <?php
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = './SignIn.php';
                    </script>
                    <?php
                    return;
                }
                ?>
                <div id="performanceDisplay">
                    <div id="controls">
                        <input type="submit" value="Flush" onclick="flush()"></input>
                        <input type="submit" value="Refresh" onclick="refreshClickHandler()"></input>
                        <input type="checkbox" id="flushOnRefreshCb"></input>
                        Flush on refresh
                        <input type="submit" id="toggleAutoRefreshBtn" value="Start Auto Refresh" onclick="toggleAutoRefresh()"></input>
                        Every
                        <input value="1" id="autoRefreshIntervalInput"></input>
                        Seconds<br/>
                        <div id="statusMessage" class="warning"> </div>
                        <div class="imgWrapper" id="profilerImg" style="display:none">
                            <a href="Profiler.php">
                                <img src="img/Profiler.jpg"></img>
                            </a>    
                        </div>
                    </div>
                    <div id="chartDivContainer">
                        <div id="chartDiv"></div>
                    </div>                  
                </div>
            </div>
        </div>            
        <?php
        require_once(dirname(__FILE__) . '/Footer.inc.php');
        ?>            
        <script>
                
            /**
             * the chart
             * */
            var plot;
            /**
             * the full data received from the server
             * */
            var serverData;

            /**
             * the uri of the service method on which the focus is. null when all uris are shown
             * */
            var focusedUri;

            var isAutoRefreshing;

            /**
             *auto refresh timer
             **/
            var timer;
            
            /**
             * time names ordered by time
             * */
            var orderedTimeNames; 


            $(function () {	
                amfphp.entryPointUrl = amfphpEntryPointUrl + "?contentType=application/json";
                $("#tabName").html("Profiler &nbsp;&nbsp;<a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/' target='_blank'>?</a>");
                $("#profilerLink").addClass("chosen");

                var availableHeight = $( "body" ).height() - $("#chartDiv").offset().top - 140;
                $( "#chartDiv" ).css( "height", availableHeight +  "px" );
    
                refresh();
                isAutoRefreshing = false;
                if (shouldFetchUpdates) {
                    amfphpUpdates.init("#newsPopup", "#newsLink", "#textNewsLink", "#latestVersionInfo");
                    amfphpUpdates.loadAndInitUi();
                }

            });    

            function displayStatusMessage(html){
                $('#statusMessage').html(html);
            }


            /**
             * callback for when performance data loaded from server . 
             * generates graph with consolidated data
             */
            function onDataLoaded(data)
            {
    
    
                if(data.sortedData.length == 0){
                    showErrorMessage("No data was available. Please make a service call then refresh. This can be done with the <a href='ServiceBrowser.php'>Service Browser</a>.");
                    return;
                }
    
                serverData = data;
    
                displayStatusMessage('');
                $("#profilerImg").hide();
                console.log($("#chartDivContainer"));
                $("#chartDivContainer").show();
    
                if(focusedUri && data.sortedData && data.sortedData[focusedUri]){
                    focusOnUri(focusedUri);
                }else{
                    showAllUris();
                }
                
                if(data.serverComment){
                    displayStatusMessage(data.serverComment);
                }
    
            }
            
            function showErrorMessage(errorMsg){
                errorMsg += "<br/>Once you have some data, this is what you should see : ";
                displayStatusMessage(errorMsg);
                $("#profilerImg").show();
                $("#chartDivContainer").hide();
            }

            function showAllUris(){
                if(plot){
                    plot.destroy();
                }
                focusedUri = null;

                var seriesData = [];
                var ticks = [];
                var ignoredUris = [];
                var missingTimes = [];
                var missingTimesAssoc = {};
                var orderedTimedNamesSet = false;
                orderedTimeNames = [];
    
                if(serverData.sortedData.length == 0){
                    return;
                }
                //here a uri referes to a service method, as that is what is used to sort the data
                for(var uri in serverData.sortedData){
                    //data for each target uri
                    var rawUriData = serverData.sortedData[uri];
                    var formattedUriData = [];
                    //sanity check: ignore uri if a time is missing
                    var ignoreUri = false;
                    for(var i = 0; i < serverData.timeNames.length; i++){
                        var expectedTimeName = serverData.timeNames[i];
                        if(!rawUriData.hasOwnProperty(expectedTimeName)){
                            ignoreUri = true;
                            if(!missingTimesAssoc.hasOwnProperty(expectedTimeName)){
                                //use an associative array to make sure we don't have duplcate missing time names'
                                missingTimesAssoc[expectedTimeName] = '';
                                missingTimes.push(expectedTimeName);
                            }
                        }
                    }
                    if(ignoreUri){
                        ignoredUris.push(uri);
                        continue;
                    }

                    //look at data for each time 
                    for(var timeName in rawUriData){

                        var timeData = rawUriData[timeName];
                        //calculate average duration
                        var numTimes = timeData.length;
                        var totalDuration = 0;
                        if(numTimes == 0){
                            //should never happen, but as we divide by totalDuration below, better to check
                            continue;
                        }
                        for(var i = 0; i < numTimes; i++)
                        {
                            totalDuration = totalDuration + timeData[i];
                        }
                        formattedUriData.push(Math.round(totalDuration / numTimes));
                        //first time round grab the time names for series labels
                        if(!orderedTimedNamesSet){
                            orderedTimeNames.push(timeName);
                        }
                    }
                    seriesData.push(formattedUriData);
					if(uri == ''){
						ticks.push("Empty Call");
					}else{
	                    ticks.push(uri);
					}
                    orderedTimedNamesSet = true;
                }

                //as chart is horizontal, data must be flipped. This is a jqplot design choice or limitation it seems
                var flippedSeriesData = [];

                for(var i = 0; i < seriesData[0].length; i++){
                    flippedSeriesData.push([]);
                    for(var j = 0; j < seriesData.length; j++){
                        flippedSeriesData[i].push(seriesData[j][i]);
                    }
                }

                if(ignoredUris.length > 0){
                    var message = "The following service methods were ignored because their data does not have all expected times: ";
                    message += ignoredUris.join(', ');
                    message += "<br/>The missing times are the following : ";
                    message += missingTimes.join(', ');
                    message += "<br/>For a service method to appear in the chart make sur it logs those times.<br/>";
                    $("#statusMessage").html(message);
                }
                var titleHtml = "Average durations for all calls (ms)";
    
                plot = buildChart("chartDiv", flippedSeriesData, ticks, getLegendLabels(orderedTimeNames), titleHtml, getSeriesColors(orderedTimeNames));
                addLabelListeners();

            }


            /**
             * show data for 1 call uri.
             * note: only the first 20 calls are shown, so as not to drown the browser.
             */
            function focusOnUri(uri){
                if(plot){
                    plot.destroy();
                }
                focusedUri = uri;
                var seriesData = [];
                orderedTimeNames = [];
                var ticks = [];
    
                //data for each target uri
                var rawUriData;
				if(uri == "Empty Call"){
					rawUriData = serverData.sortedData[""];
				}else{
					rawUriData = serverData.sortedData[uri];
				}
					
    
                var i = 0;
                //look at data for each time 
                for(var timeName in rawUriData){
                    var timeData = rawUriData[timeName];
                    timeData = timeData.slice(0, 20);
                    orderedTimeNames.push(timeName);
                    seriesData.push(timeData.reverse());

                }
                
                //need to inverse ticks otherwise '1' is at the bottom
                var numRows = seriesData[0].length;
                for(i = 0; i < numRows; i++){
                    ticks.push(numRows - i);
                }
                var titleHtml = '<a onclick="showAllUris()">Average durations for all calls(ms)</a>&nbsp;> ' + focusedUri;

                plot = buildChart("chartDiv", seriesData, ticks, getLegendLabels(orderedTimeNames), titleHtml, getSeriesColors(orderedTimeNames));
                addLabelListeners();
            }

            function addLabelListeners(){
                $('.jqplot-yaxis-tick')
                .css({ cursor: "pointer", zIndex: "1" })
                .click(function (ev) { 
                    focusOnUri($(this).text());           


                });
                
                $('#chartDiv').bind('jqplotDataClick', 
                    function (ev, seriesIndex, pointIndex, data) {
                        var message = "";
                        if(!focusedUri){
                            message = "Average ";
                        }
                        message += orderedTimeNames[seriesIndex] + ' Duration : '+data[0] + ' ms';
                        $('#statusMessage').html(message);
                    }
                );     
                
            }
            function refreshClickHandler(){
                refresh();
            }
            /**
             * load data, and optionally flush
             */
            function refresh(){ 
                var flush = $("#flushOnRefreshCb").is(':checked');
                amfphp.services.AmfphpMonitorService.getData(onDataLoaded, onServerError, flush);
    
            }
            
            /**
             * flush monitor data on server.
             */
            function flush(){
                amfphp.services.AmfphpMonitorService.flush(function(){
                    displayStatusMessage("Data Flushed");
                }, onServerError);
    
            }


            /**
             * start and stop auto refresh
             */
            function toggleAutoRefresh(){
                if(!isAutoRefreshing){
                    var interval = parseInt($("#autoRefreshIntervalInput").val());
                    if(isNaN(interval)){
                        alert("Invalid auto refresh interval");
                        return;
                    }
                }
    
                isAutoRefreshing = !isAutoRefreshing;
                if(isAutoRefreshing){
                    timer = setInterval(refresh, interval * 1000);
                    $("#toggleAutoRefreshBtn").prop("value", "Stop Auto Refresh");
        
                }else{
                    clearInterval(timer);
                    $("#toggleAutoRefreshBtn").prop("value", "Start Auto Refresh");
        
                }
    
            }

            
            function onServerError(jqXHR, textStatus ){
                var responseText = jqXHR.responseText;
                responseText = "Error communicating with the server at " + amfphp.entryPointUrl + ": <br/>" + responseText;
                if(responseText.indexOf("AmfphpMonitorService service not found") != -1){
                    showErrorMessage("The AmfphpMonitorService could not be called. This is most likely because AmfphpMonitor plugin is not enabled. See the <a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/'>documentation</a>.");
                    return;
                }

                var errorMessagePos = responseText.indexOf("AmfphpMonitor does not have permission to read and write");
                if(errorMessagePos != -1){
                    var filePathStart = responseText.indexOf("log file: ", errorMessagePos) + 10;
                    var filePathStop = responseText.indexOf("'", filePathStart);
                    var filePath = responseText.substring(filePathStart, filePathStop);
                    showErrorMessage("Could not read or write log file. Please check your webserver has read and write permissions on <br/>" + filePath);
                    return;
                }


                showErrorMessage(textStatus + "<br/><br/>" + responseText);
                
            }
        </script>
