<?php
namespace AppCore\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Components helper
 */
class ComponentsHelper extends Helper {

/**
 * Default configuration.
 *
 * @var array
 */
	protected $_defaultConfig = [];

	public $helpers = ['Html'];


	/**
	 * Retorna um span com as classes de glyphicon, pronto para ser escrito.
	 * @param  string $iconName O nome da classe do glyphicon se 'glyphicon-'. Ex.: existe o glyphicon glyphicon-ok, então chamamos ComponentsHelper::getIcon('ok');
	 * @param  [type] $options Um array de options para ser usado em Html::tag(). As opções serão atribuidas ao 'span'.
	 * @return string O span pronto para ser usado como ícone.
	 */
	public function getIcon($iconName, $options = []){
		
		$options = $this->addClass($options ,'glyphicon');
		$options = $this->addClass($options , 'glyphicon-' . $iconName);

		return $this->Html->tag('span', '', $options) . ' ';
	}
}
