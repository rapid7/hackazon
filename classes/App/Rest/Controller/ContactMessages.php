<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.10.2014
 * Time: 14:41
 */


namespace App\Rest\Controller;


use App\Exception\HttpException;
use App\Rest\Controller;

class ContactMessages extends Controller
{
    public function action_post($data = null)
    {
        if ($this->request->param('id')) {
            throw new HttpException('You can\'t create already existing object.', 400, null, 'Bad Request');
        }
        if ($data === null) {
            $data = $this->request->post();
        }
        $data['customer_id'] = $this->user->id();
        return parent::action_post($data);
    }
} 