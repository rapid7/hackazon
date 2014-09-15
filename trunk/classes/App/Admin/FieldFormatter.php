<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 12.09.2014
 * Time: 12:04
 */


namespace App\Admin;


use App\Model\BaseModel;
use App\Traits\Pixifiable;
use PHPixie\ORM\Model;

class FieldFormatter
{
    use Pixifiable;

    /**
     * @var BaseModel
     */
    protected $item;
    protected $formatOptions = [];
    protected $renderedFields = [];

    public function __construct(Model $item, array $formatOptions)
    {
        $this->item = $item;
        $this->formatOptions = $formatOptions;
    }

    public function renderForm()
    {
        $this->renderFormStart();
        $this->renderFields();
        $this->renderSubmitButtons();
        $this->renderFormEnd();
    }

    public function renderFields()
    {
        foreach ($this->formatOptions as $field => $options) {
            $this->renderField($field, $options);
        }
    }

    public function renderField($field, array $options)
    {
        if ($options['type'] == 'extra') {
            $value = $options['title'];
        } else {
            $value = isset($this->item->$field) ? $this->item->$field : '';
        }
        $type = $options['type'];
        $escapedValue = htmlspecialchars($value);
        $fieldId = 'field_'.$field;
        $commonAttrs = ' name="'.$field.'" id="'.$fieldId.'" ';
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
            }
            echo $label . '<br>' . $this->renderSelect($value, $optionList, [
                'name' => $field,
                'id' => $fieldId,
                'class' => 'form-control '.$options['class_names']
            ]);

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
            $checked = $value ? ' checked ' : '';
            echo $label.' <input type="checkbox" '.$commonAttrs.$checked.' class="form-horizontal '.$options['class_names'].'" value="1" />';

        } else {
            echo $label.'<input type="text" value="'.$escapedValue.'" '.$commonAttrs.' class="form-control '.$options['class_names'].'"/>';
        }

        echo '</div>';
    }

    public function renderFormStart()
    {
        $enctype = 'application/x-www-form-urlencoded';
        if ($this->hasFiles()) {
            $enctype = 'multipart/form-data';
        }
        $operation = $this->item->id() ? '/edit/'.$this->item->id() : '/new';
        echo '<form method="post" action="/admin/'.strtolower($this->item->model_name).$operation.'" '
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

    public function renderSelect($selectedValue, array $optionList, array $attributes = [])
    {
        $result = [];
        $result[] = '<select id="'.$attributes['id'].'" name="'.$attributes['name'].'" class="'.$attributes['class'].'">';
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
            echo '<a class="btn btn-danger js-delete-item" '
                .'href="/admin/'.$this->item->model_name.'/delete/'.$this->item->id().'">Delete</a> ';
        }

        $name = $this->item->id() ? 'Save' : 'Add';
        echo '<button class="btn btn-primary" type="submit">'.$name.'</button> ';
    }
} 