<?php

namespace AppCore\View\Widget;

use Cake\View\Widget\TextareaWidget as BaseTextarea;
use Cake\View\Form\ContextInterface;

class TextareaWidget extends BaseTextarea{
	private $_label;

	public function __construct($templates, $label) {
		parent::__construct($templates);
		$this->_label = $label;
	}

	/**
	 * @see Cake\View\Widget\Checkbox::render();
	 */
	public function render(array $data, ContextInterface $context){
		$labelData['text'] = $data['label'];

		$label = $this->_label->render($labelData, $context);
		unset($data['label']);
		if(!isset($data['class']))
			$data['class'] = '';

		if(!isset($data['rows']))
			$data['rows'] = 5;
		
		$data['class'] .= ' form-control';
		$textarea = parent::render($data, $context);

		return $this->_templates->format('textareaWrapper', ['input' => $textarea, 'label' => $label]);
	}
}