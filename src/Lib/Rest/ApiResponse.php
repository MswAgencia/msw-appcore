<?php

namespace AppCore\Lib\Rest;

use AppCore\Lib\Rest\ApiResponseInterface;
use Cake\Utility\Xml;
use Cake\Log\Log;

class ApiResponse implements ApiResponseInterface {
	protected $_response = null;

	public function __construct(\Cake\Network\Http\Response $response){
		$this->_response = $response;
	}

	/**
	 * 
	 * @return \SimpleXml Um objeto SimpleXml com os dados da resposta.
	 */
	public function toXml(){
		return $this->_response->xml;
	}

	/**
	 * 	
	 * @return array Um array com os dados da resposta.
	 */
	public function toArray(){
		if(is_null($this->_response->json)) {
			Log::write('debug', print_r($this->_response->body));
			return ['success' => false, 'message' => 'Erro na resposta recebida do servidor.'];
		}
		return $this->_response->json;
	}
	
	/**
	 * 
	 * @return string/json Uma string no formato json.
	 */
	public function toJson(){
		return json_encode($this->_response->json, JSON_PRETTY_PRINT);
	}

	/**
	 * Retorna o objeto Response original do CakePHP
	 * @return Cake\Network\Http\Response O objeto Response original do CakePHP
	 */
	public function getResponse(){
		return $this->_response;
	}

	public function __toString(){

	}
}