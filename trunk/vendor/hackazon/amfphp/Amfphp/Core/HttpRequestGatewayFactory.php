<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * A gateway factory's job is to create a gateway. There can be many gateway factories, but as such the only one for now is this one,
 * which creates a gateway assuming that the data to be processed is in an http request and thus available through the usual php globals
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-Klein
 */
class Amfphp_Core_HttpRequestGatewayFactory {



    /**
     * there seems to be some confusion in the php doc as to where best to get the raw post data from.
     * try $GLOBALS['HTTP_RAW_POST_DATA'] and php://input
     *
     * @return <String> it's a binary stream, but there seems to be no better type than String for this.
     */
    static protected function getRawPostData(){
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            return $GLOBALS['HTTP_RAW_POST_DATA'];
        }else{
            return file_get_contents('php://input');
        }

    }

    /**
     * create the gateway.
     * content type is recovered by looking at the GET parameter contentType. If it isn't set, it looks in the content headers.
     * @param Amfphp_Core_Config $config optional. If null, the gateway will use the default
     * @return Amfphp_Core_Gateway
     */
    static public function createGateway(Amfphp_Core_Config $config = null){
        $contentType = null;
        if(isset ($_GET['contentType'])){
            $contentType = $_GET['contentType'];
        }else if(isset ($_SERVER['CONTENT_TYPE'])){

            $contentType = $_SERVER['CONTENT_TYPE'];
        }
        $rawInputData = self::getRawPostData();
        return new Amfphp_Core_Gateway($_GET, $_POST, $rawInputData, $contentType, $config);
    }
}
?>
