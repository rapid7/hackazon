<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.11.2014
 * Time: 18:02
 */

namespace VulnModule\Config;
use App\Core\Request;


/**
 * Class Condition
 * @package VulnModule\Rule
 */
interface ICondition
{
    public function toArray();
    public function getName();
    public function match(Request $request);
}