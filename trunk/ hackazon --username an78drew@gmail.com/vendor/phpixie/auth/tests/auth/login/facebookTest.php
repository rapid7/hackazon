<?php

namespace FacebookTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';

	class Facebook_Login_Test extends PHPUnit_Framework_TestCase
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
			$this->pixie-> app_namespace = 'FacebookTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.login.facebook.app_id', '111');
			$pixie->config->set('auth.default.login.facebook.app_secret', '222');
			$pixie->config->set('auth.default.login.facebook.permissions', array('user_about_me'));
			$pixie->config->set('auth.default.login.facebook.fbid_field', 'fb_id');
			
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			$this->object = $this->getMock(
						'\PHPixie\Auth\Login\Facebook', 
						array('request'),
						array($pixie, $pixie-> auth->service(), 'default')
					);
			$this->object->expects($this->any())
									->method('request')
									->will($this->returnCallback(function($url) use ($pixie) {
										$data = array();
										switch($url) {
											case "https://graph.facebook.com/oauth/access_token?"
													."client_id=111"
													."&redirect_uri=2"
													."&client_secret=222"
													."&code=1":
												return "test=test2";
											case "https://graph.facebook.com/me?access_token=login_test":
												return json_encode(array(
													'id' => 12345
												));
											default:
											return json_encode(array());
										}
										
									}));
				
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_login(){
			$this->assertEquals(true, $this->object->login('login_test'));
			$this->assertEquals(false, $this->object->login('login_test2'));
		}
		
		
		public function test_check_login() {
			$this->session_array['auth_default_facebook_uid'] = 1;
			$this->session_array['auth_default_facebook_token'] = 12;
			
			$this->assertEquals(true, $this->object->check_login());
			$this->assertEquals(12, $this->object->access_token);
			
			$this->session_array['auth_default_facebook_uid'] = null;
			$this->assertEquals(false,$this->object->check_login());
		}
		
		public function test_set_user() {
			$this->object->set_user($this->pixie->orm->get('fairy')->find(),123);
			$this->assertEquals(1, $this->session_array['auth_default_facebook_uid']);
			$this->assertEquals(123, $this->object->access_token);
		}
		
		
		public function test_logout() {
			$this->object->set_user($this->pixie-> orm->get('fairy')->find());
			$this->object->logout();
			$this->assertEquals(true,empty($this->session_array['auth_default_facebook_uid']));
		}
		
		public function test_login_url() {
			$this->assertEquals("https://www.facebook.com/dialog/oauth/?"
				."client_id=111"
				."&redirect_uri=2"
				."&state=1"
				."&display=3"
				."&scope=user_about_me",$this->object->login_url(1,2,3));
		}
		
		public function test_exchange_code() {
			$data = $this->object->exchange_code(1,2);
			$this->assertEquals('test2',$data['test']);
		}
		
		public function test_request() {
			$object = new \PHPixie\Auth\Login\Facebook($this->pixie, $this->pixie->auth->service(), 'default');
			$object->request('https://graph.facebook.com/');
		}
		
		public function test_logout_url() {
			$this->object->set_user($this->pixie-> orm->get('fairy')->find(), 123);
			$redirect_url = 'http://google.com/';
			$url = $this->object->logout_url($redirect_url);
			$this->assertEquals('https://facebook.com/logout.php?access_token=123&next='.urlencode($redirect_url),$url);
		}
		
		public function test_logout_url_exception() {
			$except = false;
			try{
				$url = $this->object->logout_url('http://google.com/');
			}catch (\Exception $e) {
				$except = true;
			}
			$this->assertEquals(true, $except);
		}
	
	}
}