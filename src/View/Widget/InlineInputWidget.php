<?php

namespace AppCore\View\Widget;

use Cake\View\Widget\WidgetInterface;
use Cake\View\Form\ContextInterface;
use Cake\View\Helper\IdGeneratorTrait;

class InlineInputWidget implements WidgetInterface{
	use IdGeneratorTrait;

	protected $_templates;

	public function __construct($templates) {
		$this->_templates = $templates;
	}

	public function render(array $data, ContextInterface $context){
		$data['input'] += ['type' => 'text'];
		$data['container'] += ['class' => $data['container']['col']];
		$data['input'] = ['name' => $data['input']['name'], 'type' => $data['input']['type'], 'attrs' => $this->_templates->formatAttributes($data['input'], ['name', 'type'])];
		unset($data['container']['col']);

		return $this->_templates->format('inlineinput', [
			'input' => $this->_templates->format('input', $data['input']),
			'attrs' => $this->_templates->formatAttributes($data['container'])
			]);
	}

	public function secureFields(array $data){
		return [];
	}
}

?>