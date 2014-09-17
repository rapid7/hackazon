<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 13:32
 */


namespace App\Model;

/**
 * Class Enquiry
 * @package App\Model
 * @property int $created_by
 * @property int $assigned_to
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $created_on
 * @property string $updated_on
 *
 * @property User $creator
 * @property User $assignee
 * @property EnquiryMessage[]|EnquiryMessage $messages
 */
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

    protected $has_many = [
        'messages' => array(
            'model' => 'enquiryMessage',
            'key' => 'enquiry_id',
        )
    ];

    public function getModelMeta()
    {
        return [
            'assigned_to' => [
                'is_key' => true
            ]
        ];
    }

    public function getNewEnquiriesCount()
    {
        $model  = new Enquiry($this->pixie);
        return $model->where('status', 'new')->count_all();
    }

    /**
     * @param $message
     * @param $authorId
     * @return EnquiryMessage
     */
    public function createMessage($message, $authorId)
    {
        $enqMessage = new EnquiryMessage($this->pixie);
        $enqMessage->author_id = $authorId;
        $enqMessage->message = $message;
        $enqMessage->created_on = date('Y-m-d H:i:s');
        $enqMessage->updated_on = date('Y-m-d H:i:s');
        $this->add('messages', $enqMessage);
        $enqMessage->save();
        return $enqMessage;
    }
}