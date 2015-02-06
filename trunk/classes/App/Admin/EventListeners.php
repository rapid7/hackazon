<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 15:59
 */


namespace App\Admin;


use App\Core\Response;
use App\Events\GetResponseEvent;
use App\Exception\ForbiddenException;
use App\Exception\UnauthorizedException;

class EventListeners
{
    public static function hasAccessListener(GetResponseEvent $event/* , $eventName, EventDispatcher $dispatcher*/)
    {
        $request = $event->getRequest();

        if (!$request || !$request->isAdminPath()) {
            return;
        }

        // Allow log in
        if ($request->param('controller') == 'user' && $request->param('action') == 'login') {
            return;
        }

        $pixie = $event->getPixie();
        $user = $pixie->auth->user();

        if (!$user) {
            throw new UnauthorizedException();
        }

        if (!$pixie->auth->has_role('admin')) {
            throw new ForbiddenException();
        }
    }

    public static function redirectUnauthorized(GetResponseEvent $event/* , $eventName, EventDispatcher $dispatcher*/)
    {
        $request = $event->getRequest();

        if (!$request || !$request->isAdminPath()
            || !($event->getException() instanceof UnauthorizedException
                    || $event->getException() instanceof ForbiddenException)
        ) {
            return;
        }

        $pixie = $event->getPixie();
        if ($event->getException() instanceof ForbiddenException) {
            if ($pixie->auth->has_role('admin')) {
                return;
            }
            $pixie->session->flash('error', 'You don\'t have permissions to access this resource.');
        }
        $response = new Response($pixie);
        $response->redirect('/admin/user/login?return_url=' . rawurlencode($_SERVER['REQUEST_URI']));
        $event->setResponse($response);
    }
} 