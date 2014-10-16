<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.10.2014
 * Time: 19:27
 */


namespace App;


interface IPixifiable
{
    function getPixie();
    function setPixie(Pixie $pixie = null);
} 