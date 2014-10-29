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
use PHPixie\ORM\Model;

/**
 * Class Order
 * @package App\Rest\Controller
 * @property \App\Model\Cart $item
 */
class CartItems extends Controller
{
    public function action_post($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        unset($data['id']);
        return parent::action_post($data);
    }
}