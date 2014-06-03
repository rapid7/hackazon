<?php

namespace AuthTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(__DIR__).'/files/sessionStub.php';	
	
	class Auth_Test extends PHPUnit_Framework_TestCase
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
			$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),password VARCHAR(255), role VARCHAR(255))");
			$db->exec("INSERT INTO fairies(id,name,password, role) VALUES(1,'Trixie','53de0be73c461405f0953cf1c33806b8:26165517952a3e16d4' ,'pixie')");
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'AuthTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.login.password.login_field', 'name');
			$pixie->config->set('auth.default.login.password.password_field', 'password');
			$pixie->config->set('auth.default.login.password.hash_method', 'md5');
			$pixie->config->set('auth.default.roles.driver', 'field');
			$pixie->config->set('auth.default.roles.field', 'role');
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_set_user() {
			$this->pixie->auth->set_user($this->pixie->orm->get('fairy')->find(), 'password');
			$this->assertEquals(1, $this->pixie->auth->user()->id);
			$this->assertEquals('password',$this->pixie->auth->logged_with());
		}
		
		public function test_logout() {
			$this->pixie->auth->set_user($this->pixie->orm->get('fairy')->find(), 'password');
			$this->pixie->auth->logout();
			$this->assertEquals(null, $this->pixie->auth->user());
			$this->assertEquals(null, $this->pixie->auth->logged_with());
			$this->assertEquals(true,empty($this->session_array['auth_default_password_uid']));
		}
		
		public function test_has_role() {
			$this->assertEquals(false, $this->pixie->auth->has_role('pixie'));
			$this->pixie->auth->set_user($this->pixie-> orm->get('fairy')->find(), 'password');
			$this->assertEquals(true, $this->pixie->auth->has_role('pixie'));
			$this->assertEquals(false, $this->pixie->auth->has_role('pixie1'));
		}
		
		public function test_provider() {
			$this->assertEquals('PHPixie\Auth\Login\Password', get_class($this->pixie->auth->provider('password')));
		}
		
		public function test_check_login() {
			$this->assertEquals(null, $this->pixie->auth->user());
		}
		
		public function test_check_login_true() {
			$this->session_array['auth_default_password_uid'] = 1;
			$this->assertEquals(1, $this->pixie->auth->user()->id);
		}
		
	}
}