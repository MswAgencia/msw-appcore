<?php

namespace AppCore\View\Widget;

use Cake\View\Widget\BasicWidget as BaseBasicWidget;
use Cake\View\Form\ContextInterface;

class BasicWidget extends BaseBasicWidget{

	public function __construct($templates) {
		parent::__construct($templates);
	}

	/**
	 * @see Cake\View\Widget\Checkbox::render();
	 */
	public function render(array $data, ContextInterface $context){
		return parent::render($data, $context);
	}
}

?>