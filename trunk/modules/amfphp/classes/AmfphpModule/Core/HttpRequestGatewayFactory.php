<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.01.2015
 * Time: 16:15
 */


namespace AmfphpModule\Core;


use Amfphp_Core_Config;
use App\Pixie;

/**
 * @inheritdoc
 */
class HttpRequestGatewayFactory extends \Amfphp_Core_HttpRequestGatewayFactory
{
    /**
     * @inheritdoc
     */
    static public function createGatewayEx(Pixie $pixie, Amfphp_Core_Config $config = null)
    {
        $contentType = null;
        if(isset ($_GET['contentType'])){
            $contentType = $_GET['contentType'];
        }else if(isset ($_SERVER['CONTENT_TYPE'])){

            $contentType = $_SERVER['CONTENT_TYPE'];
        }
        $rawInputData = self::getRawPostData();
        return new Gateway($pixie, $_GET, $_POST, $rawInputData, $contentType, $config);
    }
}