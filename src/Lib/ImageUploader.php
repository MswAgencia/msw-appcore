<?php

namespace AppCore\Lib;

use AppCore\Lib\Cropper;
use AppCore\Lib\FileUploader;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use AppCore\Lib\ImageUploaderConfig;
use Cake\Utility\Text;

class ImageUploader{
	private $data = null;
	private $path = null;
	private $fullPath = null;
	public $mode = 'resize';
	public $width = 100;
	public $height = 100;
	private $Cropper = null;
	private $Uploader = null;
	private $uploadedImage = null;

	public function __construct(){
		$this->Cropper = new Cropper();
		$this->Uploader = new FileUploader();
		$this->Uploader->allowed_types('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
	}

	public function setData(array $data){
		if(!isset($data['error']))
			throw new \Exception('Erro nas informações de upload ($_FILES). Verifique se o "enctype" do form é "multipart/form-data"');
		
		if($data['error'] !== UPLOAD_ERR_OK)
			return false;

		$this->data = $data;
		return true;
	}

	/**
	 * OBS.: Procure sempre informar $path sem começar com separador de diretórios a partir de Site.webroot/img.
	 * Por exemplo:
	 * $uploader->setPath('banners/');
	 * no lugar de
	 * $uploader->setPath('/banners/');
	 *
	 * Assim ele formará um caminho correto ao executar $uploader->upload() ou $uploader->thumbnail();
	 * @param string $path O caminho do diretório onde será salva a imagem dentro de Site.webroot/img/
	 */
	public function setPath($path){
		if(!preg_match('/[\/]$/', $path))
			$path .= DS;

		$fullPath = WWW_ROOT .'img' . DS . $path;
		if(!is_dir($fullPath))
			throw new \Exception('$path deve ser um diretório válido dentro do diretório de imagens.');

		$this->path = $path;
		$this->fullPath = $fullPath;
	}

	public function createFilename(){
		$ext = $this->getImageFileExtension($this->data['tmp_name']);
		$filename = pathinfo($this->data['name'], PATHINFO_FILENAME);

		return Text::insert(':filename_:width_x_:height_:date.:extension', array('filename' => $filename, 'width' => $this->width, 'height' => $this->height, 'date' => date('hmsdmY'), 'extension' => $ext));
	}

	public function createThumbnailFilename(){
		$ext = $this->getImageFileExtension($this->uploadedImage);
		$filename = pathinfo($this->uploadedImage, PATHINFO_FILENAME);

		return Text::insert('thumb_:filename_:width_x_:height_:date.:extension', array('filename' => $filename, 'width' => $this->width, 'height' => $this->height, 'date' => date('hmsdmY'), 'extension' => $ext));
	}

	public function upload(){
		if(!isset($this->data['error']) or $this->data['error'] !== UPLOAD_ERR_OK)
			return null;

		$filename = $this->createFilename();

		$originalFilename = $this->data['name'];
		$this->uploadedImage = $this->Uploader->upload($this->data);
		if($this->uploadedImage){
			$this->Cropper->resizeImage($this->mode, $originalFilename, $this->Uploader->getDestination(), $filename, $this->width, $this->height);

			$finalImage = new File($this->Uploader->getDestination() . $filename);
			if(!$finalImage->copy($this->fullPath . $filename)){
				$finalImage->delete();
				return false;
			}
			$finalImage->delete();
			return $this->path . $filename;
		}
		return false;
	}

	public function thumbnail(){
		if(!isset($this->uploadedImage))
			return !trigger_error('É necessário fazer o upload da imagem antes de criar miniaturas dela.', E_USER_ERROR);

		$filename = $this->createThumbnailFilename();
		$originalFile = pathinfo($this->uploadedImage, PATHINFO_FILENAME) . '.' . $this->getImageFileExtension($this->uploadedImage);
		$destination = dirname($this->uploadedImage) . DS;

		$this->Cropper->resizeImage($this->mode, $originalFile, $destination, $filename, $this->width, $this->height);
		$thumbFile = new File($destination . DS . $filename);

		$thumbFile->copy($this->fullPath . $filename);
		$thumbFile->delete();
		return $this->path . $filename;
	}

	public function setConfig(ImageUploaderConfig $config) {
		$this->width = $config->getWidth();
		$this->height = $config->getHeight();
		$this->mode = $config->getMode();
	}

	public function close(){
		$file = new File($this->uploadedImage);
		return $file->delete();
	}

	private function getImageFileExtension($path){
		$type = exif_imagetype($path);
		switch($type){
			case IMAGETYPE_GIF    : return 'gif'; 
			case IMAGETYPE_JPEG    : return 'jpg'; 
			case IMAGETYPE_PNG    : return 'png'; 
			case IMAGETYPE_SWF    : return 'swf'; 
			case IMAGETYPE_PSD    : return 'psd'; 
			case IMAGETYPE_BMP    : return 'bmp'; 
			case IMAGETYPE_TIFF_II : return 'tiff'; 
			case IMAGETYPE_TIFF_MM : return 'tiff'; 
			case IMAGETYPE_JPC    : return 'jpc'; 
			case IMAGETYPE_JP2    : return 'jp2'; 
			case IMAGETYPE_JPX    : return 'jpf'; 
			case IMAGETYPE_JB2    : return 'jb2'; 
			case IMAGETYPE_SWC    : return 'swc'; 
			case IMAGETYPE_IFF    : return 'aiff'; 
			case IMAGETYPE_WBMP    : return 'wbmp'; 
			case IMAGETYPE_XBM    : return 'xbm'; 
			default                : return false; 
		}
	}
}


?>
