<?php
namespace AppCore\Lib\Rest;

interface ApiResponseInterface {
	/**
	 * 
	 * @return \SimpleXml Um objeto SimpleXml com os dados da resposta.
	 */
	public function toXml();

	/**
	 * 
	 * @return string/json Uma string no formato json.
	 */
	public function toJson();

	/**
	 * Retorna o objeto Response original do CakePHP
	 * @return Cake\Network\Http\Response O objeto Response original do CakePHP
	 */
	public function getResponse();

	public function __toString();
}