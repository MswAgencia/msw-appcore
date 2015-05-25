<?php

namespace AppCore\View\Widget;

use Cake\View\Widget\CheckboxWidget as BaseCheckbox;
use Cake\View\Form\ContextInterface;

class CheckboxWidget extends BaseCheckbox{
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
		unset($data['label']);
		$checkbox = parent::render($data, $context);

		$labelData['text'] = $checkbox . $labelData['text'];
		$labelData['escape'] = false;

		$labeledCheckbox = $this->_label->render($labelData, $context);



		return $this->_templates->format('checkboxWrapper', [
			'checkbox' => $labeledCheckbox
		]);
	}
}