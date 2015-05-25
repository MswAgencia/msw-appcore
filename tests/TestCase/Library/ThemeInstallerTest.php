<?php

namespace AppCore\Test\TestCase\Library;

use Cake\TestSuite\TestCase;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Plugin;
use AppCore\Lib\ThemeInstaller;
use AppCore\Lib\PluginStarter;
use Cake\Core\Configure;

/**
 * Classe de testes para AppCore\Lib\ThemeInstaller;
 * @author bruno <bruno@masterstudioweb.com.br>
 */
class ThemeInstallerTest extends TestCase {
	const PLUGIN_FOLDER_PERMISSIONS = 0755;
	const CREATE_FOLDER_IF_NOT_EXISTS = true;

	/**
	 * Cria um plugin de teste e o carrega para conseguir rodar os testes.
	 */
	public function setUp() {
		parent::setUp();

		$testData = [
			'full_path' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS,
			'config_folder' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS . 'config' . DS,
			'css_folder' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS . 'webroot' . DS . '_assets' . DS . 'css' . DS,
			'js_folder' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS . 'webroot' . DS . '_assets' . DS . 'js' . DS,
			'img_folder' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS . 'webroot' . DS . '_assets' . DS . 'img' . DS,
			'packages_folder' => ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest' . DS . 'webroot' . DS . '_assets' . DS . 'packages' . DS,
		];

		$pluginFolder = new Folder($testData['full_path'], self::CREATE_FOLDER_IF_NOT_EXISTS, self::PLUGIN_FOLDER_PERMISSIONS);
		$pluginFolder->create($testData['config_folder']);
		$pluginFolder->create($testData['css_folder']);
		$pluginFolder->create($testData['js_folder']);
		$pluginFolder->create($testData['img_folder']);
		$pluginFolder->create($testData['packages_folder'] . 'sample_package');

		$defaultSettingsFile = new File($testData['config_folder'] . 'default_settings.php', true);
		$defaultSettingsFile->write("<?php 
			return [
				'MyApplication.Modules.ThemeInstallerTest.Settings' => 
					['Default' => true]
				]; 
		?>");
		$defaultSettingsFile->close();

		$file = new File($testData['css_folder'] . 'sample.css', true);
		$file->write('#id { }');
		$file->close();
		$file = new File($testData['js_folder'] . 'sample.js', true);
		$file->write('#id { }');
		$file->close();

		$file = new File($testData['packages_folder'] . 'sample_package' . DS . 'sample.css', true);
		$file->write('#id { }');
		$file->close();

		$file = new File($testData['packages_folder'] . 'sample_package' . DS . 'sample.js', true);
		$file->write('#id { }');
		$file->close();


		$bootstrapFile = new File($testData['config_folder'] . 'bootstrap.php', true);
		$bootstrapFile->close();
	}

	public function testIfThemeFilesWereInstalled() {
		Plugin::load('ThemeInstallerTest', ['routes' => false, 'bootstrap' => false]);
		$starter = new PluginStarter();
		$starter->load('ThemeInstallerTest');

		$themeInstaller = new ThemeInstaller();
		$themeInstaller->installTheme('ThemeInstallerTest');

		$this->assertEquals(true, file_exists(WWW_ROOT . 'theme' . DS . 'ThemeInstallerTest' . DS . 'css' . DS . 'sample.css'));
		$this->assertEquals(true, file_exists(WWW_ROOT . 'theme' . DS . 'ThemeInstallerTest' . DS . 'js' . DS . 'sample.js'));
		$this->assertEquals(true, file_exists(WWW_ROOT . 'theme' . DS . 'ThemeInstallerTest' . DS . 'packages' . DS . 'sample_package' . DS . 'sample.css'));
		$this->assertEquals(true, file_exists(WWW_ROOT . 'theme' . DS . 'ThemeInstallerTest' . DS . 'packages' . DS . 'sample_package' . DS . 'sample.js'));
	}

	public function tearDown() {
		parent::tearDown();
		Plugin::unload('ThemeInstallerTest');
		$testPluginFolder = new Folder(ROOT . DS . 'plugins' . DS . 'ThemeInstallerTest');
		$testPluginFolder->delete();


		$testPluginConfigFolder = new Folder(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'ThemeInstallerTest');
		$testPluginConfigFolder->delete();

		$webrootThemeFolder = new Folder(WWW_ROOT . 'theme' . DS . 'ThemeInstallerTest');
		$webrootThemeFolder->delete();
	}
}