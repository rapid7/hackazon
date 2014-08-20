<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 15:23
 */


namespace App\Rest;


use App\EventDispatcher\EventDispatcher;
use App\Events\GetResponseEvent;
use App\Exception\HttpException;


class KernelEventListeners
{
    public static function restRouteHandler(GetResponseEvent $event , $eventName, EventDispatcher $dispatcher)
    {
        $request = $event->getRequest();
        $route = $request->route;
        if ($route->name != 'rest') {
            return;
        }

        $response = $event->getPixie()->restService->handleRequest($request, $event->getCookie());
        if ($response) {
            $event->setResponse($response);
        }
    }
} 