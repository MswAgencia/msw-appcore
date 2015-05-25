<?php
namespace AppCore\Lib;

class ImageUploaderConfig {
	public $width;
	public $height;
	public $mode;

	/**
	 * [__construct description]
	 */
	public function __construct($width = 100, $height = 100, $mode = 'resize') {
		$this->width = $width;
		$this->height = $height;
		$this->mode = $mode;
	}

	/**
	 * [getWidth description]
	 * @return [type] [description]
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * [getHeight description]
	 * @return [type] [description]
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * [getMode description]
	 * @return [type] [description]
	 */
	public function getMode() {
		return $this->mode;
	}
}
