<?php

namespace PasswordCryptTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';

	class PasswordCrypt_Login_Test extends PHPUnit_Framework_TestCase
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
			$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),password VARCHAR(255))");
			$db->exec("INSERT INTO fairies(id,name,password) VALUES(1,'Trixie','\$1\$YedTmYOS\$Hduvs.m2BTctZOskn6QkC1')"); // old crypt() passwords
      $db->exec("INSERT INTO fairies(id,name,password) VALUES(1,'Vixie','\$2y\$10\$usesomesillystringforeQa5FME91Ofn.gNX7VH5DFkYkpDXFsbG')"); // new password_hash passwords
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'PasswordCryptTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);

			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'PDO');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.login.password.login_field', 'name');
			$pixie->config->set('auth.default.login.password.password_field', 'password');
			$pixie->config->set('auth.default.login.password.hash_method', 'crypt');
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			$this->object = $this->pixie->auth->provider('password');
		}

		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}

		public function test_hash_password(){
			$this->assertTrue( 40<strlen($this->object->hash_password('test')) );
		}
		public function test_login(){
			$this->assertEquals(true, $this->object->login('Trixie', '1234567'));
			$this->assertEquals(false, $this->object->login('Trixie', 'test1'));
			$this->assertEquals(false,$this->object->login('Trixie1', 'test1'));
      $this->assertEquals(true, $this->object->login('Vixie', '1234567'));
			$this->assertEquals(false, $this->object->login('Vixie', 'test1'));
			$this->assertEquals(false,$this->object->login('Vixie1', 'test1'));
		}
	}
}