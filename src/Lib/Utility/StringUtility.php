<?php


namespace AppCore\Lib\Utility;
use Cake\Utility\Inflector;
use Cake\Utility\Hash;

/**
 * Classe de utilidades para manipulação de strings.
 *
 * @package AppCore\Lib\Utility
 * @author Bruno Schmidt <bruno@masterstudioweb.com.br>
 */
class StringUtility {

	/**
	 * Gera um slug para o $text.
	 * Os espaços serão substituidos por hífens e as acentuações serão removidas. Também ficará tudo em lowercase.
	 * @param  string $text A string a ser transformada em slug.
	 * @return string       O slug.
	 */
	public function slug($text){
		$slug = Inflector::slug($text, '-');
		$slug = strtolower($slug);
		return $slug;
	}

	/**
	 * Transforma uma string em array com base no $separator.
	 * @param  string $string A string a ser transformada em array.
	 * @param  string $separator O separador para definir o que cortará a string e definir cada elemento do array.
	 * @return  Array O array.
	 */
	public function strToArray($string, $separator = ','){
		$theArray = explode($separator, $string);
		$theArray = Hash::map($theArray, '{n}', 'trim');
		return $theArray;
	}
}