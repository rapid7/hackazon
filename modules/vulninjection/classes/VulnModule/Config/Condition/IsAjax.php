<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.11.2014
 * Time: 18:12
 */


namespace VulnModule\Config\Condition;


use App\Core\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use VulnModule\Config\Condition;

/**
 * Class IsAjax
 * @package VulnModule\Rule\Condition
 */
class IsAjax extends Condition
{
    /**
     * @var boolean
     */
    protected $value = true;

    function __construct($value = true)
    {
        $this->setIsAjax($value);
    }

    /**
     * @param boolean $isAjax
     */
    public function setIsAjax($isAjax)
    {
        $this->value = (boolean) $isAjax;
    }

    public function isAjax()
    {
        return $this->value;
    }

    public function toArray()
    {
        return $this->value;
    }

    public function fillFromArray($data)
    {
        parent::fillFromArray($data);

        if (is_array($data)) {
            if (array_key_exists('value', $data)) {
                $this->setIsAjax(!!$data['value']);
            }
        } else {
            $this->setIsAjax(!!$data);
        }
    }

    public function match(Request $request)
    {
        return $request->is_ajax() === $this->isAjax();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addGetterConstraint('ajax', new NotNull());
    }

    function __toString()
    {
        return $this->value ? 'AJAX' : 'Not AJAX';
    }

    /**
     * @param Condition|IsAjax $condition
     * @return bool
     */
    public function equalsTo($condition)
    {
        if (!parent::equalsTo($condition)) {
            return false;
        }

        return $this->isAjax() === $condition->isAjax();
    }
}