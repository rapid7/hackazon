<?php

namespace PHPixie\Paginate\Pager;

/**
 * Pager implementation for DB Models
 *
 * @package Paginate
 */
class DB extends \PHPixie\Paginate\Pager{

	/**
	 * Get the total number items
	 *
	 * @return integer Total number of items
	 */
	protected function item_count() {
		return count($this->items->execute()->as_array());

	}

	/**
	 * Get items for the current page
	 *
	 * @return \PHPixie\DB\Result
	 */
	public function current_items() {
		return $this->items
					->offset($this->offset)
					->limit($this->page_size)
					->execute();
	}
}