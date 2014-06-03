<?php
namespace Model{
	class Stub_ORM
	{

		public $row;
		public $loaded;
		public $cached = array();
		public $model_name = 'Stub_Orm';

		public function values($row, $loaded)
		{
			$this->row = $row;
			$this->loaded = $loaded;
			return $this;
		}

		public function columns()
		{
			return array('id', 'name');
		}

	}
}
