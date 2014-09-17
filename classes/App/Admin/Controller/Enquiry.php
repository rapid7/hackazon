<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:01
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\ArraysHelper;
use App\Model\EnquiryMessage;

class Enquiry extends CRUDController
{
    public $modelNamePlural = 'Enquiries';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'id' => [
                    'column_classes' => 'dt-id-column',
                ],
                'title' => [
                    'type' => 'link'
                ],
                'status',
                'creator.username' => [
                    'is_link' => true,
                    'title' => 'Created By',
                    'template' => '/admin/user/%creator.id%'
                ],
                'assignee.username' => [
                    'is_link' => true,
                    'title' => 'Assigned To',
                    'template' => '/admin/user/%assignee.id%'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        $this->model->with('creator', 'assignee');
    }

    protected function getEditFields()
    {
        return [
            'id' => [],
            'created_by' => [
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
                'required' => true
            ],
            'assigned_to' => [
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\User::getAvailableUsers',
            ],
            'title' => [
                'required' => true
            ],
            'description' => [
                'type' => 'textarea',
                'required' => true
            ],
            'status' => [
                'type' => 'select',
                'option_list' => ArraysHelper::arrayFillEqualPairs(['new', 'rejected', 'resolved'])
            ],
            'created_on' => [
                'data_type' => 'date'
            ],
            'updated_on',
        ];
    }

    public function fieldFormatter($value, $item = null, array $format = [])
    {
        $value2 = parent::fieldFormatter($value, $item, $format);
        if ($format['original_field_name'] == 'status') {
            $value2 = '<span class="label label-'.strtolower($value2).'">'.$value.'</span>';
        }

        return $value2;
    }

    public function action_edit()
    {
        parent::action_edit();
        /** @var \App\Model\Enquiry $enquiry */
        $enquiry = $this->view->item;
        if ($enquiry->id()) {
            $this->view->enquiryMessages = $enquiry->messages->with('author')->find_all()->as_array();
            $this->view->subview = 'enquiry/edit';
        }
    }

    public function action_add_message()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }
        $id = $this->request->param('id');

        if (!$id) {
            throw new NotFoundException();
        }

        /** @var \App\Model\Enquiry $enquiry */
        $enquiry = $this->pixie->orm->get('enquiry', $id);

        if (!$enquiry || !$enquiry->loaded()) {
            throw new NotFoundException();
        }

        $message = $this->request->post('message');
        if (!$message) {
            $this->jsonResponse(['error' => 1, 'message' => 'Please enter the message.']);
        }

        /** @var EnquiryMessage $enquiryMessage */
        $enquiryMessage = $enquiry->createMessage($message, $this->pixie->auth->user()->id());

        if ($enquiryMessage->id()) {
            $messageView = $this->view('enquiry/_enquiry_message');
            $messageView->eMessage = $enquiryMessage;
            $this->jsonResponse([
                'success' => 1,
                'enquiryMessage' => $enquiryMessage->as_array(true),
                'html' => $messageView->render()
            ]);
        } else {
            $this->jsonResponse(['error' => 1, 'message' => 'Error while adding message.']);
        }
    }
} 