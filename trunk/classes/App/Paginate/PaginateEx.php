<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 02.02.2015
 * Time: 18:52
 */


namespace App\Paginate;


use App\Paginate\Paginate\Pager\ORM;
use PHPixie\Paginate;

class PaginateEx extends Paginate
{
    public function orm($model, $page, $page_size, $first_page_url = null)
    {
        return new ORM($this->pixie, $model, $page, $page_size, $first_page_url);
    }
}