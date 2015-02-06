<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 19:30
 */


namespace App\Controller;


use App\Page;

class Amf extends Page
{


    public function action_index()
    {
        $this->vulninjection->goUp()->goUp();
        $this->vulninjection->loadAndAddChildContext('amf');
        $this->vulninjection->goDown('amf');

        //header("Access-Control-Allow-Origin: *");
        $this->pixie->amf->run();
        exit;
    }
} 