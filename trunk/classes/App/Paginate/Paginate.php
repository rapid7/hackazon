<?php
namespace App\Paginate;

class Paginate {

	public function db($model, $page, $page_size, $first_page_url = null) {
		return new \App\Paginate\Paginate\Pager\DB($this->pixie, $model, $page, $page_size, $first_page_url);
	}
}
