<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.09.2014
 * Time: 17:30
 */


namespace App\Model;

/**
 * Class File
 * @package App\Model
 * @property int $user_id
 * @property string $path
 */
class File extends BaseModel
{
    public $table = 'tbl_files';
}