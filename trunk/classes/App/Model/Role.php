<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 15:14
 */


namespace App\Model;

/**
 * Role class for access control.
 * @package App\Model
 * @property string $name
 */
class Role extends BaseModel
{
    public $table = 'tbl_roles';
} 