<?php
namespace App\Paginate;

use App\Paginate\Paginate\Pager\DB;
use App\Pixie;

class Paginate {
	/**
	 * @var Pixie
	 */
	protected $pixie;

	public function __construct(Pixie $pixie)
	{
		$this->pixie = $pixie;
	}

	public function db($model, $page, $page_size, $first_page_url = null) {
		return new DB($this->pixie, $model, $page, $page_size, $first_page_url);
	}
}
