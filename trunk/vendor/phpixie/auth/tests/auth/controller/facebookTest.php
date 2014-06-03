<?php

namespace FacebookContollerTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace FacebookContollerTest\Controller {
	class FB extends \PHPixie\Auth\Controller\Facebook {
		public $new_user_called = false;
		public function new_user($access_token, $return_url, $display_mode) {
			$this->new_user_called = true;
		}
	}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';

	class Facebook_Controller_Test extends PHPUnit_Framework_TestCase
	{

		/**
		 * @var Expression_Database
		 */
		protected $object;

		/**
		 * Sets up the fixture, for example, opens a network connection.
		 * This method is called before a test is executed.
		 */
		protected function setUp()
		{
			$this->db_file = tempnam(sys_get_temp_dir(), 'test.sqlite');
			file_put_contents($this->db_file, '');
			$db = new PDO('sqlite:'.$this->db_file);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),fb_id VARCHAR(255))");
			$db->exec("INSERT INTO fairies(id,name,fb_id) VALUES(1,'Trixie','12345')");
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'FacebookContollerTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = $this->getMock('\PHPixie\Auth',array('provider'),array($pixie));
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.login.facebook.app_id', '111');
			$pixie->config->set('auth.default.login.facebook.app_secret', '222');
			$pixie->config->set('auth.default.login.facebook.permissions', array('user_about_me'));
			$pixie->config->set('auth.default.login.facebook.fbid_field', 'fb_id');
			
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			$provider = $this->getMock(
						'\PHPixie\Auth\Login\Facebook', 
						array('request', 'exchange_code'),
						array($pixie, $pixie-> auth->service(), 'default')
					);
					
			$this->pixie->auth->expects($this->any())
									->method('provider')
									->will($this->returnValue($provider));
									
			$provider->expects($this->any())
									->method('request')
									->will($this->returnCallback(function($url) use ($pixie) {
										$data = array();
										switch($url) {
											case "https://graph.facebook.com/me?access_token=valid_token":
												return json_encode(array(
													'id' => 12345
												));
											default:
											return json_encode(array());
										}
										
									}));
			$provider->expects($this->any())
									->method('exchange_code')
									->will($this->returnCallback(function($code, $url) {
										if ($code == 'valid_code')
											return array('access_token'=>'valid_token');
										return array('access_token'=>'new_user_token');
									}));
									
			$this->object = new FacebookContollerTest\Controller\FB($pixie);
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function normalize_url($response) {
			$location = $response->headers[1];
			$location = str_replace('Location: ', '', $location);
			return preg_replace('#state=.*?&#', '', $location);
		}
		
		public function test_flow_start() {
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array(), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('index');
			$this->assertEquals("https://www.facebook.com/dialog/oauth/?client_id=111&redirect_uri=http://test.com&display=page&scope=user_about_me", 
				$this->normalize_url($this->object->response)
			);
		}
		
		public function test_flow_start_popup() {
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array(), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('popup');
			$this->assertEquals("https://www.facebook.com/dialog/oauth/?client_id=111&redirect_uri=http://test.com&display=popup&scope=user_about_me", 
				$this->normalize_url($this->object->response)
			);
		}
		
		public function test_flow_code_correct() {
			$this->pixie->session->set('auth_default_facebook_state',111);
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array('state'=>'111', 'code'=>'valid_code'), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('index');
			$this->assertEquals(1, $this->pixie->auth->user()->id);
		}
		
		public function test_flow_code_correct_new_user() {
			$this->pixie->session->set('auth_default_facebook_state',111);
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array('state'=>'111', 'code'=>'new_code'), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('index');
			$this->assertEquals(null, $this->pixie-> auth->user());
			$this->assertEquals(true, $this->object->new_user_called);
		}
		
		public function test_flow_error_page() {
			$this->pixie->session->set('auth_default_facebook_state',112);
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array('state'=>'111', 'code'=>'new_code'), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('index');
			$this->assertEquals("Location: /", $this->object->response->headers[1]);
		}
		
		public function test_flow_error_popup() {
			$this->pixie->session->set('auth_default_facebook_state',112);
			$request = new \PHPixie\Request($this->pixie, null, "GET", array(), array('state'=>'111', 'code'=>'new_code'), array(),  array('HTTP_HOST'=>'test.com'));
			$this->object->request = $request;
			$this->object->run('popup');
		}
	
	}
}