<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.01.2015
 * Time: 19:23
 */


namespace App\Templating;


use Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormView;

/**
 * Templates for Symfony2 forms. Made as class to speed-up form rendering.
 * @package App\Templating
 */
class Templates
{
    /**
     * @param PhpEngine $view
     * @param FormView $form
     */
    public function context_form($view, $form)
    {
        /** @var FormHelper $formHelper */
        $formHelper = $view['form'];
        echo $formHelper->form($form);
    }

    /**
     * @param PhpEngine $view
     * @param $method
     * @param $name
     * @param $action
     * @param $attr
     * @param $multipart
     */
    public function form_start($view, $method, $name, $action, $attr, $multipart)
    {
        ?>
        <?php $method = strtoupper($method) ?>
        <?php $form_method = $method === 'GET' || $method === 'POST' ? $method : 'POST' ?>
        <form name="<?php echo $name ?>" method="<?php echo strtolower($form_method) ?>" action="<?php echo $action ?>"<?php foreach ($attr as $k => $v) { printf(' %s="%s"', $view->escape($k), $view->escape($v)); } ?><?php if ($multipart): ?> enctype="multipart/form-data"<?php endif ?>>
        <?php if ($form_method !== $method): ?>
            <input type="hidden" name="_method" value="<?php echo $method ?>" />
        <?php endif ?>

       <?php
    }

       /**
        * @param PhpEngine $view
        * @param $attr
        * @param $id
        * @param $translation_domain
        * @param TranslatorHelper $translatorHelper
        */
    public function widget_container_attributes($view, $attr, $id, $translation_domain, $translatorHelper)
    {
       ?>
        <?php if (!empty($id)): ?>id="<?php echo $view->escape($id) ?>" <?php endif ?>
        <?php foreach ($attr as $k => $v): ?>
            <?php if (in_array($v, array('placeholder', 'title'), true)): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($translatorHelper->trans($v, array(), $translation_domain))) ?>
            <?php elseif ($v === true): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($k)) ?>
            <?php elseif ($v !== false): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($v)) ?>
            <?php endif ?>
        <?php endforeach ?>

        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function context_row($form, $formHelper)
    {
        /** @var FormView $form */
        ?>
        <?php //echo $formHelper->label($form) ?>
        <?php echo $formHelper->errors($form); ?>
        <?php echo $formHelper->widget($form); ?>
        <?php
    }

    /**
     * @param FormErrorIterator $errors
     */
    public function form_errors($errors)
    {
        /** @var FormView $form */
        ?>
        <?php if (count($errors) > 0): ?>
            <div class="panel panel-danger">
                <div class="panel-body">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error->getMessage() ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif ?>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     */
    public function context_widget($view, $form)
    {
        /** @var FormHelper $formHelper */
        $formHelper = $view['form'];
        ?>
        <div class="panel panel-primary context-panel js-context-panel" <?php echo $formHelper->block($form, 'widget_container_attributes') ?>
            data-id="<?php echo $form->vars['id']; ?>" data-name="<?php echo $form->vars['full_name']; ?>"
            data-edit-mode="<?php echo (int) $form->vars['edit_mode_enabled']; ?>">
            <div class="panel-heading form-inline">
                <?php if (!$form->vars['edit_mode_enabled']): ?>
                    <?php echo '<strong>' . $form['name']->vars['value'] . '</strong> (' . $form['type']->vars['value']
                            . ($form['technology']->vars['value'] == 'any' ? '' : ', ' . $form['technology']->vars['value']) . ')'; ?>
                <?php endif; ?>
                <?php echo isset($form['name']) ? $formHelper->widget($form['name']) : ''; ?>
                <?php echo isset($form['storageRole']) ? $formHelper->widget($form['storageRole']) : ''; ?>
                <?php echo isset($form['type']) ? $formHelper->widget($form['type']) : ''; ?>
                <?php echo isset($form['technology']) ? $formHelper->widget($form['technology']) : ''; ?>

                <div class="pull-right">
                    <?php if ($form->vars['edit_mode_enabled']): ?>
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm js-add-field" type="button">Add Field</button>
                            <button class="btn btn-default btn-sm js-add-child-context" type="button">Add Child</button>
                            <button class="btn btn-default btn-sm js-remove" type="button">Remove</button>
                        </div>
                        <div class="btn-group js-position-buttons">
                            <button class="btn btn-default btn-sm js-move-up js-position-button" type="button" title="Move up">&#8593;</button>
                            <button class="btn btn-default btn-sm js-move-down js-position-button" type="button" title="Move down">&#8595;</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clearfix clear"></div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php echo $formHelper->errors($form); ?>
                <a href="#" class="js-show-vulns-link show-vulns-link">Show vulnerabilities</a><br>
                <div class="vulns-block js-vulns-block">
                    <?php echo $formHelper->row($form['vulnTree']); ?>
                </div>
                <br>
                <?php echo $formHelper->row($form['fields']); ?>
                <?php if ($form['children'] && $form['children']->count()): ?><br><?php endif; ?>
                <?php echo $formHelper->block($form, 'form_rows') ?>
                <?php echo $formHelper->rest($form); ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param FormHelper $formHelper
     * @param FormView $form
     * @param string $type
     */
    public function hidden_widget($formHelper, $form, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple', array('type' => !is_null($type) ? $type : "hidden"));
    }

    /**
     * @param FormHelper $formHelper
     * @param FormView $form
     */
    public function hidden_row($formHelper, $form)
    {
        echo $formHelper->widget($form);
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param $type
     * @param $value
     * @param FormHelper $formHelper
     */
    public function form_widget_simple($view, $form, $value, $formHelper, $type = null)
    {
        ?>
        <input type="<?php echo !is_null($type) ? $view->escape($type) : 'text' ?>" <?php echo $formHelper->block($form, 'widget_attributes')
                ?><?php if (!empty($value) || is_numeric($value)): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?> />
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param $id
     * @param $full_name
     * @param $read_only
     * @param $disabled
     * @param $required
     * @param $attr
     * @param $translation_domain
     * @param TranslatorHelper $translatorHelper
     */
    public function widget_attributes($view, $id, $full_name, $read_only, $disabled, $required, $attr, $translation_domain,
            $translatorHelper)
    {
        ?>
        id="<?php echo $view->escape($id) ?>" name="<?php echo $view->escape($full_name) ?>" <?php if ($read_only): ?>readonly="readonly" <?php endif ?>
        <?php if ($disabled): ?>disabled="disabled" <?php endif ?>
        <?php if ($required): ?>required="required" <?php endif ?>
        <?php foreach ($attr as $k => $v): ?>
        <?php if (in_array($v, array('placeholder', 'title'), true)): ?>
            <?php printf('%s="%s" ', $view->escape($k), $view->escape($translatorHelper->trans($v, array(), $translation_domain))) ?>
        <?php elseif ($v === true): ?>
            <?php printf('%s="%s" ', $view->escape($k), $view->escape($k)) ?>
        <?php elseif ($v !== false): ?>
            <?php printf('%s="%s" ', $view->escape($k), $view->escape($v)) ?>
        <?php endif ?>
        <?php endforeach ?>

        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function conditional_vulnerable_element_row($form, $formHelper = null)
    {
        ?>
        <div class="panel panel-yellow vulnerability-block js-vulnerability-block"
            <?php echo $formHelper->block($form, 'widget_container_attributes') ?>
            data-id="<?php echo $form->vars['id']; ?>" data-name="<?php echo $form->vars['full_name']; ?>">
            <div class="panel-heading form-inline">
                Vulnerabilities <?php echo $formHelper->widget($form['name']); ?>
                <?php echo $form->vars['edit_mode_enabled'] ? '' : ($form['name']->vars['value'] ? ' (' . $form['name']->vars['value'] . ')' : ''); ?>
                <?php if ($form->vars['edit_mode_enabled']): ?>
                <?php if (isset($form['conditionSet'])) {  ?>
                    <a href="#" class="js-show-conditions">Hide conditions</a>
                <?php } ?>
                <div class="pull-right">
                    <div class="btn-group">
                        <button class="btn btn-default btn-sm js-add-vulnerability-selector dropdown-toggle" data-toggle="dropdown"
                                type="button">Add Vulnerability <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                        </ul>
                    </div>
                    <div class="btn-group">
                        <?php if (isset($form['conditionSet'])) {  ?>
                            <button class="btn btn-default btn-sm js-add-condition-selector dropdown-toggle" data-toggle="dropdown"
                                    type="button">Add Condition <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                            </ul>
                        <?php } ?>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default btn-sm js-add-child" type="button">Add Child</button>
                        <button class="btn btn-default btn-sm js-remove" type="button">Remove</button>
                    </div>
                </div>
            <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php
                echo $formHelper->errors($form);

                if (isset($form['conditionSet'])) {  ?>
                    <div class="panel panel-yellow vulnerability-conditions-block">
                        <div class="panel-body">
                            <?php
                            echo $formHelper->label($form['conditionSet']);
                            echo $formHelper->errors($form['conditionSet']);
                            echo $formHelper->widget($form['conditionSet']);  ?>
                        </div>
                    </div>
                <?php
                }

                echo $formHelper->label($form['vulnerabilitySet']);
                echo $formHelper->errors($form['vulnerabilitySet']);
                echo $formHelper->widget($form['vulnerabilitySet']);?>

                <?php
                if ($form['children']) {
                    echo $formHelper->row($form['children']);
                }

                echo $formHelper->rest($form); ?>

            </div>
        </div>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function context_fields_collection_row($form, $formHelper)
    {
        /** @var FormView $form */
        ?>
        <div class="panel panel-green context-fields" data-field-collection-id="<?php echo $form->vars['id']; ?>"
            data-field-collection-name="<?php echo $form->vars['full_name']; ?>">
            <div class="panel-heading">
                <?php echo $formHelper->label($form); ?>
                <?php if ($form->vars['edit_mode_enabled']): ?>
                <div class="pull-right">
                    <div class="btn-group">
                        <button class="btn btn-default btn-sm js-add-field" type="button">Add Field</button>
                    </div>
                </div>
            <?php endif; ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php
                //echo $formHelper->errors($form);
                echo $formHelper->widget($form);
                echo $formHelper->rest($form);
                ?>

            </div>
        </div>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param string $label
     * @param boolean $required
     * @param boolean $compound
     * @param string $id
     * @param string $name
     * @param string $translation_domain
     * @param $label_attr
     * @param FormHelper $formHelper
     * @param TranslatorHelper $translatorHelper
     * @param $label_format
     */
    public function form_label($view, $label, $required, $compound, $id, $name, $translation_domain, $label_attr,
                               $formHelper, $translatorHelper, $label_format = null)
    {
        ?>
        <?php if (false !== $label): ?>
        <?php if ($required) { $label_attr['class'] = trim((isset($label_attr['class']) ? $label_attr['class'] : '').' required'); } ?>
        <?php if (!$compound) { $label_attr['for'] = $id; } ?>
        <?php if (!$label) { $label = !is_null($label_format)
            ? strtr($label_format, array('%name%' => $name, '%id%' => $id))
            : $formHelper->humanize($name); } ?>
        <label <?php foreach ($label_attr as $k => $v) { printf('%s="%s" ', $view->escape($k), $view->escape($v)); } ?>><?php
            echo $view->escape($translatorHelper->trans($label, array(), $translation_domain)) ?></label>
        <?php endif ?>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param array $attr
     * @param FormHelper $formHelper
     * @param $prototype
     */
    public function collection_widget($view, $form, $attr, $formHelper, $prototype = null)
    {
        ?>
        <?php if (!is_null($prototype)): ?>
                <?php $attr['data-prototype'] = $view->escape($formHelper->row($prototype)) ?>
            <?php endif ?>
            <?php echo $formHelper->widget($form, array('attr' => $attr)) ?>
        <?php
    }

    public function context_fields_collection_widget($form, $formHelper, $compound)
    {
        $this->form_widget($form, $formHelper, $compound);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param boolean $compound
     */
    public function form_widget($form, $formHelper, $compound)
    {
        ?>
        <?php if ($compound): ?>
            <?php echo $formHelper->block($form, 'form_widget_compound')?>
        <?php else: ?>
            <?php echo $formHelper->block($form, 'form_widget_simple')?>
        <?php endif ?>

        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param $errors
     */
    public function form_widget_compound($form, $formHelper, $errors)
    {
        ?>
        <div <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
            <?php if (!$form->parent && $errors): ?>
                <?php echo $formHelper->errors($form) ?>
            <?php endif ?>
            <?php echo $formHelper->block($form, 'form_rows') ?>
            <?php echo $formHelper->rest($form) ?>
        </div>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function form_rows($form, $formHelper)
    {
        ?>
        <?php foreach ($form as $child) : ?>
            <?php echo $formHelper->row($child) ?>
        <?php endforeach; ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function field_row($form, $formHelper)
    {
        /** @var FormView $form */
        ?>
        <div class="panel panel-info field-block js-field-block" <?php echo $formHelper->block($form, 'widget_container_attributes') ?>
            data-field-id="<?php echo $form->vars['id']; ?>"
            data-id="<?php echo $form->vars['name']; ?>">
            <div class="panel-body">
                <?php //echo $view['form']->label($form) ?>
                <?php echo $formHelper->errors($form) ?>
                <?php echo $formHelper->widget($form) ?>
                <?php //include __DIR__ . '/field_widget.html.php'; ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function field_widget($view, $form, $formHelper)
    {
        ?>
        <div class="field-props form-inline">
            <?php echo $formHelper->widget($form['name']); ?>
            <?php echo $formHelper->label($form['source']); ?>
            <?php echo $formHelper->widget($form['source']); ?>

            <?php if (!$form->vars['edit_mode_enabled']): ?>
                <?php echo '<strong>' . $view->escape($form['name']->vars['value']) . '</strong>' . ' (' . $form['source']->vars['value'] . ')'; ?>
            <?php endif; ?>
            <a href="#" class="js-show-vulns-link show-vulns-link">Show vulnerabilities</a>

            <div class="pull-right field-operations-block">
                <a href="#" class="js-remove-field remove-field-link">Remove</a>
            </div>
        </div>
        <div class="vulns-block js-vulns-block">
            <?php echo $formHelper->row($form['vulnTree'], $form['vulnTree']->vars); ?>
        </div>

        <?php echo $formHelper->rest($form); ?>
        <?php
    }

   /**
    * @param FormView $form
    * @param FormHelper $formHelper
    */
    public function vulnerable_element_row($form, $formHelper)
    {
        $this->conditional_vulnerable_element_row($form, $formHelper);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function form_row($form, $formHelper)
    {
        ?>
        <div>
            <?php echo $formHelper->label($form) ?>
            <?php echo $formHelper->errors($form) ?>
            <?php echo $formHelper->widget($form) ?>
        </div>
        <?php
    }

    /**
     * @param FormHelper $formHelper
     * @param FormView $form
     */
    public function form_rest($form, $formHelper)
    {
        ?>
        <?php foreach ($form as $child): ?>
            <?php if (!$child->isRendered()): ?>
                <?php echo $formHelper->row($child) ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php
    }

    public function collection_rest($view, $form)
    {
        $this->form_rest($view, $form);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function button_row($form, $formHelper)
    {
        ?>
        <div>
            <?php echo $formHelper->widget($form); ?>
        </div>
        <?php
    }

       /**
        * @param PhpEngine $view
        * @param $form
        * @param $label
        * @param $label_format
        * @param $name
        * @param $id
        * @param $type
        * @param $translation_domain
        * @param TranslatorHelper $translatorHelper
        * @param FormHelper $formHelper
        */
    public function button_widget($view, $form, $label, $name, $id, $translation_domain,
                                  $formHelper, $translatorHelper, $label_format = null, $type = null)
    {
        ?>
        <?php if (!$label) { $label = !is_null($label_format)
        ? strtr($label_format, array('%name%' => $name, '%id%' => $id))
        : $formHelper->humanize($name); } ?>
        <button type="<?php echo !is_null($type) ? $view->escape($type) : 'button' ?>" <?php
            echo $formHelper->block($form, 'button_attributes') ?>><?php echo $view->escape($translatorHelper->trans($label, array(), $translation_domain)) ?></button>

        <?php
    }

    /**
     * @param PhpEngine $view
     * @param $id
     * @param $full_name
     * @param $disabled
     * @param $attr
     * @param $translation_domain
     * @param TranslatorHelper $translatorHelper
     */
    public function button_attributes($view, $id, $full_name, $disabled, $attr, $translation_domain, $translatorHelper)
    {
        ?>
        id="<?php echo $view->escape($id) ?>" name="<?php echo $view->escape($full_name) ?>" <?php if ($disabled): ?>disabled="disabled" <?php endif ?>
        <?php foreach ($attr as $k => $v): ?>
            <?php if (in_array($v, array('placeholder', 'title'), true)): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($translatorHelper->trans($v, array(), $translation_domain))) ?>
            <?php elseif ($v === true): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($k)) ?>
            <?php elseif ($v !== false): ?>
                <?php printf('%s="%s" ', $view->escape($k), $view->escape($v)) ?>
            <?php endif ?>
        <?php endforeach ?>
        <?php
    }

    public function button_label()
    {

    }

    /**
     * @param FormView $form
     * @param string $type
     * @param FormHelper $formHelper
     */
    public function submit_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'button_widget',  array('type' => !is_null($type) ? $type : 'submit'));
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null|boolean $render_rest
     */
    public function form_end($form, $formHelper, $render_rest = null)
    {
        ?>
       <?php if (!is_null($render_rest) || $render_rest): ?>
           <?php echo $formHelper->rest($form) ?>
       <?php endif ?>
        </form>
        <?php
    }

    public function vuln_el_collection_rest($view, $form)
    {
        $this->form_rest($view, $form);
    }

    public function vulnerable_element_rest($view, $form)
    {
        $this->form_rest($view, $form);
    }

    public function conditional_vulnerable_element_rest($view, $form)
    {
        $this->form_rest($view, $form);
    }

    public function context_collection_rest($view, $form)
    {
        $this->form_rest($view, $form);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function attributes($form, $formHelper)
    {
        echo $formHelper->block($form, 'widget_attributes');
    }

    /**
     * @param PhpEngine $view
     * @param $form
     * @param $value
     * @param $checked
     * @param FormHelper $formHelper
     */
    public function checkbox_widget($view, $form, $value, $checked, $formHelper)
    {
        ?>
        <input type="checkbox"
            <?php echo $formHelper->block($form, 'widget_attributes') ?>
            <?php if (strlen($value) > 0): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?>
            <?php if ($checked): ?> checked="checked"<?php endif ?>
            />

        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function choice_option($form, $formHelper)
    {
        echo $formHelper->block($form, 'choice_widget_options');
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param boolean $expanded
     */
    public function choice_widget($form, $formHelper, $expanded)
    {
        ?>
        <?php if ($expanded): ?>
            <?php echo $formHelper->block($form, 'choice_widget_expanded') ?>
        <?php else: ?>
            <?php echo $formHelper->block($form, 'choice_widget_collapsed') ?>
        <?php endif ?>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param $required
     * @param $placeholder
     * @param $placeholder_in_choices
     * @param $multiple
     * @param $value
     * @param $preferred_choices
     * @param $choices
     * @param $separator
     * @param $translation_domain
     * @param FormHelper $formHelper
     * @param TranslatorHelper $translatorHelper
     */
    public function choice_widget_collapsed($view, $form, $required, $placeholder, $placeholder_in_choices, $multiple,
                                            $value, $preferred_choices, $choices, $separator, $translation_domain,
                                            $formHelper, $translatorHelper)
    {
        ?>
        <select
            <?php if ($required && null === $placeholder && $placeholder_in_choices === false && $multiple === false):
                $required = false;
            endif; ?>
            <?php echo $formHelper->block($form, 'widget_attributes', array(
                'required' => $required
            )) ?>
            <?php if ($multiple): ?> multiple="multiple"<?php endif ?>
            >
            <?php if (null !== $placeholder): ?><option value=""<?php if ($required and empty($value) && "0" !== $value):
                ?> selected="selected"<?php endif?>><?php echo $view->escape($translatorHelper->trans($placeholder, array(), $translation_domain))
                ?></option><?php endif; ?>
            <?php if (count($preferred_choices) > 0): ?>
                <?php echo $formHelper->block($form, 'choice_widget_options', array('choices' => $preferred_choices)) ?>
                <?php if (count($choices) > 0 && null !== $separator): ?>
                    <option disabled="disabled"><?php echo $separator ?></option>
                <?php endif ?>
            <?php endif ?>
            <?php echo $formHelper->block($form, 'choice_widget_options', array('choices' => $choices)) ?>
        </select>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function choice_widget_expanded($form, $formHelper)
    {
        ?>
        <div <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
            <?php foreach ($form as $child): ?>
                <?php echo $formHelper->widget($child) ?>
                <?php echo $formHelper->label($child) ?>
            <?php endforeach ?>
        </div>
        <?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param $choices
     * @param $translation_domain
     * @param $is_selected
     * @param $value
     * @param FormHelper $formHelper
     * @param TranslatorHelper $translatorHelper
     */
    public function choice_widget_options($view, $form, $choices, $translation_domain, $is_selected, $value, $formHelper,
                                          $translatorHelper)
    {
        ?>
        <?php foreach ($choices as $index => $choice): ?>
            <?php if (is_array($choice)): ?>
                <optgroup label="<?php echo $view->escape($translatorHelper->trans($index, array(), $translation_domain)) ?>">
                    <?php echo $formHelper->block($form, 'choice_widget_options', array('choices' => $choice)) ?>
                </optgroup>
            <?php else: ?>
                <option value="<?php echo $view->escape($choice->value) ?>"<?php if ($is_selected($choice->value, $value)):
                    ?> selected="selected"<?php endif?>><?php echo
                    $view->escape($translatorHelper->trans($choice->label, array(), $translation_domain)) ?></option>
            <?php endif ?>
        <?php endforeach ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function container_attributes($form, $formHelper)
    {
        echo $formHelper->block($form, 'widget_container_attributes');
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function form($form, $formHelper)
    {
        ?>
        <?php echo $formHelper->start($form) ?>
        <?php echo $formHelper->widget($form) ?>
        <?php echo $formHelper->end($form) ?>
        <?php
    }

    public function form_enctype($form)
    {
        if ($form->vars['multipart']): ?>enctype="multipart/form-data"<?php endif;
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function integer_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple', array('type' => !is_null($type) ? $type : "number"));
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param $money_pattern
     */
    public function money_widget($form, $formHelper, $money_pattern)
    {
        echo str_replace('{{ widget }}', $formHelper->block($form, 'form_widget_simple'), $money_pattern);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function number_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple',  array('type' => !is_null($type) ? $type : "text"));
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function password_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple',  array('type' => !is_null($type) ? $type : "password"));
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function percent_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple',  array('type' => !is_null($type) ? $type : "text")) ?> %<?php
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param $value
     * @param $checked
     * @param FormHelper $formHelper
     */
    public function radio_widget($view, $form, $value, $checked, $formHelper)
    {
        ?>
        <input type="radio"
            <?php echo $formHelper->block($form, 'widget_attributes') ?>
               value="<?php echo $view->escape($value) ?>"
            <?php if ($checked): ?> checked="checked"<?php endif ?>
            />
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function repeated_row($form, $formHelper)
    {
        echo $formHelper->block($form, 'form_rows');
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function reset_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'button_widget',  array('type' => !is_null($type) ? $type : 'reset'));
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function search_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple',  array('type' => !is_null($type) ? $type : "search"));
    }

    /**
     * @param PhpEngine $view
     * @param FormView $form
     * @param $value
     * @param FormHelper $formHelper
     */
    public function textarea_widget($view, $form, $value, $formHelper)
    {
        ?><textarea <?php echo $formHelper->block($form, 'widget_attributes') ?>><?php echo $view->escape($value) ?></textarea><?php
    }

    /**
     * @param FormView $form
     * @param $widget
     * @param $with_minutes
     * @param $with_seconds
     * @param FormHelper $formHelper
     */
    public function time_widget($form, $widget, $with_minutes, $with_seconds, $formHelper)
    {
        ?>
        <?php if ($widget == 'single_text'): ?>
            <?php echo $formHelper->block($form, 'form_widget_simple'); ?>
        <?php else: ?>
            <?php $vars = $widget == 'text' ? array('attr' => array('size' => 1)) : array() ?>
            <div <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
                <?php
                // There should be no spaces between the colons and the widgets, that's why
                // this block is written in a single PHP tag
                echo $formHelper->widget($form['hour'], $vars);

                if ($with_minutes) {
                    echo ':';
                    echo $formHelper->widget($form['minute'], $vars);
                }

                if ($with_seconds) {
                    echo ':';
                    echo $formHelper->widget($form['second'], $vars);
                }
                ?>
            </div>
        <?php endif ?>

    <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     * @param null $type
     */
    public function url_widget($form, $formHelper, $type = null)
    {
        echo $formHelper->block($form, 'form_widget_simple',  array('type' => !is_null($type) ? $type : "url"));
    }

    /**
     * @param PhpEngine $view
     * @param $label
     * @param $required
     * @param $compound
     * @param $id
     * @param $name
     * @param $translation_domain
     * @param $label_attr
     * @param FormHelper $formHelper
     * @param TranslatorHelper $translatorHelper
     * @param null $label_format
     */
    public function condition_label($view, $label, $required, $compound, $id, $name, $translation_domain, $label_attr,
                                    $formHelper, $translatorHelper, $label_format = null)
    {
        /** @var FormView $form */
        ?>
        <?php if (false !== $label): ?>
        <?php if ($required) { $label_attr['class'] = trim((isset($label_attr['class']) ? $label_attr['class'] : '').' required'); } ?>
        <?php if (!$compound) { $label_attr['for'] = $id; } ?>
        <?php if (!$label) { $label = !is_null($label_format)
                ? strtr($label_format, array('%name%' => $name, '%id%' => $id))
                : $formHelper->humanize($name); } ?>
            <label <?php foreach ($label_attr as $k => $v) { printf('%s="%s" ', $view->escape($k), $view->escape($v)); }
                ?>><?php echo $view->escape($translatorHelper->trans($label, array(), $translation_domain)) ?>: </label>
        <?php endif ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function vulnerability_row($form, $formHelper)
    {
        /** @var FormView $form */
        ?>
        <?php //echo $form['name']->vars['value']; ?>
        <?php echo $formHelper->errors($form) ?>
        <?php echo $formHelper->widget($form) ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function vulnerability_widget($form, $formHelper)
    {
        ?>
        <div class="vulnerability js-vulnerability vuln-<?php echo strtolower($form['name']->vars['value']); ?> form-inline"
             data-name="<?php echo $form['name']->vars['value']; ?>"
            <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
            <label>
                <?php
                echo $formHelper->widget($form['enabled']);
                echo ' <strong>' . $form['name']->vars['value'] . '</strong> ';
                ?>
            </label>
            <div class="js-vuln-attrs vuln-attrs form-inline">
                <?php
                /** @var FormView $child */
                foreach ($form as $child) {
                    if ($child->isRendered() || in_array($child->vars['name'], ['name', 'enabled'])) {
                        continue;
                    }

                    echo '<span class="vuln-attr">' . ($child->vars['label'] !== false ? $formHelper->label($child) . ': ' : '')
                        . $formHelper->widget($child) . '</span>';
                }
                ?>
            </div>
            <?php echo $formHelper->rest($form); ?>
            <div class="pull-right vuln-operations-block">
                <a href="#" class="js-remove-vulnerability remove-vulnerability-link">Remove</a>
            </div>
        </div>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function all_types($form, $formHelper)
    {
        ?>
        <?php
        $allVulns = [];
        $fieldVulns = [];
        ?>

        <?php foreach ($form['vulnerabilities']->children as $child) {
            $vulnHtml = $formHelper->row($child);
            $vulnHtml = str_replace(
                ['form___ALL_VULNS__', 'form[__ALL_VULNS__]'],
                ['{{ id }}', '{{ name }}'],
                $vulnHtml);
            $allVulns[] = $child['name']->vars['value'];
            if (strpos($child['targetsString']->vars['value'], 'field') !== false) {
                $fieldVulns[] = $child['name']->vars['value'];
            }
            ?>
            <script type="text/x-handlebars" charset="UTF-8" id="tplVulnerability_<?php echo $child['name']->vars['value']; ?>"
                    class="js-template js-vulnerability-template" data-vulnerability="<?php echo $child['name']->vars['value']; ?>"
                    data-targets="<?php echo $child['targetsString']->vars['value']; ?>">
                <?php echo $vulnHtml."\n"; ?>
            </script>
        <?php } ?>
        <script type="text/javascript">
            VulnInfo = typeof VulnInfo == 'object' ? VulnInfo : {};
            VulnInfo.vulns = ['<?php echo implode("', '", $allVulns) ?>'];
            VulnInfo.fieldVulns = ['<?php echo implode("', '", $fieldVulns) ?>'];
        </script>
        <?php echo $formHelper->rest($form); ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function all_conditions($form, $formHelper)
    {
        ?>
        <?php
        $allConditions = [];
        ?>
        <?php foreach ($form['conditions']->children as $child) {
        $conditionHtml = $formHelper->row($child);
        $conditionHtml = str_replace(
            ['form___ALL_CONDITIONS__', 'form[__ALL_CONDITIONS__]'],
            ['{{ id }}', '{{ name }}'],
            $conditionHtml);
        $allConditions[] = $child['name']->vars['value'];
        ?>
            <script type="text/x-handlebars" charset="UTF-8" id="tplCondition_<?php echo $child['name']->vars['value']; ?>"
                    class="js-template js-condition-template" data-condition="<?php echo $child['name']->vars['value']; ?>">
                <?php echo $conditionHtml."\n"; ?>
            </script>
        <?php } ?>
        <script type="text/javascript">
            VulnInfo = typeof VulnInfo == 'object' ? VulnInfo : {};
            VulnInfo.conditions = ['<?php echo implode("', '", $allConditions) ?>'];
        </script>
        <?php echo $formHelper->rest($form); ?>
        <?php
    }

    /**
     * @param FormView $form
     * @param $errors
     * @param FormHelper $formHelper
     */
    public function condition_row($form, $errors, $formHelper)
    {
        ?>
        <div class="js-condition-row condition-row" data-name="<?php echo $form['name']->vars['value']; ?>">
            <?php echo $formHelper->label($form) ?>
            <?php echo $formHelper->errors($form) ?>
            <div class="pull-right condition-operations-block">
                <a href="#" class="js-remove-condition remove-condition-link">Remove</a>
            </div>
            <div <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
                <?php if (!$form->parent && $errors): ?>
                    <?php echo $formHelper->errors($form) ?>
                <?php endif ?>
                <?php echo $formHelper->block($form, 'form_rows') ?>
                <?php echo $formHelper->rest($form) ?>
            </div>
        </div>

        <?php
    }

    /**
     * @param FormView $form
     * @param $errors
     * @param FormHelper $formHelper
     */
    public function condition_widget($form, $errors, $formHelper)
    {
        ?>
        <div <?php echo $formHelper->block($form, 'widget_container_attributes') ?>>
            <?php if (!$form->parent && $errors): ?>
                <?php echo $formHelper->errors($form) ?>
            <?php endif ?>
            <?php echo $formHelper->block($form, 'form_rows') ?>
            <?php echo $formHelper->rest($form) ?>
        </div>

        <?php
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function context_rest($form, $formHelper)
    {
        $this->form_rest($form, $formHelper);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function vuln_el_collection_row($form, $formHelper)
    {
        if ($form->count()) {
            echo $formHelper->label($form);
        }
        echo $formHelper->errors($form);
        echo $formHelper->widget($form);
        echo $formHelper->rest($form);
    }

    /**
     * @param FormView $form
     * @param FormHelper $formHelper
     */
    public function matrix_filter_form($form, $formHelper)
    {
        echo $formHelper->form($form);
    }

    /**
     * @param $form FormView
     * @param FormHelper $formHelper
     */
    public function context_collection_row($form, $formHelper)
    {
        if ($form->count()) {
            echo $formHelper->label($form);
        }
        echo $formHelper->errors($form);
        echo $formHelper->widget($form);
        echo $formHelper->rest($form);
    }
}