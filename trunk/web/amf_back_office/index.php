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
 * entry point for back office 
 * @author Ariel Sommeria-klein
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

    <title>AmfPHP Back Office</title>    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
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
            <?php require_once(dirname(__FILE__) . '/Header.inc.php'); ?>
            <div id='main' >
                <?php
                $accessManager = new Amfphp_BackOffice_AccessManager();
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = './SignIn.php';
                    </script>
                    <?php
                    return;
                }
                ?>
                <div id="tabsExplanation">

                    <h2>Welcome to the <span class="titleSpan">Amfphp <span class="backoffice">Back Office</span></span></h2>
                    <span id="keyMessage">Here you can access the 3 parts of the Back Office. </span>
                    <br/><br/>
                    <h3>The Service Browser</h3>
                    The service browser allows you to test your services. It lists all services and methods. Click a service method, and a dialog will appear allowing you to call it and optionally set some parameters.
                    Once you call the method, you have a choice of ways to display the return data. Depending on what kind of return data you are expecting you will find one view or another more useful.
                    <br/><a target="_blank" href="http://silexlabs.org/amfphp/documentation/using-the-back-office/service-browser/">Service Browser Documentation</a>
                    <div class="imgWrapper">
                        <a href="ServiceBrowser.php">
                            <img src="img/ServiceBrowser.jpg"></img>
                        </a>
                    </div>
                    <br/><br/>
                    <h3>The Client Generator</h3>
                    The client generator allows you to generate fully functional client projects including:
                    <ul>
                        <li>service classes that expose your service methods so that you can call them easily.</li>
                        <li>a GUI class to access each service. These are great to make back offices.</li>
                        <li>project files to wrap them all and hit the ground running.</li>
                    </ul>
                    <a target="_blank" href="http://silexlabs.org/amfphp/documentation/using-the-back-office/client-generator/">Client Generator Documentation</a>
                    <div class="imgWrapper">
                        <a href="ClientGenerator.php">
                            <img src="img/ClientGenerator.jpg"></img>
                        </a>    
                    </div>
                    <br/><br/>
                    <h3>The Profiler</h3>
                    The Profiler allows you to observe the time spent by each service call in the different stages of processing. 
                    The idea is to help you better understand how your server shall perform live, 
                    and to give you easy access to the information you need to eliminate bottlenecks and fine-tune performance.
                    <br/><a target="_blank" href="http://silexlabs.org/amfphp/documentation/using-the-back-office/profiler/">Profiler Documentation</a>
                    <div class="imgWrapper">
                        <a href="Profiler.php">
                            <img src="img/Profiler.jpg"></img>
                        </a>    
                    </div>

                </div>
            </div>
        </div>            
        <?php require_once(dirname(__FILE__) . '/Footer.inc.php'); ?>

        <script>
            $(function () {	        
                    
                if (shouldFetchUpdates) {
                    amfphpUpdates.init("#newsPopup", "#newsLink", "#textNewsLink", "#latestVersionInfo");
                    amfphpUpdates.loadAndInitUi();
                }
                $("#tabName").text("Home");
                $("#homeLink").addClass("chosen");
                    
            });
            

            
        </script>

    </body>    
</html>

