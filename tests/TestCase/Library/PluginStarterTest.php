<?php

namespace AppCore\Test\TestCase\Library;

use Cake\TestSuite\TestCase;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Plugin;
use AppCore\Lib\PluginStarter;
use Cake\Core\Configure;

/**
 * Classe de testes para AppCore\Lib\PluginStarter;
 * @author bruno <bruno@masterstudioweb.com.br>
 */
class PluginStarterTest extends TestCase {
	const PLUGIN_FOLDER_PERMISSIONS = 0755;
	const CREATE_FOLDER_IF_NOT_EXISTS = true;

	/**
	 * Cria um plugin de teste e o carrega para conseguir rodar os testes.
	 */
	public function setUp() {
		parent::setUp();

		$testPluginData = [
			'full_path' => ROOT . DS . 'plugins' . DS . 'PluginInstallerTest' . DS,
			'config_folder' => ROOT . DS . 'plugins' . DS . 'PluginInstallerTest' . DS . 'config' . DS
		];

		$pluginFolder = new Folder($testPluginData['full_path'], self::CREATE_FOLDER_IF_NOT_EXISTS, self::PLUGIN_FOLDER_PERMISSIONS);
		$pluginFolder->create('config');

		$defaultSettingsFile = new File($testPluginData['config_folder'] . 'default_settings.php', true);
		$defaultSettingsFile->write("<?php 
			return [
				'MyApplication.Modules.PluginInstallerTest.Settings' => 
					['Default' => true]
				]; 
		?>");
		$defaultSettingsFile->close();
		$bootstrapFile = new File($testPluginData['config_folder'] . 'bootstrap.php', true);
		$bootstrapFile->close();
		Plugin::load('PluginInstallerTest', ['routes' => false, 'bootstrap' => false]);
	}

	public function testIfTestPluginFilesWereCreated() {
		$this->assertEquals(file_exists(ROOT . DS . 'plugins' . DS . 'PluginInstallerTest'), true);
	}
	public function testIfTestPluginWasLoadedOnSetUp() {
		$isLoaded = Plugin::loaded('PluginInstallerTest');
		$this->assertEquals($isLoaded, true);
	}

	public function testIfTestPluginBootstrapFileExists() {
		$this->assertEquals(file_exists(ROOT . DS . 'plugins' . DS . 'PluginInstallerTest' . DS . 'config' . DS . 'bootstrap.php'), true);
	}

	public function testPluginInstall() {
		$starter = new PluginStarter();
		$starter->install('PluginInstallerTest');

		$this->assertEquals(file_exists(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'PluginInstallerTest' . DS . 'settings.php'), true);

		$defaultSettingsFile = new File(ROOT . DS . 'plugins' . DS . 'PluginInstallerTest' . DS . 'config' . DS . 'default_settings.php');
		$installedSettingsFile = new File(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'PluginInstallerTest' . DS . 'settings.php');

		$this->assertEquals($defaultSettingsFile->md5(), $installedSettingsFile->md5());
		$defaultSettingsFile->close();
		$installedSettingsFile->close();
	}

	public function testLoadingASuccefullyInstalledPluginSettings() {
		$starter = new PluginStarter();
		$result = $starter->load('PluginInstallerTest');

		$this->assertEquals($result, true);

		$this->assertEquals(Configure::read('MyApplication'), ['Modules' => ['PluginInstallerTest' => ['Settings' => ['Default' => true]]]]);
	}

	public function testInstalledPluginSettingsFileWillNotBeOverrridenOnLoad() {
		$starter = new PluginStarter();
		$starter->load('PluginInstallerTest');

		$installedSettingsFile = new File(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'PluginInstallerTest' . DS . 'settings.php');
		$defaultSettingsFile = new File(ROOT . DS . 'plugins' . DS . 'PluginInstallerTest' . DS . 'config' . DS . 'default_settings.php');

		$installedSettingsFile->write("<?php 
			return [
				'MyApplication.Modules.PluginInstallerTest.Settings' => 
					['Default' => false]
				]; 
		?>");
		$installedSettingsFile->close();

		$starter->load('PluginInstallerTest');
		$installedSettingsFile = new File(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'PluginInstallerTest' . DS . 'settings.php');

		$this->assertEquals(strcmp($installedSettingsFile->md5(), $defaultSettingsFile->md5()) === 0, false);
		$installedSettingsFile->close();
		$defaultSettingsFile->close();
	}

	public function tearDown() {
		parent::tearDown();

		$testPluginFolder = new Folder(ROOT . DS . 'plugins' . DS . 'PluginInstallerTest');
		$testPluginFolder->delete();

		$testPluginConfigFolder = new Folder(ROOT . DS . 'config' . DS . 'Plugins' . DS . 'PluginInstallerTest');
		$testPluginConfigFolder->delete();
	}

}