<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice_ClientGenerator
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
    <title>Amfphp Back Office - Client Generator</title>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" href="css/style.css" />
        
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <script type="text/javascript" src="js/services.js"></script>
        <script type="text/javascript">
<?php 
    echo 'var amfphpVersion = "' . AMFPHP_VERSION . "\";\n"; 
    echo 'var amfphpEntryPointUrl = "' . $config->resolveAmfphpEntryPointUrl() . "\";\n"; 
    if ($config->fetchAmfphpUpdates) {
        echo "var shouldFetchUpdates = true;\n"; 
    }else{
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
                <div id="clientGenContent">
                    <div id="statusMessage" class="warning"></div>
                    
                    <h2>Generate a client project that consumes your services. </h2>
                    Use one of the following generators to generate a client Stub project. 
                    <br/>The project includes :<br/>
                    <ul>
                        <li>code to make calling your services easy</li>
                        <li>a starting point for a user interface you can customize</li>
                    </ul>
                    <?php 
                    $writeTestFolder = AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/Generated/';
                    if(!is_writable($writeTestFolder)){
                        echo "WARNING: could not write to ClientGenerator/Generated/. <br/> You need to change your permissions to be able to use the client generator.<br/><br/>";
                    }

                    ?>
                    Code shall be generated for the following services:
                    <br/>
                    <ul id="serviceList"></ul>
                    <?php
                    $generatorManager = new Amfphp_BackOffice_ClientGenerator_GeneratorManager();
                    $generators = $generatorManager->loadGenerators(array('ClientGenerator/Generators'));

 
    //links for each generator
                    echo "\n<table class='borderTop' id='clientGen'>";
                    foreach ($generators as $generator) {
                        echo "\n    <tr>";
                        $generatorName = $generator->getUiCallText();
                        $generatorClass = get_class($generator);
                        $infoUrl = $generator->getInfoUrl();
                        echo "\n        <td>$generatorName</td>";
                        echo "\n        <td><a href=\"$infoUrl\">Info</a></td>";
                        echo "\n        <td><a onclick='generate(\"" . $generatorClass . "\")'>Generate!</a></td>";
                        echo "\n    </tr>";
                    }
                    ?>
                        <tr><td class="borderTop">IOS</td><td class="borderTop"><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/ios/">Info/Vote Up</a></td>        <td class="borderTop">Not Available Yet</td>    </tr>
                        <tr><td>Haxe</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/haxe/">Info/Vote Up</a></td>        <td>Not Available Yet</td>    </tr>
                        <tr><td>Android</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/android/">Info/Vote Up</a></td>        <td>Not Available Yet </td></tr>
                        <tr class="borderTop"><td class="borderTop">Write your Own?</td><td class="borderTop"><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/writing-you-own-client-generator/">Info</a></td>        <td class="borderTop"></td></tr>
                    </table>
                </div>
            </div>
        </div>
                
        <?php
        require_once(dirname(__FILE__) . '/Footer.inc.php');
        ?>
            <script>
$(function () {	   
    amfphp.entryPointUrl = amfphpEntryPointUrl + "?contentType=application/json";   
    $("#tabName").html("Client Generator  &nbsp;&nbsp;<a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/client-generator/' target='_blank'>?</a>");
    
    $("#clientGeneratorLink").addClass("chosen");

    amfphp.entryPointUrl = amfphpEntryPointUrl + "?contentType=application/json";
    amfphp.services.AmfphpDiscoveryService.discover(onServicesLoaded, function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });


});    



function displayStatusMessage(html){
    $('#statusMessage').html(html);
}

/**
 * callback for when service data loaded from server . 
 * generates method list. 
 */
function onServicesLoaded(data)
{
    if(typeof data == "string"){
        displayStatusMessage(data);
        return;
    }
    if(data.length == 0){
        displayStatusMessage("No Services were found on the server. If this is a new installation, create a class in Amfphp/Services/ and it should appear here.");
    }    
    serviceData = data;

    //generate service/method list
    var rootUl = $("ul#serviceList");
    $(rootUl).empty();
    for(serviceName in serviceData){
        var service = serviceData[serviceName];
        var serviceLi = $("<li>" + serviceName + "</li>")
        .appendTo(rootUl);
        $(serviceLi).attr("title", service.comment);
        $("<ul/>").appendTo(serviceLi);
    }
    if (shouldFetchUpdates) {
        //only load update info once services loaded(that's the important stuff)
        amfphpUpdates.init("#newsPopup", "#newsLink", "#textNewsLink", "#latestVersionInfo");
        amfphpUpdates.loadAndInitUi();
    }
    
}

function generate(generatorClass){
    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
    var request = $.ajax({
      url: "ClientGeneratorBackend.php?generatorClass=" + generatorClass,
      type: "POST",
      data: JSON.stringify(serviceData)
    });

    request.done(onGenerationDone);

    request.fail(function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });


}

function onGenerationDone(data){
    $('#statusMessage').html(data);
    var n = $(document).height();
    $('html, body').animate({ scrollTop: n }, 50);    
}

</script>
