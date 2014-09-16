<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 19:30
 */


namespace App\Controller;


use App\Page;

class Voucher extends Page
{
    public function action_index()
    {
        header("Access-Control-Allow-Origin: *");
        $this->pixie->amf->run();
        exit;
    }
} 