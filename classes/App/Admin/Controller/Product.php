<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.08.2014
 * Time: 20:02
 */


namespace App\Admin\Controller;


use App\Admin\CRUDController;
use App\Admin\FieldFormatter;
use App\Exception\NotFoundException;
use App\Helpers\ArraysHelper;
use App\Model\BaseModel;
use App\Model\Category;
use App\Model\Option;
use App\Model\OptionValue;
use App\Model\ProductOptionValue;

class Product extends CRUDController
{
    public $modelNamePlural = 'Products';

    protected function getListFields()
    {
        return array_merge(
            $this->getIdCheckboxProp(),
            [
                'productID' => [
                    'title' => 'Id',
                    'column_classes' => 'dt-id-column',
                    'data_type' => 'integer',
                ],
                'name' => [
                    'max_length' => 64,
                    'type' => 'link'
                ],
                'category.name' => [
                    'title' => 'Category',
                    'type' => 'link',
                    'template' => '/admin/category/%category.categoryID%',
                    'width' => 150
                ],
                'Price' => [
                    'value_prefix' => '$',
                    'data_type' => 'integer',
                ],
                'picture' => [
                    'type' => 'image',
                    'dir_path' => '/products_pictures/',
                    'max_width' => 40,
                    'max_height' => 30,
                    'is_link' => true,
                    'column_classes' => 'dt-picture-column',
                    'title' => 'Pic'
                ]
            ],
            $this->getEditLinkProp(),
            $this->getDeleteLinkProp()
        );
    }

    protected function tuneModelForList()
    {
        $this->model->with('category');
    }

    protected function getEditFields()
    {
         $fields = [
            'productID' => [
                'label' => 'Id'
            ],
            'name' => [
                'type' => 'text',
                'required' => true
            ],
            'categoryID' => [
                'label' => 'Category',
                'type' => 'select',
                'option_list' => 'App\Admin\Controller\Category::getAvailableCategoryOptions',
                'required' => true
            ],
            'description' => [
                'type' => 'textarea'
            ],
            'brief_description' => [
                'type' => 'textarea'
            ],
            'Price' => [
                'label' => 'Price ($)'
            ],
            'product_code' => [

            ],
            'picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'big_picture' => [
                'type' => 'image',
                'dir_path' => '/products_pictures/'
            ],
            'meta_title' => [
                'type' => 'textarea',
            ],
            'meta_keywords' => [
                'type' => 'textarea',
            ],
            'meta_desc' => [
                'type' => 'textarea',
                'label' => 'Meta Description'
            ],
            'in_stock' => [
                'type' => 'boolean'
            ],
            'enabled' => [
                'type' => 'boolean'
            ]
        ];

        return $fields;
    }

    public function action_edit()
    {
        $id = $this->request->param('id');
        $options = $this->getAllProductOptionsWithValuesArray();

        if ($this->request->method == 'POST') {
            $product = null;
            if ($id) {
                /** @var \App\Model\Product $product */
                $product = $this->pixie->orm->get($this->model->model_name, $id);
            }

            if (!$product || !$product->loaded()) {
                throw new NotFoundException();
            }

            $data = $this->request->post();
            $this->processRequestFilesForItem($product, $data);
            $product->values($product->filterValues($data));
            $product->save();

            if ($product->loaded()) {
                $this->redirect('/admin/' . strtolower($product->model_name) . '/edit/'.$product->id());
                return;
            }

        } else {

            if (!$id) {
                throw new NotFoundException();
            }

            $product = $this->pixie->orm->get($this->model->model_name, $id);
            if (!$product->loaded()) {
                throw new NotFoundException();
            }
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = $this->modelName.' &laquo;'.htmlspecialchars(trim($product->name)).'&raquo;';
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $product;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($product, $editFields);
        $this->view->formatter->setPixie($this->pixie);

        $this->view->options = $options;
        $this->view->subview = 'product/edit';
        $this->view->fieldFormatter = $this->getProductOptionsFormatter();
    }

    public function action_new()
    {
        /** @var \App\Model\User $user */
        $user = $this->pixie->orm->get($this->model->model_name);
        $roles = self::getRoleOptions($this->pixie);

        if ($this->request->method == 'POST') {
            $data = $this->request->post();
            $user->values(array_merge($user->filterValues($data), [
                'created_on' => $data['created_on'] ?: date('Y-m-d H:i:s')
            ]));
            $user->save();

            if ($user->loaded()) {
                $this->processRequestFilesForItem($user, $data);

                $requestUserRoles = $data['roles'] ?: [];
                $userRoles = array_intersect_key($roles, array_flip($requestUserRoles));

                foreach ($this->pixie->orm->get('role')->find_all() as $role) {
                    if (array_key_exists($role->id(), $userRoles)) {
                        $user->add('roles', $role);
                    }
                }

                $this->redirect('/admin/' . strtolower($user->model_name) . '/edit/'.$user->id());
                return;
            }
        } else {
            $userRoles = [];
        }

        $editFields = $this->prepareEditFields();
        $this->view->pageTitle = 'Add new ' . $this->modelName;
        $this->view->pageHeader = $this->view->pageTitle;
        $this->view->modelName = $this->model->model_name;
        $this->view->item = $user;
        $this->view->editFields = $editFields;
        $this->view->formatter = new FieldFormatter($user, $editFields);
        $this->view->formatter->setPixie($this->pixie);
        $this->view->roles = self::getRoleOptions($this->pixie);
        $this->view->subview = 'product/edit';
        $this->view->userRoles = $userRoles;
    }

    /**
     * @return array
     */
    private function getAllProductOptionsWithValuesArray()
    {
        $result = [];
        /** @var Option[] $options */
        $options = $this->pixie->orm->get('option')->order_by('sort_order', 'asc')->find_all()->as_array();
        foreach ($options as $option) {
            $res = ['name' => $option->name, 'variants' => []];

            /** @var OptionValue $variant */
            foreach ($option->variants->order_by('sort_order', 'asc')->find_all()->as_array() as $variant) {
                $res['variants'][$variant->id()] = $variant->name;
            }
            $result[$option->id()] = $res;
        }

        return $result;
    }

    /**
     * @param \App\Model\Product $product
     * @return array
     */
    protected function getProductOptionsWithValuesArray(\App\Model\Product $product)
    {
        $result = [];
        $productOptions = $product->productOptions
            ->with('optionVariant.parentOption')
            ->order_by('optionVariant_parentOption.name', 'asc')
            ->order_by('optionVariant.name', 'asc')
            ->find_all()->as_array();

        /** @var ProductOptionValue[] $productOptions */
        foreach ($productOptions as $option) {
            $variant = $option->optionVariant;
            $parentOpt = $variant->parentOption;
            if (!$result[$parentOpt->id()]) {
                $result[$parentOpt->id()] = [
                    'option' => $parentOpt,
                    'variants' => [],
                    'productOptions' => []
                ];
            }
            $result[$parentOpt->id()]['productOptions'][$option->id()] = $option;
            $result[$parentOpt->id()]['variants'][$option->id()] = $option->optionVariant;
        }

        return $result;
    }

    /**
     * @return FieldFormatter
     */
    protected function getProductOptionsFormatter()
    {
        $opts = $this->getOptionsArray();
        /** @var Option $option */
        $option = $this->pixie->orm->get('Option', key($opts));
        $optVals = $option->getValuesForOption();
        return new FieldFormatter($this->pixie->orm->get('OptionValue'), [
            'optionID' => [
                'type' => 'select',
                'label' => 'Option',
                'option_list' => $opts,
                'value' => key($opts),
                'required' => true
            ],
            'variantID' => [
                'type' => 'select',
                'label' => 'Variant',
                'option_list' => $optVals,
                'value' => key($optVals),
                'required' => true
            ]
        ]);
    }

    public function getOptionsArray()
    {
        $result = [];
        $options = $this->pixie->orm->get('Option')->order_by('name', 'asc')->find_all()->as_array();
        /** @var Option[] $options */
        foreach ($options as $opt) {
            $result[$opt->id()] = $opt->name;
        }

        return $result;
    }
}