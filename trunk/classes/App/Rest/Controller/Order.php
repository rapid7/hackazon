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
            return $this->asArrayWith(parent::action_get(), ['orderAddress']);
        } else {
            throw new NotFoundException();
        }
    }

    public function action_post($data = null)
    {
        $data['customer_id'] = $this->user->id();
        return parent::action_post($data);
    }

    public function action_put($data = null)
    {
        $data['customer_id'] = $this->user->id();
        return parent::action_put($data);
    }

    public function action_patch($data = null)
    {
        $data['customer_id'] = $this->user->id();
        return parent::action_patch($data);
    }

    public function action_get_collection()
    {
        $page = $this->request->get('page', 1);
        $this->model->where('customer_id', $this->user->id());
        $pager = $this->pixie->paginate->orm($this->model, $page, $this->perPage);
        $currentItems = $this->asArrayWith($pager->current_items());
        $this->addLinksForCollection($pager);
        return $currentItems;
    }

    public function action_get_addresses()
    {
        $addresses = $this->item->orderAddress->find_all();
        return $addresses->as_array(true);
    }

    /**
     * Create address for given order.
     */
    public function action_post_addresses($data)
    {
        $data['order_id'] = $this->item->id();
        $data['customer_id'] = $this->user->id();

        $controller = self::createController('OrderAddresses', $this->request, $this->pixie, true);
        $controller->run('post', ['data' => $data]);
        $address = $controller->response->body;
        $this->execute = false;      // important in order to not escape output twice
        return $address;
    }
}