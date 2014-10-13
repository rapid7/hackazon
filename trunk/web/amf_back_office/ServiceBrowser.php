<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
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
    <title>Amfphp Back Office - Service Browser</title>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.hotkeys.js"></script>
        <script type="text/javascript" src="js/jquery.jstree.js"></script>
        <script type="text/javascript" src="js/dataparse.js"></script>
        <script type="text/javascript" src="js/ace/ace.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/services.js"></script>
        <script language="javascript" type="text/javascript" src="js/sb.js"></script>
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

            <div id="main">
                <div id="statusMessage" class="warning"> </div>
                <table id="twoColumnLayout">
                    <tr>
                        <td class="left">
                            <?php
                            if (!$isAccessGranted) {
                                ?>
                                <script>
                                    window.location = "./SignIn.php";
                                </script>
                                <?php
                                return;
                            }
                            ?>
                            <div id="services">
                                <h2>Services and Methods</h2>
                                <ul id='serviceMethods' >
                                    Loading Service Data...

                                </ul>
                            </div>                            
                        </td>
                        <td id="methodCaller" class="right">

                            <h2 id="methodCallerTitle">Method Caller </h2>
                            <h3>1. Select an Amfphp Service Method From the list on the left. 
                                <a id="toggleMethodDescriptionBtn">- Hide Description</a></h3>
                            <div id="methodDescription">
                                <h4 id="serviceHeader"></h4>
                                <span id="serviceComment"></span>
                                <h4 id="methodHeader"></h4>
                                <span id="methodComment"></span>
                            </div>

                            <h3>2. Set your call parameters.</h3>
                            If you need to send a complex parameter, you can use <a target="_blank" href="http://en.wikipedia.org/wiki/JSON">JSON</a> notation.
                            If you want the parameter value to be prefilled, you can propose an example value in your service comments. 
                            <a target="_blank" href="http://www.silexlabs.org/amfphp/documentation/using-the-back-office/service-browser/#parameters">See the documentation here.</a>

                            <div id="methodParameters">
                                <table id="paramDialogs"><tbody></tbody></table>
                                <span  id="noParamsIndicator">This method has no parameters.</span>
                            </div>

                            <h3>3. Call the server.</h3>
                            <div id="methodCall">
                                <table>
                                    <tr>
                                        <td>Simple</td>
                                        <td>Advanced</td>
                                        <td>Call Repeater</td>
                                    </tr>
                                    <tr>
                                        <td><input  type="submit" value="Call" onclick="makeJsonCall()"/>  </td>
                                        <td>
                                            <input  type="submit" value="Call JSON" onclick="makeJsonCall()"/>  
                                            <input  type="submit" value="Call AMF" onclick="makeAmfCall()"/>       
                                        </td>
                                        <td>
                                            <input  type="submit" id="toggleRepeatBtn" value="Start Repeat Call AMF" onclick="toggleRepeat()"/>       
                                            Number of Concurrent Calls
                                            <input id="concurrencyInput" value="1"/>              
                                        </td>
                                    </tr>
                                </table>
                                <div id="amfCallerContainer">
                                    Flash Player is needed to make AMF calls. 
                                    <a target="_blank" href="http://www.adobe.com/go/getflashplayer">
                                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                    </a>
                                </div>                                                            
                            </div>

                            <h3>4. View the server's answer.</h3>
                            <div id="methodCallResult"  >

                                <div class="showResultView">
                                    <a id="tree">Tree</a>
                                    <a id="print_r">print_r</a>
                                    <a id="json">JSON</a>
                                    <a id="php">PHP Serialized</a>
                                    <a id="raw">Raw</a>
                                </div>
                                <div id="dataView">
                                    <div id="tree" class="resultView"></div>
                                    <div id="print_r" class="resultView"></div>
                                    <div id="json" class="resultView"></div>
                                    <div id="php" class="resultView"></div>
                                    <div id="raw" class="resultView"></div>
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>
        </div>            
        <?php
        require_once(dirname(__FILE__) . '/Footer.inc.php');
        ?>        
    </body>    
</html>
