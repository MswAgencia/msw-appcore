<?php
namespace AppCore\View\Helper;

use Cake\View\Helper\FormHelper as CakeFormHelper;
use Cake\View\View;

class FormHelper extends CakeFormHelper {

	protected $_defaultConfig = [
		'errorClass' => 'form-error',
		'typeMap' => [
			'string' => 'text', 'datetime' => 'datetime', 'boolean' => 'checkbox',
			'timestamp' => 'datetime', 'text' => 'textarea', 'time' => 'time',
			'date' => 'date', 'float' => 'number', 'integer' => 'number',
			'decimal' => 'number', 'binary' => 'file', 'uuid' => 'string'
		],
		'templates' => [
			'button' => '<button{{attrs}}>{{text}}</button>',
			'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
			'checkboxWrapper' => '<div class="form-group"><div class="checkbox">{{checkbox}}</div></div>',
			'errorList' => '<ul>{{content}}</ul>',
			'errorItem' => '<li>{{text}}</li>',
			'file' => '<input type="file" name="{{name}}"{{attrs}}>',
			'fieldset' => '<fieldset>{{content}}</fieldset>',
			'formStart' => '<form{{attrs}}>',
			'formEnd' => '</form>',
			'formGroup' => '{{label}}{{input}}',
			'hiddenblock' => '<div style="display:none;">{{content}}</div>',
			'inlineinput' => '<div{{attrs}}>{{input}}</div>',
			'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
			'inputSubmit' => '<input type="{{type}}"{{attrs}}>',
			'label' => '<label{{attrs}}>{{text}}</label>',
			'legend' => '<legend>{{text}}</legend>',
			'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
			'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
			'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
			'selectWrapper' => '<div class="form-group">{{label}}{{input}}</div>',
			'selectWrapperNoLabel' => '<div class="form-group">{{input}}</div>',
			'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
			'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
			'radioWrapper' => '{{input}}{{label}}',
			'textarea' => '<textarea name="{{name}}" {{attrs}}>{{value}}</textarea>',
			'textareaWrapper' => '<div class="form-group">{{label}}{{input}}</div>',
			'dateWidget' => '<div class="row">
				<div class="col-sm-3">{{year}}</div>
				<div class="col-sm-3">{{month}}</div>
				<div class="col-sm-3">{{day}}</div>
				<div class="col-sm-3">{{hour}}</div>
				<div class="col-sm-3">{{minute}}</div>
				<div class="col-sm-3">{{second}}</div>
				<div class="col-sm-3">{{meridian}}</div>
			</div>',
			'error' => '<div class="help-block">{{content}}</div>',
			'submitContainer' => '{{content}}',
			'inputContainer' => '<div class="form-group{{required}}">{{content}}</div>',
			'inputContainerError' => '<div class="form-group has-error has-feedback {{type}}{{required}}">{{content}}<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>{{error}}</div>',
		]
	];

	/**
	 * Default widgets
	 *
	 * @var array
	 */
		protected $_defaultWidgets = [
			'button' => ['Cake\View\Widget\ButtonWidget'],
			'checkbox' => ['AppCore\View\Widget\CheckboxWidget', 'label'],
			'file' => ['Cake\View\Widget\FileWidget'],
			'label' => ['Cake\View\Widget\LabelWidget'],
			'nestingLabel' => ['Cake\View\Widget\NestingLabelWidget'],
			'multicheckbox' => ['Cake\View\Widget\MultiCheckboxWidget', 'nestingLabel'],
			'radio' => ['Cake\View\Widget\RadioWidget', 'nestingLabel'],
			'select' => ['Cake\View\Widget\SelectBoxWidget'],
			'textarea' => ['AppCore\View\Widget\TextareaWidget', 'label'],
			'select' => ['AppCore\View\Widget\SelectBoxWidget', 'label'],
			'datetime' => ['Cake\View\Widget\DateTimeWidget', 'AppCore\View\Widget\SelectBoxWidget'],
			'_default' => ['AppCore\View\Widget\BasicWidget'],
			'inlineInput' => ['AppCore\View\Widget\InlineInputWidget']
		];

    public function __construct(View $view, $config = []) {
        parent::__construct($view, $config);
    }

    public function label($fieldName, $text = null, array $options = []) {

		$options = $this->addClass($options, 'control-label');

    	return parent::label($fieldName, $text, $options);
    }

	protected function _getInput($fieldName, $options)
	{
		if (isset($options['type']) && !in_array($options['type'], ['radio', 'checkbox', 'datetime']))
		{
			$options = $this->addClass($options, 'form-control');
		}

		return parent::_getInput($fieldName, $options);
	}

	public function inlineInput($fieldName, $options = []){
		trigger_error('Não usar', E_USER_WARNING);
		$options['input']['name'] = $fieldName;
		$options['input'] = $this->addClass($options['input'], 'form-control');
		
		if(!isset($options['container']))
			$options['container']['col'] = 'col-md-3';
		return $this->widget('inlineInput', $options);
	}
}
