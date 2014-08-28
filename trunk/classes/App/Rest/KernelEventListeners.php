<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 15:23
 */


namespace App\Rest;


use App\Events\GetResponseEvent;

class KernelEventListeners
{
    public static function restRouteHandler(GetResponseEvent $event /*, $eventName, EventDispatcher $dispatcher*/)
    {
        $request = $event->getRequest();
        if (!$request) {
            return;
        }
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