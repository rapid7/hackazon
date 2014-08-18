<?php

namespace PHPixie\Paginate\Pager;

/**
 * Pager implementation for ORM Models
 *
 * @package Paginate
 */
class ORM extends \PHPixie\Paginate\Pager{

	/**
	 * Get the total number items
	 *
	 * @return integer Total number of items
	 */
	protected function item_count() {
		return $this->items->count_all();
	}

	/**
	 * Get items for the current page
	 *
	 * @return \PHPixie\ORM\Result
	 */
	public function current_items() {
		return $this->items
					->offset($this->offset)
					->limit($this->page_size)
					->find_all();
	}
}