<?php
require_once(__DIR__.'/../../files/Fairy.php');

class ORMTest extends PHPUnit_Framework_TestCase{

	protected $pager;
	protected $db_file;
	protected $conf_file;
	protected $fairies;
	protected $pixie;
	
	protected function setUp() {
		$this->db_file = tempnam(sys_get_temp_dir(), 'test.sqlite');
		$this->conf_file = tempnam(sys_get_temp_dir(), 'test.conf');
		file_put_contents($this->db_file, '');
		$db = new PDO('sqlite:'.$this->db_file);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY)");
		
		for($i=0;$i<22;$i++)
			$db->exec("INSERT INTO fairies(id) VALUES ($i)");
		
		$this->pixie = $this->getMock('\PHPixie\Pixie',array('find_file'));
		$this->pixie->expects($this->any())
                 ->method('find_file')
                 ->will($this->returnValue($this->conf_file));
				 
		$this->pixie->router = new \PHPixie\Router($this->pixie);
		$this->pixie->router->add(new \PHPixie\Route('test', '/<category>/page-<page>', array()));
		$this->pixie->db = new \PHPixie\DB($this->pixie);
		$this->pixie->orm = new \PHPixie\ORM($this->pixie);
				 
		$this->pixie->config->set('db.default.connection', 'sqlite:'.$this->db_file);
		$this->pixie->config->set('db.default.driver', 'pdo');

		$this->fairies = new \Model\Fairy($this->pixie);
		$this->fairies->where('id', '>', 2);
		$this->pager = new \PHPixie\Paginate\Pager\ORM($this->pixie, $this->fairies, 2, 5);
	}
	
	public function testException() {
		$except = false;
		try{
			$this->pager->url(1);
		}catch(\Exception $e){
			$except = true;
		}
		$this->assertEquals(true, $except);
	}
	
	public function testPageProperties() {
		$this->assertEquals(4, $this->pager->num_pages);
		$this->assertEquals(2, $this->pager->page);
		$this->assertEquals(5, $this->pager->page_size);
	}
	
	public function testPattern() {
		$this->pager->set_url_pattern('/page-#page#');
		$this->assertEquals('/page-1', $this->pager->url(1));
		$this->assertEquals('/page-2', $this->pager->url(2));
	}
	
	public function testRoute() {
		$this->pager->set_url_route('test', array('category' => 'fairy'));
		$this->assertEquals('/fairy/page-1', $this->pager->url(1));
		$this->assertEquals('/fairy/page-2', $this->pager->url(2));
	}
	
	public function testCallback() {
		$this->pager->set_url_callback(function($page) {
			return "/callback/page-$page";
		});
		$this->assertEquals('/callback/page-1', $this->pager->url(1));
		$this->assertEquals('/callback/page-2', $this->pager->url(2));
	}
	
	public function testFirstPage() {
		$pager = new \PHPixie\Paginate\Pager\ORM($this->pixie, $this->fairies, 2, 5, '/first_page');
		$pager->set_url_pattern('/page-#page#');
		$this->assertEquals('/first_page', $pager->url(1));
		$this->assertEquals('/page-2', $pager->url(2));
	}
	
	public function testItems() {
		$items = $this->pager->current_items();
		$i = 8;
		foreach($items as $item)
			$this->assertEquals($i++, $item->id);
	}
	
	protected function tearDown(){	
		$db = $this->pixie->db->get();
		$db->conn = null;
		unlink($this->db_file);
		unlink($this->conf_file);
	}
}