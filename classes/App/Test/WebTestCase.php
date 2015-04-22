<?php
/**
 * Created with IntelliJ IDEA by Nick Chervyakov.
 * User: Nikolay Chervyakov 
 * Date: 21.04.2015
 * Time: 17:00
  */



namespace App\Test;


use Goutte\Client;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;

/**
 * Base Functional test case
 * @package App\Test
 */
class WebTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $host;

    /**
     * Test user username
     * @var string
     */
    protected $username;

    /**
     * Test user password
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $config = [];

    public function setUp()
    {
        $config = include __DIR__.'/../../../assets/config/parameters.php';
        $sampleConfig = include __DIR__.'/../../../assets/config/parameters.sample.php';
        $config = is_array($config) ? $config : [];
        $sampleConfig = is_array($sampleConfig) ? $sampleConfig : [];
        $this->config = array_merge($sampleConfig, $config);
        $this->host = preg_replace('/^https?:\/\//', '', $this->config['host']);
        $this->username = $this->config['test_user']['username'];
        $this->password = $this->config['test_user']['password'];

        $this->client = $this->createClient();
    }

    /**
     * @return Client
     */
    public function createClient()
    {
        $client = new Client();
        $client->setServerParameter('HTTP_HOST', $this->host);
        return $client;
    }

    public function ensureLoggedIn()
    {
        $crawler = $this->client->request('GET', '/user/login');

        /** @var Response $response */
        $response = $this->client->getResponse();

        if ($response->getStatus() < 200 && $response->getStatus() >= 300) {
            return;
        }

        $loginForm = $crawler->filter('#loginPageForm')->form();

        $this->client->submit($loginForm, [
            'username' => $this->username,
            'password' => $this->password
        ]);

        if (strpos($this->client->getHistory()->current()->getUri(), 'account') === FALSE) {
            throw new \PHPUnit_Framework_Exception("Should be logged in, but is not.");
        }
    }

    protected function prepareAmfPayload($service, $method, array $parameters = [])
    {
        return json_encode([
            "serviceName" => $service,
            "methodName" => $method,
            "parameters" => $parameters
        ]);
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function requestJsonAmf($service, $method, array $parameters = [])
    {
        $payload = $this->prepareAmfPayload($service, $method, $parameters);
        $this->client->setHeader('Content-Type', 'application/json');
        return $this->client->request('POST', '/amf', [], [], [], $payload);
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->client);
    }

    /**
     * @return null|Response
     */
    protected function getResponse()
    {
        if (!$this->client) {
            return null;
        }

        return $this->client->getResponse();
    }

    /**
     * @return null|Request
     */
    protected function getRequest()
    {
        if (!$this->client) {
            return null;
        }

        return $this->client->getRequest();
    }
}