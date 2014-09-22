<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.09.2014
 * Time: 17:15
 */


namespace App\Model;

/**
 * Class ProductOptionValue
 * @package App\Model
 * @property int $ID
 * @property int $productID
 * @property int $variantID
 * @property int $price_surplus
 * @property int $default
 * @property OptionValue $optionVariant
 * @property Product $product
 * @property
 */
class ProductOptionValue extends BaseModel
{
    public $table = 'tbl_product_options_values';
    public $id_field = 'ID';

    protected $belongs_to = array(
        'product' => array(
            'model' => 'Product',
            'key' => 'productID'
        ),
        'optionVariant' => array(
            'model' => 'OptionValue',
            'key' => 'variantID'
        )
    );

    public function checkCanSaveProductOption()
    {
        if (!$this->productID || !$this->variantID) {
            return false;
        }

        /** @var ProductOptionValue $opt */
        $opt = $this->pixie->orm->get($this->model_name)
            ->where('productID', $this->productID)->where('and', ['variantID', $this->variantID])->find();

        if ($opt->loaded() && $opt->id() != $this->id()) {
            return false;
        }

        return true;
    }
} 