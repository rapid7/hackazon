<?php

namespace ServiceTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(__DIR__).'/files/sessionStub.php';	
	
	class Servive_Auth_Test extends PHPUnit_Framework_TestCase
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
			$this->pixie-> app_namespace = 'ServiceTest\\';
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
			$this->object = $this->pixie->auth->service();
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_set_user() {
			$this->object->set_user($this->pixie->orm->get('fairy')->find(), 'password');
			$this->assertEquals(1, $this->object->user()->id);
			$this->assertEquals('password',$this->object->logged_with());
		}
		
		public function test_logout() {
			$this->object->set_user($this->pixie->orm->get('fairy')->find(), 'password');
			$this->object->logout();
			$this->assertEquals(null, $this->object->user());
			$this->assertEquals(null, $this->object->logged_with());
			$this->assertEquals(true,empty($this->session_array['auth_default_password_uid']));
		}
		
		public function test_has_role() {
			$this->assertEquals(false, $this->object->has_role('pixie'));
			$this->object->set_user($this->pixie-> orm->get('fairy')->find(), 'password');
			$this->assertEquals(true, $this->object->has_role('pixie'));
			$this->assertEquals(false, $this->object->has_role('pixie1'));
		}
		
		public function test_provider() {
			$this->assertEquals('PHPixie\Auth\Login\Password', get_class($this->object->provider('password')));
		}
		
		public function test_check_login() {
			$this->assertEquals(false, $this->object->check_login());
			$this->session_array['auth_default_password_uid'] = 1;
			$this->assertEquals(true, $this->object->check_login());
		}
		
		public function test_user_model() {
			$this->assertEquals('ServiceTest\Model\Fairy', get_class($this->object->user_model()));
		}
		
	}
}