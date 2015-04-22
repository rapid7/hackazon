<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 02.02.2015
 * Time: 18:44
 */


namespace App\Paginate\Paginate\Pager;


use App\Model\BaseModel;
use App\Pixie;
use VulnModule\VulnerableField;

/**
 * Class ORM
 * @package App\Paginate\Paginate\Pager
 * @property Pixie $pixie
 */
class ORM extends \PHPixie\Paginate\Pager\ORM
{
    public function __construct($pixie, $items, $page, $page_size, $first_page_url = null)
    {
        $this->pixie = $pixie;
        $this->items =  $items;
        $this->page = $page;
        $this->page_size = $page_size;

        $filteredPageSize = $page_size instanceof VulnerableField ? $page_size->getFilteredValue() : $page_size;
        $filteredPage = $page instanceof VulnerableField ? $page->getFilteredValue() : $page;

        $this->offset = $filteredPageSize * ($filteredPage - 1);
        $this->num_items = $this->item_count();
        $this->num_pages = ceil($this->num_items / (int)$filteredPageSize);

        $this->first_page_url = $first_page_url;
    }

    /**
     * @inheritdoc
     */
    public function current_items()
    {
        /** @var BaseModel $query */
        $query = $this->items
            ->offset($this->offset)
            ->limit($this->page_size instanceof VulnerableField ? $this->page_size->getFilteredValue() : $this->page_size);

        $vulnFields = [];
        if ($this->page_size instanceof VulnerableField) {
            $vulnFields[] = $this->page_size;
        }
        if ($this->page instanceof VulnerableField) {
            $vulnFields[] = $this->page;
        }

        if (count($vulnFields)) {
            $query->conn->startBlindness($vulnFields);
        }

        return $query->find_all();
    }
}