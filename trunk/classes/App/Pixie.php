<?php

namespace App;


use AmfphpModule\AmfphpModule;
use App\Cart\CartService;
use App\Cart\CheckoutService;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\EventDispatcher\EventDispatcher;
use App\EventDispatcher\Events;
use App\Events\GetResponseEvent;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Installation\Installer;
use App\Rest\RestService;
use GWTModule\GWTModule;
use PHPixie\Controller;
use PHPixie\Cookie;
use PHPixie\Exception\PageNotFound;
use PHPixie\ORM;
use VulnModule\Config\ModelInfoRepository;
use VulnModule\VulnInjection;

/**
 * Pixie dependency container
 *
 * @method Pixie bootstrap
 * @property-read \PHPixie\DB $db Database module
 * @property-read \PHPixie\ORM $orm ORM module
 * @property-read \PHPixie\Auth $auth Auth module
 * @property-read VulnInjection $vulninjection Vulninjection module
 * @property-read \PHPixie\Email $email Email module
 * @property-read Request $request Request instance
 * @property-read Response $response Request instance
 * @property-read Debug $debug Debug object
 * @property-read VulnInjection\Service $vulnService Debug object
 * @property-read ModelInfoRepository $modelInfoRepository
 * @property-read EventDispatcher $dispatcher
 * @property-read Cookie $cookie
 * @property-read RestService $restService
 * @property-read \PHPixie\Paginate $paginate
 * @property-read GWTModule $gwt
 * @property-read AmfphpModule $amf
 * @property-read Config $config
 * @property-read Installer $installer
 * @property-read CartService $cart
 * @method Controller|Rest\Controller controller
 */
class Pixie extends \PHPixie\Pixie {

    /**
     * @var VulnInjection\Service
     */
    protected $vulnService;

    protected $modules = array(
        'config'  => '\App\Core\Config',
        'db' => '\PHPixie\DB',
        'orm' => '\PHPixie\ORM',
        'auth' => '\PHPixie\Auth',
        'vulninjection' => '\VulnModule\VulnInjection',
		'email' => '\PHPixie\Email',
		'paginate' => '\PHPixie\Paginate',
		'paginateDB' => '\App\Paginate\Paginate',
		'gwt' => 'GWTModule\GWTModule',
		'amf' => 'AmfphpModule\AmfphpModule',
    );

    /**
     * Constructs Pixie instance.
     */
    public function __construct()
    {
        $this->instance_classes['debug'] = '\\App\\Debug';
        $this->instance_classes['request'] = '\\App\\Core\\Request';
        $this->instance_classes['response'] = '\\App\\Core\\Response';
        $this->instance_classes['modelInfoRepository'] = '\\VulnModule\\Config\\ModelInfoRepository';
        $this->instance_classes['dispatcher'] = '\\App\\EventDispatcher\\EventDispatcher';
        $this->instance_classes['restRouteMatcher'] = '\\App\\Rest\\RouteMatcher';
        $this->instance_classes['restService'] = '\\App\\Rest\\RestService';
        $this->instance_classes['installer'] = '\\App\\Installation\\Installer';
        $this->instance_classes['cart'] = '\\App\\Cart\\CartService';
        Pixifier::getInstance()->setPixie($this);
    }

    /**
     * Add named instances by hand.
     * @param $name
     * @param $class
     */
    public function addInstanceClass($name, $class)
    {
        $this->instance_classes[$name] = $class;
    }

    /**
     * @inheritdoc
     */
    protected function after_bootstrap() {
		//Whatever code you want to run after bootstrap is done.
        $displayErrors = $this->getParameter('parameters.display_errors');
        $this->debug->display_errors = is_bool($displayErrors) ? $displayErrors : true;

        $this->dispatcher->addListener(Events::KERNEL_PRE_EXECUTE, '\\App\\Rest\\KernelEventListeners::restRouteHandler');
        $this->dispatcher->addListener(Events::KERNEL_PRE_EXECUTE, '\\App\\Admin\\EventListeners::hasAccessListener');

        $this->dispatcher->addListener(Events::KERNEL_PRE_HANDLE_EXCEPTION, '\\App\\Admin\\EventListeners::redirectUnauthorized');

        $this->dispatcher->addListener('PRE_REMOVE_ENTITY', '\\App\\Model\\Role::roleRemoveListener');
	}

    /**
     * @inheritdoc
     */
    public function handle_http_request()
    {
        $request = null;
        try {
            $request =  $this->http_request();
            $response = $request->execute();
            $response->add_header("Content-Length: " . strlen($response->body));
            $response->send_headers()->send_body();

        } catch (PageNotFound $e) {
            $e = new NotFoundException('Not Found', 404, $e);
            $e->setParameter('request', $request);
            $this->handle_exception($e);

        } catch (HttpException $e) {
            $e->setParameter('request', $request);
            $this->handle_exception($e);

        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * @param \Exception $exception
     */
    public function handle_exception($exception)
    {
        if ($exception instanceof PageNotFound) {
            $exception = new NotFoundException('', 404, $exception);
        }

        if (!($exception instanceof HttpException)) {
            $this->debug->render_exception_page($exception);
        } else {
            $this->handle_error_request($exception);
        }
    }

    /**
     * Shows caught exception in a nice view.
     * @param \App\Exception\HttpException|\Exception $exception
     * @return null
     */
    public function handle_error_request(\Exception $exception) {
        try {
            $response = null;
            $request = null;
            if ($exception instanceof HttpException) {
                $request = $exception->getParameter('request');
            }

            $event = new GetResponseEvent($request, $request ? $request->getCookie() : []);
            $event->setException($exception);
            $this->dispatcher->dispatch(Events::KERNEL_PRE_HANDLE_EXCEPTION, $event);

            if ($event->getResponse()) {
                $response = $event->getResponse();
            }

            if (!$response) {
                $isAdmin = $exception instanceof HttpException
                    && $exception->getParameter('request')
                    && $exception->getParameter('request')->isAdminPath()
                    && $this->auth->has_role('admin');

                if ($isAdmin) {
                    $route_data = $this->router->match('/admin/error/' . $exception->getCode());
                } else {
                    $route_data = $this->router->match('/error/' . $exception->getCode());
                }
                $route_data['params'] = array_merge($route_data['params'], [
                    'exception' => $exception
                ]);
                $request = $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE);
                $response = $request->execute();
            }
            $response->send_headers()->send_body();

        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * Get param value without exception. Instead if value is missing, NULL is returned.
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($name, $default = null)
    {
        try {
            return $this->config->get($name);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Creates custom implementation of Request.
     * @inheritdoc
     */
    public function request($route, $method = "GET", $post = array(), $get = array(), $param = array(), $server = array(), $cookie = array())
    {
        return new Request($this, $route, $method, $post, $get, $param, $server, $cookie);
    }

    /**
     * @inheritdoc
     * @return Response|\PHPixie\Response
     */
    public function response()
    {
        return new Response($this);
    }

    /**
     * @@inheritdoc
     * @return View|\PHPixie\View
     */
    public function view($name)
    {
        return new View($this, $this->view_helper(), $name);
    }

    /**
     * @return VulnInjection\Service
     */
    public function getVulnService()
    {
        return $this->vulnService;
    }

    /**
     * @param VulnInjection\Service $vulnService
     * @return $this
     */
    public function setVulnService($vulnService)
    {
        $this->vulnService = $vulnService;
        $this->addInstance('vulnService', $vulnService);
        return $this;
    }

    /**
     * @inheritdoc
     * @return View\Helper|\PHPixie\View\Helper
     */
    public function view_helper()
    {
        return new View\Helper($this);
    }

    /**
     * Adds new object as a dependency.
     * @param $name
     * @param $object
     */
    public function addInstance($name, $object)
    {
        $this->instances[$name] = $object;
    }

    public function isWindows()
    {
        if (stristr(php_uname('s'), 'Windows NT')) {
            return true;
        }

        return false;
    }
}
