<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.11.2014
 * Time: 18:07
 */


namespace VulnModule\Config\Condition;


use App\Core\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use VulnModule\Config\Condition;

class Method extends Condition
{
    /**
     * @var array Method collection
     */
    protected $methods = [];

    function __construct($methods = [])
    {
        $this->setMethods($methods);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods($methods)
    {
        if (!is_array($methods)) {
            if (!is_string($methods)) {
                throw new \InvalidArgumentException("Method must be a string.");
            }
            $methods = [$methods];
        }

        $this->methods = [];

        foreach ($methods as $method) {
            $this->addMethod($method);
        }
    }

    private function addMethod($method)
    {
        $method = strtoupper($method);

        if (!in_array($method, $this->methods)) {
            $this->methods[] = $method;
        }
    }

    public function toArray()
    {
        return [
            'methods' => $this->methods
        ];
    }

    public function fillFromArray($data)
    {
        parent::fillFromArray($data);

        if (array_key_exists('methods', $data)) {
            if (!is_array($data['methods'])) {
                $data['methods'] = [$data['methods']];
            }
            $this->setMethods($data['methods']);
        }
    }

    public function match(Request $request)
    {
        $method = strtoupper($request->method);
        return in_array($method, $this->getMethods());
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('methods', new NotBlank(['message' => "At least one method must be selected."]));
        $metadata->addPropertyConstraint('methods', new Choice([
            'message' => "At least one method must be selected.",
            'choices' => Request::getMethods(),
            'multiple' => true
        ]));
    }

    function __toString()
    {
        return 'Methods: ' . ($this->methods ? implode(', ', $this->methods) : '-');
    }

    /**
     * @param Condition|Method $condition
     * @return bool
     */
    public function equalsTo($condition)
    {
        if (!parent::equalsTo($condition)) {
            return false;
        }

        return $this->methods == $condition->getMethods();
    }
}