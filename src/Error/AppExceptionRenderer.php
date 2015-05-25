<?php

namespace AppCore\Error;

use Cake\Error\ExceptionRenderer;
use Exception;

class AppExceptionRenderer extends ExceptionRenderer {

	public function __construct(Exception $exception){
		parent::__construct($exception);
	}

	public function render() {
		$code = $this->_code($this->error);
		if($code == 404)
			$this->controller->redirect('/pagina-nao-encontrada');

		return parent::render();
	}
}