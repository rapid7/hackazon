<?php

namespace App;


use App\Core\Request;
use App\Core\View;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use PHPixie\Exception\PageNotFound;
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
 * @property-read Debug $debug Debug object
 * @property-read VulnInjection\Service $vulnService Debug object
 */
class Pixie extends \PHPixie\Pixie {

    /**
     * @var VulnInjection\Service
     */
    protected $vulnService;

    protected $modules = array(
        'db' => '\PHPixie\DB',
        'orm' => '\PHPixie\ORM',
        'auth' => '\PHPixie\Auth',
        'vulninjection' => '\VulnModule\VulnInjection',
        'email' => '\PHPixie\Email',
    );

    /**
     * Constructs Pixie instance.
     */
    public function __construct()
    {
        $this->instance_classes['debug'] = '\\App\\Debug';
        $this->instance_classes['request'] = '\\App\\Core\\Request';
    }
	
	protected function after_bootstrap(){
		//Whatever code you want to run after bootstrap is done.
        $displayErrors = $this->getParameter('parameters.display_errors');
        $this->debug->display_errors = is_bool($displayErrors) ? $displayErrors : true;
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
     */
    public function handle_error_request(\Exception $exception) {
        try {
            $route_data = $this->router->match('/error/' . $exception->getCode());
            $route_data['params'] = array_merge($route_data['params'], [
                'exception' => $exception
            ]);
            $request = $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE);
            $response = $request->execute();
            $response->send_headers()->send_body();

        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * Get param value without exception. Instead if value is missing, NULL is returned.
     * @param $name
     * @return mixed|null
     */
    public function getParameter($name)
    {
        try {
            return $this->config->get($name);
        } catch (\Exception $e) {
            return null;
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

    public function view_helper()
    {
        return new View\Helper($this);
    }

    public function addInstance($name, $object)
    {
        $this->instances[$name] = $object;
    }
}
