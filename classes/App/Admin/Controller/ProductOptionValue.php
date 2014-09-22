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

class ProductOptionValue extends CRUDController
{
    protected function getListFields()
    {
        return [
            'optionVariant.parentOption.name' => [
                'type' => 'text'
            ],
            'optionVariant.name' => [
                'max_length' => '255',
                'strip_tags' => true,
            ],
            'variantID' => [],
            'ID' => [],
            'optionVariant.optionID' => [
                'title' => 'optionID'
            ],
            'price_surplus' => [
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
        $this->model->with('optionVariant.parentOption');
        if ($productId = $this->request->get('product_id')) {
            $this->model->where('productID', $productId);
        }
    }

    public function action_save()
    {
        if ($this->request->method != 'POST') {
            throw new HttpException('Method Not Allowed', 405, null, 'Method Not Allowed');
        }
        $data = $this->request->post();
        $prodOptionId = $data['ID'];
        unset($data['ID']);

        if ($prodOptionId) {
            /** @var \App\Model\ProductOptionValue $prodOpt */
            $prodOpt = $this->pixie->orm->get('ProductOptionValue', $prodOptionId);
            if (!$prodOpt || !$prodOpt->loaded()) {
                throw new NotFoundException();
            }

            unset($data['productID']);

        } else {
            $prodOpt = $this->pixie->orm->get('ProductOptionValue');
            if (!$data['variantID'] || !$data['productID']) {
                throw new \LogicException('You must provide option variant and product id for product option.');
            }

            $variant = $this->pixie->orm->get('OptionValue', $data['variantID']);
            if (!$variant || !$variant->loaded()) {
                throw new NotFoundException('Option variant with ID=' . $data['variantID'] . ' does not exist.');
            }

            $product = $this->pixie->orm->get('Product', $data['productID']);
            if (!$product || !$product->loaded()) {
                throw new NotFoundException('Product with ID=' . $data['productID'] . ' does not exist.');
            }
        }

        $prodOpt->values($prodOpt->filterValues($data));
        if (!$prodOpt->checkCanSaveProductOption()) {
            $this->jsonResponse([
                'error' => 1,
                'message' => 'Such option already exists for this product. Please edit it instead.']
            );
            return;
        }

        $prodOpt->save();

        $this->jsonResponse(['success' => true, 'productOption' => $prodOpt->as_array(true)]);
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

        $prodOption = $this->pixie->orm->get('ProductOptionValue', $id);

        if (!$prodOption || !$prodOption->loaded()) {
            throw new NotFoundException();
        }

        $prodOption->delete();
        $this->jsonResponse(['success' => 1]);
    }
} 