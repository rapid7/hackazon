<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 18.09.2014
 * Time: 14:21
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Option;

class OptionValue extends CRUDController
{
    protected function getListFields()
    {
        return [
            'variantID' => [
                'type' => 'text'
            ],
            'name' => [
                'max_length' => '255',
                'strip_tags' => true,
            ],
            'sort_order' => [
            ],
            'edit' => [
                'extra' => true,
                'type' => 'html',
                'template' => '<a href="#" data-id="%'.$this->model->id_field.'%" '
                    . ' class="js-edit-variant">Edit</a>',
                'column_classes' => 'edit-action-column'
            ],
            'delete' => [
                'extra' => true,
                'type' => 'html',
                'template' => '<a href="#" data-id="%'.$this->model->id_field.'%" '
                    . ' class="js-delete-variant">Delete</a>',
                'column_classes' => 'delete-action-column'
            ]
        ];
    }

    protected function tuneModelForList()
    {
        $this->model->with('parentOption');
        if ($optId = $this->request->get('option_id')) {
            $this->model->where('optionID', $optId);
        }
    }

    public function action_save()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }
        $data = $this->request->post();
        $variantId = $data['variantID'];
        unset($data['variantID']);

        if ($variantId) {
            /** @var \App\Model\OptionValue $variant */
            $variant = $this->pixie->orm->get('OptionValue', $variantId);
            if (!$variant || !$variant->loaded()) {
                throw new NotFoundException();
            }

            unset($data['optionID']);

        } else {
            $variant = $this->pixie->orm->get('OptionValue');
            if (!$data['optionID']) {
                throw new \LogicException('You must provide option to which this variant belongs.');
            }

            $option = $this->pixie->orm->get('Option', $data['optionID']);
            if (!$option || !$option->loaded()) {
                throw new NotFoundException('Option with ID=' . $data['optionID'] . ' does not exist.');
            }
        }

        $variant->values($variant->filterValues($data));
        $variant->save();

        $this->jsonResponse(['success' => true, 'variant' => $variant->as_array(true)]);
    }

    public function action_delete()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }

        $id = $this->request->post('id');

        if (!$id) {
            throw new NotFoundException();
        }

        $variant = $this->pixie->orm->get('OptionValue', $id);

        if (!$variant || !$variant->loaded()) {
            throw new NotFoundException();
        }

        $confirmed = (boolean) $this->request->post('confirm');

        $count = $this->pixie->db->query('count')->table('tbl_product_options_values')
            ->where('variantID', $variant->id())->execute();

        if ($count) {
            if (!$confirmed) {
                $this->jsonResponse([
                    'error' => 1,
                    'message' => $count . ' products depend on this option variant. Confirm removal of product options.'
                        . "\nAll dependent products will lose this property. Be careful",
                    'productCount' => (int)$count
                ]);
                return;

            } else {
                $this->pixie->db->query('delete')->table('tbl_product_options_values')
                    ->where('variantID', $variant->id())->execute();
            }
        }

        $variant->delete();
        $this->jsonResponse(['success' => 1]);
    }

    public function action_get_option_values()
    {
        $optionId = $this->request->getRequestData('option_id');
        if (!$optionId) {
            throw new NotFoundException();
        }
        $option = $this->pixie->orm->get('Option', $optionId);

        /** @var Option $option */
        if (!$option->loaded()) {
            throw new NotFoundException;
        }

        $values = $option->getValuesForOption($optionId);

        $this->jsonResponse(['optionVariants' => $values]);
    }
}