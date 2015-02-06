<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.01.2015
 * Time: 15:03
 */


namespace GWTModule\RPC;


use App\Exception\SQLException;
use MappedClassLoader;
use MappedMethod;
use SerializationPolicy;

class RPC extends \RPC
{
    public static function invokeAndEncodeResponse($target, MappedMethod $serviceMethod, $args,
               SerializationPolicy $serializationPolicy, MappedClassLoader $mappedClassLoader)
    {
        if ($serviceMethod === null) {
            require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
            throw new \NullPointerException("Not found matches serviceMethod (TIP: did you map your service method correctly?");
        }
        if ($serializationPolicy === null) {
            require_once(GWTPHP_DIR.'/maps/java/lang/NullPointerException.class.php');
            throw new \NullPointerException("serializationPolicy");
        }

        try {
            $result = $serviceMethod->invoke($target, $args);
            $responsePayload = RPC::encodeResponseForSuccess($serviceMethod,$result,$serializationPolicy,$mappedClassLoader);

        } catch (\Exception $ex) {
            if ($ex instanceof SQLException) {
                if (!$ex->isVulnerable() || $ex->isBlind()) {
                    $responsePayload = RPC::encodeResponseForFailure($serviceMethod, $ex,
                        $serializationPolicy,$mappedClassLoader);

                } else {
                    header('Content-Type: text/html; charset=utf-8');
                    header("HTTP/1.1 500 Internal Server Error");
                    echo $ex->getMessage() . "\nSQL error:\n" . $ex->getPrevious()->getMessage();
                    return '';
                }

            } else {
                $responsePayload = RPC::encodeResponseForFailure($serviceMethod, $ex,
                    $serializationPolicy, $mappedClassLoader);
            }
        }
        return $responsePayload;
    }
}