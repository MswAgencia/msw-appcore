<?php
namespace AppCore\Lib\Rest;

use AppCore\Lib\Rest\RestApiRequesterInterface;
use Cake\Network\Http\Client;

/**
 * @todo Escrever documentação
 * Class RestApiRequester
 * @package AppCore\Lib\Rest
 */
class RestApiRequester implements RestApiRequesterInterface {
	private $Socket = null;

	public function __construct($host, $auth = null){
		$this->Socket = new Client([
			'host' => $host,
			'scheme' => 'http',
			'auth' => $auth,
			]);
	}

    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return \Cake\Network\Http\Response
     */
	protected function _create($url, $data = [], $options = []){
        if(!isset($options['type']))
            $options['type'] = 'json';

		return $this->Socket->post($url, json_encode($data), $options);
	}

    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return \Cake\Network\Http\Response
     */
	protected function _update($url, $data = [], $options = []){
        if(!isset($options['type']))
            $options['type'] = 'json';

        return $this->Socket->patch($url, json_encode($data), $options);
	}

    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return \Cake\Network\Http\Response
     */
	protected function _read($url, $data = [], $options = []){
        if(!isset($options['type']))
            $options['type'] = 'json';

        return $this->Socket->get($url, $data, $options);
	}

    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return \Cake\Network\Http\Response
     */
	protected function _delete($url, $data = [], $options = []){
        if(!isset($options['type']))
            $options['type'] = 'json';

        return $this->Socket->delete($url, json_encode($data), $options);
	}
}