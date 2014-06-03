<?php

namespace AuthStub;
	
class Session{
	
	public $data;
	
	public function __construct(&$data) {
		$this->data = &$data;
	}
	
	public function get($key){
		if (isset($this->data[$key]))
			return $this->data[$key];
	}
	
	public function set($key, $val){
		$this->data[$key] = $val;
	}
	
	public function remove($key){
		if (isset($this->data[$key]))
			unset($this->data[$key]);
	}
}
