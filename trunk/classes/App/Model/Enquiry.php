<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 13:32
 */


namespace App\Model;


class Enquiry extends BaseModel
{
    public $table = 'tbl_enquiries';

    protected $belongs_to = [
        'creator' => [
            'model' => 'User',
            'key' => 'created_by'
        ],
        'assignee' => [
            'model' => 'User',
            'key' => 'assigned_to'
        ]
    ];
}