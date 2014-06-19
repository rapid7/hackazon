<?php

namespace RelationTest\Model {
	class Fairy extends \PHPixie\ORM\Model {
		public $belongs_to = array('role');
		public $has_many = array('roles'=>array('through'=>'fairies_roles'));
	}
	class Role extends \PHPixie\ORM\Model { 
		public $has_many = array('fairies'=>array('through'=>'fairies_roles'));
	}
}
namespace {

	require_once dirname(dirname(__DIR__)).'/files/sessionStub.php';
	
	class Relation_Role_Test extends PHPUnit_Framework_TestCase
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
			$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),password VARCHAR(255), role_id INT)");
			$db->exec("INSERT INTO fairies(id,name,password,role_id) VALUES(1,'Trixie','111',1)");
			$db->exec("CREATE TABLE roles(id INT PRIMARY_KEY,name VARCHAR(255))");
			$db->exec("INSERT INTO roles(id,name) VALUES(1,'pixie')");
			$db->exec("CREATE TABLE fairies_roles(fairy_id INT, role_id INT)");
			$db->exec("INSERT INTO fairies_roles(fairy_id, role_id) VALUES(1,1)");
			$db = null;
			$pixie = $this->pixie = new \PHPixie\Pixie();
			$this->pixie-> app_namespace = 'RelationTest\\';
			$pixie->db = new \PHPixie\DB($pixie);
			$pixie->orm = new \PHPixie\ORM($pixie);
			$this->pixie->auth = new \PHPixie\Auth($pixie);
			
			$pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
			$pixie->config->set('db.default.driver', 'pdo');
			$pixie->config->set('auth.default.login.password.login_field', 'name');
			$pixie->config->set('auth.default.login.password.password_field', 'password');
			$pixie->config->set('auth.default.login.password.hash_method', 'md5');
			$pixie->config->set('auth.default.model', 'Fairy');
			$pixie->config->set('auth.default.roles.driver', 'relation');
			$pixie-> config->set('auth.default.roles.name_field', 'name');
			$pixie-> config->set('auth.default.roles.relation', 'role');
			$pixie-> config->set('auth.default.roles.type', 'belongs_to');
			$this->session_array = array();
			$pixie->session = new \AuthStub\Session($this->session_array);
			
		}
		
		public function tearDown() {
			$this->pixie->db->get()->conn = null;
			unlink($this->db_file);
		}
		
		public function test_has_role() {
			$this->object = new PHPixie\Auth\Role\Relation($this->pixie, 'default');
			$user = $this->pixie->orm->get('fairy')->find();
			$this->assertEquals(true, $this->object->has_role($user, 'pixie'));
			$this->assertEquals(false, $this->object->has_role($user, 'pixie1'));
		}
		
		public function test_has_role_many() {
			$this->pixie->config->set('auth.default.roles.relation', 'roles');
			$this->pixie->config->set('auth.default.roles.type', 'has_many');
			$this->object = new PHPixie\Auth\Role\Relation($this->pixie, 'default');
			$user = $this->pixie->orm->get('fairy')->find();
			$this->assertEquals(true, $this->object->has_role($user, 'pixie'));
			$this->assertEquals(false, $this->object->has_role($user, 'pixie1'));
		}
		
	}
}