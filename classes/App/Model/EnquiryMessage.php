<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 13:32
 */


namespace App\Model;

/**
 * Class EnquiryMessage
 * @package App\Model
 * @property int $id
 * @property int $enquiry_id
 * @property int $author_id
 * @property string $message
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $author
 * @property Enquiry $enquiry
 */
class EnquiryMessage extends BaseModel
{
    public $table = 'tbl_enquiry_messages';

    protected $belongs_to = [
        'enquiry' => [
            'model' => 'Enquiry',
            'key' => 'enquiry_id'
        ],
        'author' => [
            'model' => 'User',
            'key' => 'author_id'
        ]
    ];
}