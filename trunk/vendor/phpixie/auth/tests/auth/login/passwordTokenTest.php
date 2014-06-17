<?php

namespace PasswordTokenTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';
	
	class Password_Token_Test extends PHPUnit_Framework_TestCase
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
			$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),password VARCHAR(255), token VARCHAR(255))");
			$db->exec("INSERT INTO fairies(id,name,password, token) VALUES(1,'Trixie','53de0be73c461405f0953cf1c33806b8:26165517952a3e16d4', NULL)");
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'PasswordTokenTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.login.password.login_field', 'name');
			$pixie->config->set('auth.default.login.password.password_field', 'password');
			$pixie->config->set('auth.default.login.password.hash_method', 'md5');
			$pixie->config->set('auth.default.login.password.login_token_field', 'token');
			$pixie->config->set('auth.default.login.password.login_token_lifetime', 1);
			
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			$this->object = $this->pixie->auth->provider('password');
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_random_string(){
			$this->assertEquals(48, strlen($this->object->random_string()));
		}
		
		public function test_login(){
			$this->assertEquals(true, $this->object->login('Trixie', 'test', true));
			$token = $this->pixie->db->query('select')->fields('token')->table('fairies')->execute()->get('token');
			$this->assertEquals(true, strlen($token) > 0);
			$login_token = $this->pixie->cookie->get('login_token');
			$this->assertEquals(true, strlen($login_token) > 0);
			$this->pixie->session->remove('auth_default_password_uid');
			$this->pixie->cookie->set('login_token', "Trixie:test");
			$this->assertEquals(false, $this->object->check_login());
			$this->pixie->cookie->set('login_token', $login_token);
			$this->assertEquals(true, $this->object->check_login());
			$this->pixie->session->remove('auth_default_password_uid');
			sleep(2);
			$this->assertEquals(false, $this->object->check_login());
		}
		

	}
}