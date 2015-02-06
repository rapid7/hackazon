<?php

namespace App;


use AmfphpModule\AmfphpModule;
use App\Cart\CartService;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Route;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\DependencyInjection\Container;
use App\EventDispatcher\EventDispatcher;
use App\EventDispatcher\Events;
use App\Events\GetResponseEvent;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\ContainerHelper;
use App\Installation\Installer;
use App\Paginate\PaginateEx;
use App\Rest\Auth\AuthFactory;
use App\Rest\RestService;
use GWTModule\GWTModule;
use PHPixie\Controller;
use PHPixie\Cookie;
use PHPixie\Exception\PageNotFound;
use PHPixie\ORM;
use VulnModule\AnnotationReader;
use VulnModule\Config\ModelInfoRepository;
use VulnModule\VulnInjection;

/**
 * Pixie dependency container
 *
 * @method Pixie bootstrap
 * @property-read \PHPixie\DB $db Database module
 * @property-read \App\Core\ORM $orm ORM module
 * @property-read Auth $auth Auth module
 * @property-read VulnInjection $vulninjection Vulninjection module
 * @property-read \PHPixie\Email $email Email module
 * @property-read Request $request Request instance
 * @property-read Response $response Request instance
 * @property-read Router $router Router instance
 * @property-read Session $session Session instance
 * @property-read Debug $debug Debug object
 * @property-read VulnInjection\Service $vulnService Debug object
 * @property-read ModelInfoRepository $modelInfoRepository
 * @property-read EventDispatcher $dispatcher
 * @property-read Cookie $cookie
 * @property-read RestService $restService
 * @property-read AuthFactory $restAuthFactory
 * @property-read PaginateEx $paginate
 * @property-read GWTModule $gwt
 * @property-read AmfphpModule $amf
 * @property-read Config $config
 * @property-read Installer $installer
 * @property-read CartService $cart
 * @property-read Container $container
 * @property-read ContainerHelper $containerHelper
 * @property-read Paginate\Paginate $paginateDB
 * @property-read AnnotationReader $annotationReader
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
        'orm' => '\App\Core\ORM',
        'auth' => '\App\Core\Auth',
        'session' => '\App\Core\Session',
        'vulninjection' => '\VulnModule\VulnInjection',
		'email' => '\PHPixie\Email',
		'paginate' => 'App\\Paginate\\PaginateEx',
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
        $this->instance_classes['router'] = '\\App\\Core\\Router';
        $this->instance_classes['modelInfoRepository'] = '\\VulnModule\\Config\\ModelInfoRepository';
        $this->instance_classes['dispatcher'] = '\\App\\EventDispatcher\\EventDispatcher';
        $this->instance_classes['restRouteMatcher'] = '\\App\\Rest\\RouteMatcher';
        $this->instance_classes['restService'] = '\\App\\Rest\\RestService';
        $this->instance_classes['restAuthFactory'] = '\\App\\Rest\\Auth\\AuthFactory';
        $this->instance_classes['installer'] = '\\App\\Installation\\Installer';
        $this->instance_classes['cart'] = '\\App\\Cart\\CartService';
        $this->instance_classes['container'] = '\\App\\DependencyInjection\\Container';
        $this->instance_classes['containerHelper'] = '\\App\\Helpers\\ContainerHelper';
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

    public function http_request()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = preg_replace("#^{$this->basepath}(?:index\\.php/?)#i", '/', $uri);
        $url_parts = parse_url($uri);
        $route_data = $this->router->match($url_parts['path'], $_SERVER['REQUEST_METHOD']);
        return $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'],
            $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE, apache_request_headers());
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

            $event = new GetResponseEvent($request, $request ? $request->cookie() : []);
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
                $request = $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'],
                    $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE, apache_request_headers());
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
    public function request($route, $method = "GET", $post = [], $get = [], $param = [], $server = [], $cookie = [], $headers = [])
    {
        return new Request($this, $route, $method, $post, $get, $param, $server, $cookie, $headers);
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

    /**
     * Constructs a route
     *
     * @param string $name Name of the route
     * @param mixed $rule Rule for this route
     * @param array $defaults Default parameters for the route
     * @param mixed $methods Methods to restrict this route to.
     *                       Either a single method or an array of them.
     * @return Route
     */
    public function route($name, $rule, $defaults, $methods = null)
    {
        return new Route($this->basepath, $name, $rule, $defaults, $methods);
    }
}
