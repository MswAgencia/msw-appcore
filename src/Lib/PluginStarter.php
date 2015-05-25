<?php
namespace AppCore\Lib;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Utility\Text;
use Cake\Core\Plugin;
use Cake\Core\Configure;

/**
 * Responsável pela inicialização dos plugins.
 * Faz uma verificação se o plugin está 'instalado'. Não estando, é feita a instalação padrão do plugin para que seja configurado através do painel.
 * @version 1.0.0
 */
class PluginStarter{
	/**
	 * Diretório onde os arquivos dos plugins serão colocados quando forem instalados.
	 * É preenchido quando o objeto é criado pelo construtor.
	 * @var string
	 */
	protected $pluginInstallationFolder = null;


	public function __construct(){
		$this->pluginInstallationFolder = ROOT . DS . 'config' . DS . 'Plugins' . DS;
	}

	/**
	 * Faz uma instalação básica do plugin.
	 * A instalação consiste em criar o arquivo de configurações do plugin no diretório apropriado.
	 * Este método é chamado pelo método PluginStarter::load() quando necessário.
	 * 
	 * @param string $pluginName O nome do plugin a ser instalado.
	 * @return void             
	 */
	public function install($pluginName){
		$settingsFileFolder = $this->pluginInstallationFolder . $pluginName . DS;
		if(Plugin::loaded($pluginName)):
			$defaultFile = Plugin::path($pluginName) . 'config' . DS . 'default_settings.php';
			$folderHandler = new Folder();
			if(!$folderHandler->cd($settingsFileFolder))
				$folderHandler->create($settingsFileFolder);
			$fileHandler = new File($defaultFile);
			$fileHandler->copy($settingsFileFolder . 'settings.php');
			$fileHandler->close();
		endif;
	}

	/**
	 * Wrapper para Configure::load().
	 * Faz uma verificação para ver se o plugin está instalado. (@see PluginStarter::install()).
	 * 
	 * @param string $pluginName O nome do plugin a ser carregado.
	 * @return bool              O retorno do chamado Configure::load().
	 */
	public function load($pluginName){
		$settingsFile = $this->pluginInstallationFolder . $pluginName . DS . 'settings.php';
		if(!file_exists($settingsFile))
			$this->install($pluginName);

		$configPath = Text::insert('Plugins/:plugin/settings', ['plugin' => $pluginName]);
		return Configure::load($configPath);
	}
}