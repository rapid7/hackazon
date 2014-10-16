<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 12.09.2014
 * Time: 12:04
 */


namespace App\Admin;


use App\Helpers\ArraysHelper;
use App\Model\BaseModel;
use App\Pixie;
use PHPixie\ORM\Model;

class FieldFormatter
{
    /**
     * @var BaseModel
     */
    protected $item;
    protected $formatOptions = [];
    protected $renderedFields = [];
    protected $options = [];
    protected $controllerAlias;

    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Model $item, array $formatOptions, array $options = [])
    {
        $this->item = $item;
        $this->formatOptions = $formatOptions;
        $this->options = $options;
        $this->controllerAlias = $options['alias'] ?: $this->item->model_name;
    }

    public function renderForm()
    {
        $this->renderFormStart();
        $this->renderFields();
        $this->renderSubmitButtons();
        $this->renderFormEnd();
    }

    public function renderFields($fields = null)
    {
        if ($fields === null) {
            $fields = array_keys($this->formatOptions);
        }
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            $this->renderField($field, $this->formatOptions[$field]);
        }
    }

    public function renderField($field, array $options = null)
    {
        if (in_array($field, $this->renderedFields)) {
            return;
        }

        if (!is_array($options)) {
            $options = $this->formatOptions[$field];
        } else {
            $options = array_merge($this->formatOptions[$field], $options);
        }

        if (isset($options['value'])) {
            $value = $options['value'];
        } else if ($options['type'] == 'extra') {
            $value = $options['title'];

        } else {
            $value = isset($this->item->$field) ? $this->item->$field : $options['default_value'];
        }

        $type = $options['type'];
        $escapedValue = htmlspecialchars($value);
        $fieldId = 'field_'.$field;
        $commonAttrs = ' name="'.$field.'" id="'.$fieldId.'" ';
        if ($options['required']) {
            $commonAttrs .= ' required ';
        }
        $label = '<label for="'.$fieldId.'">'.$options['label'].'</label>';

        echo '<div class="form-group"> ';

        $method = 'render' . strtoupper($options['type'] . 'Field');
        if (method_exists($this, $method)) {
            $this->$method();

        } else if ($options['readonly'] && in_array($options['type'], ['text', 'textarea', 'password', 'select'])) {
            echo $label.': '.$escapedValue;

        } else if ($options['type'] == 'hidden') {
            echo '<input type="hidden" value="'.$escapedValue.'" '.$commonAttrs.' />';

        } else if ($options['type'] == 'textarea') {
            echo $label.'<textarea cols="40" rows="4" '.$commonAttrs.' '
                .'class="form-control '.$options['class_names'].'">'.$escapedValue.'</textarea>';

        } else if ($type == 'select') {
            $optionList = $options['option_list'];
            if (is_callable($optionList)) {
                $optionList = call_user_func_array($optionList, [$this->pixie, $options]);
            } else if (!is_array($optionList)) {
                $optionList = ArraysHelper::arrayFillEqualPairs([$optionList]);
            }
            echo $label . '<br>' . $this->renderSelect($value, $optionList, array_merge([
                'name' => $field,
                'id' => $fieldId,
                'class' => 'form-control '.$options['class_names'],
            ], $options['required'] ? ['required' => 'required'] : []));

        } else if ($options['type'] == 'image') {
            echo $label;
            if ($value) {
                if ($options['use_external_dir']) {
                    $src = "/upload/download.php?image=".$escapedValue;
                } else {
                    $src = htmlspecialchars($options['dir_path'].$value);
                }

                echo '<br><img src="'.$src.'" alt="" '
                    . 'class="model-image model-'.htmlspecialchars($this->item->model_name).'-image" /> <br>'
                    . '<label><input type="checkbox" name="remove_image_'.htmlspecialchars($field).'" /> Remove image</label>';
            }
            echo '<br><input type="file" '.$commonAttrs.' class="file-input btn btn-default btn-primary btn-lg" '
                    . 'title="Select image" value="'.$escapedValue.'">';

        } else if ($options['type'] == 'boolean') {
            $checked = $value ? ' checked ' : (!$this->item->loaded() && $options['default_value'] ? ' checked ' : '');
            echo $label.' <input type="checkbox" '.$commonAttrs.$checked.' class="form-horizontal '.$options['class_names'].'" value="1" />';

        } else {
            $dataType = $options['data_type'] == 'email' ? 'email' : 'text';
            echo $label.'<input type="'.$dataType.'" value="'.$escapedValue.'" '.$commonAttrs.' class="form-control '.$options['class_names'].'"/>';
        }

        echo '</div>';
        $this->renderedFields[] = $field;
    }

    public function renderFormStart()
    {
        $enctype = 'application/x-www-form-urlencoded';
        if ($this->hasFiles()) {
            $enctype = 'multipart/form-data';
        }
        $operation = $this->item->id() ? '/edit/'.$this->item->id() : '/new';
        echo '<form method="post" action="/admin/'.strtolower($this->controllerAlias).$operation.'" '
                . 'enctype="'.$enctype.'" '
                . ' class="model-form model-'.$this->item->model_name.'-form">';
    }

    public function renderFormEnd()
    {
        echo '</form>';
    }

    public function hasFiles()
    {
        foreach ($this->formatOptions as $options) {
            if ($options['type'] == 'file' || $options['type'] == 'image') {
                return true;
            }
        }
        return false;
    }

    public function renderSelect($selectedValue = null, array $optionList, array $attributes = [])
    {
        $result = [];
        $result[] = '<select '.$this->mergeAttributes($attributes).'>';
        foreach ($optionList as $value => $label) {
            $result[] = '<option value="'.htmlspecialchars($value).'" '
                . ($value == $selectedValue ? ' selected' : '').'>'.htmlspecialchars($label).'</option>';
        }

        $result[] = '</select>';
        return implode("\n", $result);
    }

    public function renderSubmitButtons()
    {
        if ($this->item->id()) {
            echo '<a class="btn btn-primary" '
                .'href="/admin/'.$this->controllerAlias.'/new/">Add new</a> ';
            echo '<a class="btn btn-danger js-delete-item" '
                .'href="/admin/'.$this->controllerAlias.'/delete/'.$this->item->id().'">Delete</a> ';
        }

        $name = $this->item->id() ? 'Save' : 'Add';
        echo '<button class="btn btn-primary" type="submit">'.$name.'</button> ';
    }

    public function mergeAttributes(array $attributes)
    {
        $attrs = [];
        array_walk($attributes, function ($value, $attr) use (&$attrs) {
            $attrs[] = htmlspecialchars($attr).'="'.htmlspecialchars($value).'"';
        });
        $attrs = implode(" ", $attrs);
        return $attrs;
    }

    function getPixie()
    {
        return $this->pixie;
    }

    function setPixie(Pixie $pixie = null)
    {
        $this->pixie = $pixie;
    }
} 