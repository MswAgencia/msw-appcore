<?php

namespace AppCore\View\Widget;

use Cake\View\Widget\SelectBoxWidget as BaseSelectBox;
use Cake\View\Form\ContextInterface;

class SelectBoxWidget extends BaseSelectBox{
	private $_label;

	public function __construct($templates, $label) {
		parent::__construct($templates);
		$this->_label = $label;
	}

	/**
	 * @see Cake\View\Widget\Checkbox::render();
	 */
	public function render(array $data, ContextInterface $context){
		if(!empty($data['label']))
			$label = $this->_label->render(['text' => $data['label']], $context);
		unset($data['label']);
		
		if(!isset($data['class']))
			$data['class'] = '';
		$data['class'] .= ' form-control';

		$select = parent::render($data, $context);
		
		if(!isset($label))
			return $this->_templates->format('selectWrapperNoLabel', ['input' => $select]);

		return $this->_templates->format('selectWrapper', [
			'label' => $label,
			'input' => $select
		]);

	}
}