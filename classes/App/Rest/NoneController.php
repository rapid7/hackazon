<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 17:02
 */


namespace App\Rest;


class NoneController extends Controller
{
    public function action_get()
    {
        // TODO: output possible links to resources
        $this->response->body = [
//            'orders' => $this->prefix.'orders',
//            'products' => $this->prefix.'products'
        ];
    }

    public static function allowedMethods()
    {
        return ['GET', 'HEAD', 'OPTIONS'];
    }
} 