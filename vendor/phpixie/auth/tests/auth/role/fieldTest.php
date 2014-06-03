<?php

namespace FieldTest\Model {
	class Fairy extends \PHPixie\ORM\Model{}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';
	
	class Field_Role_Test extends PHPUnit_Framework_TestCase
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
			$db->exec("INSERT INTO fairies(id,name,password,role) VALUES(1,'Trixie','111', 'pixie')");
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'FieldTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.login.password.login_field', 'name');
			$pixie->config->set('auth.default.login.password.password_field', 'password');
			$pixie->config->set('auth.default.login.password.hash_method', 'md5');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.roles.driver', 'field');
			$pixie-> config->set('auth.default.roles.field', 'role');
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			$this->object = new PHPixie\Auth\Role\Field($pixie, 'default');
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_has_role() {
			$user = $this->pixie->orm->get('fairy')->find();
			$this->assertEquals(true, $this->object->has_role($user, 'pixie'));
			$this->assertEquals(false, $this->object->has_role($user, 'pixie1'));
		}
		
	}
}