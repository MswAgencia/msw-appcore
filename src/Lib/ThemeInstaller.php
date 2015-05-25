<?php

namespace AppCore\Lib;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Core\Plugin;

/**
 * Classe responsável pela instalação dos assets de um tema.
 */
class ThemeInstaller {
	
	public function installTheme($theme) {
		if(!Plugin::loaded($theme))
			throw new \Exception('Plugin não foi carregado.');

		if(!file_exists(WWW_ROOT . 'theme' . DS . $theme) or Configure::read('debug')) {
			$handler = new Folder();
			$handler->copy([
				'to' =>	WWW_ROOT . 'theme' . DS . $theme,
				'from' => Plugin::path($theme) . 'webroot' . DS . '_assets',
				'mode' => 0755,
				'scheme' => Folder::OVERWRITE
				]);

			return true;
		}
		return false;
	}
}
