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
 * @property \App\Model\Order $item
 */
class Order extends Controller
{
    public function action_get()
    {
        if ($this->item->customer_id == $this->user->id()) {
            $this->item->orderItems->with('product');
            $data = $this->asArrayWith(parent::action_get(), ['orderAddress', 'orderItems']);
            $data['increment_id'] = 1000000 + $data['id'];
            $data['total_price'] = $this->item->orderItems->getItemsTotal();
            return $data;
        } else {
            throw new NotFoundException();
        }
    }

    public function action_post($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        $data['customer_id'] = $this->user->id();
        unset($data['total_price']);
        unset($data['id']);
        unset($data['increment_id']);
        if (!$data['coupon_id']) {
            $data['coupon_id'] = null;
        }
        error_log("Order to be added: " . serialize($data));
        return parent::action_post($data);
    }

    public function action_put($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        $data['customer_id'] = $this->user->id();
        return parent::action_put($data);
    }

    public function action_patch($data = null)
    {
        if ($data === null) {
            $data = $this->request->post();
        }
        $data['customer_id'] = $this->user->id();
        return parent::action_patch($data);
    }

    public function action_get_collection()
    {
        $page = $this->request->get('page', 1);
        $perPage = $this->request->get('per_page', $this->perPage);
        // TODO: Remove this check to run "where" condition always.
        if ($this->user) {
            $this->model->where('customer_id', $this->user->id());
        }
        $this->adjustOrder();
        $pager = $this->pixie->paginate->orm($this->model, $page, $perPage);
        $currentItems = $this->asArrayWith($pager->current_items());
        foreach ($currentItems as $key => $item) {
            $currentItems[$key]['increment_id'] = 1000000 + $item['id'];
        }

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

    protected function preloadModel()
    {
        if ($this->request->param('id')) {
            /** @var Model $model */
            $model = $this->model
                ->where($this->model->id_field, $this->request->param('id') - 1000000)
                ->find();
            if ($model->loaded()) {
                $this->item = $model;
            } else {
                $this->model = $this->pixie->orm->get($this->modelName);
                $model = $this->model
                    ->where($this->model->id_field, $this->request->param('id'))
                    ->find();
                if ($model->loaded()) {
                    $this->item = $model;
                } else {
                    throw new NotFoundException();
                }
            }
        }
    }
}