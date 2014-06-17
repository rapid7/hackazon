<?php

class HelperTest extends PHPUnit_Framework_TestCase
{

	protected $helper;

	protected function setUp()
	{
		$this->pixie = new \PHPixie\Pixie;
		$this->helper = new \PHPixie\View\Helper($this->pixie);
	}

	public function testEscape() {
		$this->assertEquals('a&lt;&gt;&amp;ddfg\'&quot;', $this->helper->escape("a<>&ddfg'\""));
	}

	public function testOutput() {
		ob_start();
		$this->helper->output("a<>&ddfg'\"");
		$data = ob_get_clean();
		$this->assertEquals('a&lt;&gt;&amp;ddfg\'&quot;', $data);
	}
	
	public function testGet_Aliases() {
		$aliases = $this->helper->get_aliases();
		$this->assertEquals(1, count($aliases));
		extract($aliases);
		ob_start();
		$_("a<>&ddfg'\"");
		$data = ob_get_clean();
		$this->assertEquals('a&lt;&gt;&amp;ddfg\'&quot;', $data);
	}

}
