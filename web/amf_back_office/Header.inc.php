<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * top link bar
 * 
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 * note: there is a weird rendering error in FF if the news and sign out btns are too near the top. A line of pixels 
 * goes missing or something. So be vcareful when redimensioning
 */
/**+
 * dummy to get rid of phpdocumentor error...
 */
$temp = 0;
?>
<div id="header">
    <div id="line1" class="headerLine">
        <div class="overFlowAuto middleBound"> 
            <p class="alignLeft" id="titleP">
                <span class="titleSpan">Amfphp <span class="backoffice">Back Office</span></span>
            </p>
            <table class="alignRight" style="overflow: hidden">
                    <tr>
                        <td><div onclick="amfphpUpdates.toggleNews()" id="newsLink" class="alignLeft"> 
                                <div id="textNewsLink">Show<br/>News</div>
                            </div></td>
                        <td><div onclick="window.location='SignOut.php';" id="signOutLink" class="alignLeft"> 
                                <div id="textSignOutLink">Sign<br/>Out</div>
                            </div></td>
                    </tr>
                </table>
        </div>
    </div>
    <div id="line2" class="headerLine">
        <div class="overFlowAuto middleBound"> 
            <p class="alignLeft" id="tabNavP">
                <a class="important" href="index.php" id="homeLink">Home</a>
                <a class="important" href="ServiceBrowser.php" id="serviceBrowserLink">Service Browser</a>
                <a class="important" href="ClientGenerator.php" id="clientGeneratorLink">Client Generator</a>
                <a class="important" href="Profiler.php" id="profilerLink">Profiler</a>
            </p>
            <div class="alignRight" id="silexLabsLink" onclick="window.open('http://silexlabs.org','_blank');"></div>
        </div>
    </div>
    <div id="line3" class="overFlowAuto middleBound headerLine">
        <p class="alignLeft" id="tabNameP">
            <span id="tabName"></span>
        </p>   
        <p class="alignRight">
            <span id="currentVersionPre">You are running </span>
            <span id="currentVersion"><?php echo AMFPHP_VERSION; ?></span>
            <br/> 
            <span id="latestVersionInfo">&nbsp;</span>            
        </p>
        <div id="newsPopup">
            <div id="newsPopupTitle">
                <span class="newsDivTitle" id="newsPopupTitleText">Amfphp News</span>
                <p class="alignRight">
                    <a onclick="amfphpUpdates.toggleNews();"><img src="img/Close.png"></img></a>
                </p>
            </div>
        </div>    
    </div>

</div>