<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 20.08.2014
 * Time: 13:01
 */


namespace App\Rest\Controller;


use App\Exception\NotFoundException;
use App\Rest\Controller;

/**
 * Class Order
 * @package App\Rest\Controller
 * @property \App\Model\Order $item
 */
class Order extends Controller
{
    public function action_get()
    {
        if ($this->item->customer_id == $this->user->id()) {
            parent::action_get();
        } else {
            throw new NotFoundException();
        }
    }
} 