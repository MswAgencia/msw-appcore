<?php
namespace AppCore\Lib\Image;

class Image {
  private $_resource;
  private $_type;
  private $_filepath;
  private $_filename;
  private $_width;
  private $_height;

  public function __construct($filepath)
  {
    $imageinfo = getimagesize($filepath);
    $this->_width = $imageinfo[0];
    $this->_height = $imageinfo[1];
    $this->_type = $imageinfo[2];
    $this->_filepath = $filepath;
    $this->_filename = pathinfo($filepath, PATHINFO_BASENAME);
  }

  public function getFilename()
  {
    return $this->_filename;
  }

  public function resizeTo($width, $height, $mode = 'resize')
  {
    switch($mode) {
      case 'resize':
        return $this->resize($width, $height);
      case 'resizeCrop':
      case 'resize_crop':
        return $this->resizeAndCrop($width, $height);
      default:
        return false;
    }
  }

  public function resizeAndCrop($newWidth, $newHeight)
  {
    $this->open();
    $oldWidth = $this->_width;
    $oldHeight = $this->_height;

    $ratioX = $newWidth / $oldWidth;
    $ratioY = $newHeight / $oldHeight;

    if ($ratioX < $ratioY) {
      $startX = round(($oldWidth - ($newWidth / $ratioY))/2);
      $startY = 0;
      $oldWidth = round($newWidth / $ratioY);
      $oldHeight = $oldHeight;
    } else {
      $startX = 0;
      $startY = round(($oldHeight - ($newHeight / $ratioX))/2);
      $oldWidth = $oldWidth;
      $oldHeight = round($newHeight / $ratioX);
    }
    $applyWidth = $newWidth;
    $applyHeight = $newHeight;

    $this->resized_image = imagecreatetruecolor($applyWidth, $applyHeight);
    if($this->_type === IMAGETYPE_PNG) {
      imagealphablending($this->resized_image, false);
      imagesavealpha($this->resized_image, true);
    }
    imagecopyresampled($this->resized_image, $this->_resource, 0,0 , $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
  }

  public function resize($newWidth, $newHeight)
  {
    $this->open();
    $widthScale = 2;
    $heightScale = 2;

    if($newWidth)
      $widthScale = $newWidth / $oldWidth;
    if($newHeight)
      $heightScale = $newHeight / $oldHeight;

    if($widthScale < $heightScale) {
      $maxWidth = $newWidth;
      $maxHeight = false;
    } elseif ($widthScale > $heightScale ) {
      $maxHeight = $newHeight;
      $maxWidth = false;
    } else {
      $maxHeight = $newHeight;
      $maxWidth = $newWidth;
    }

    if($maxWidth > $maxHeight){
      $applyWidth = $maxWidth;
      $applyHeight = ($oldHeight * $applyWidth) / $oldWidth;
    } elseif ($maxHeight > $maxWidth) {
      $applyHeight = $maxHeight;
      $applyWidth = ($applyHeight * $oldWidth) / $oldHeight;
    } else {
      $applyWidth = $maxWidth;
      $applyHeight = $maxHeight;
    }
    $startX = 0;
    $startY = 0;

    $this->resized_image = imagecreatetruecolor($applyWidth, $applyHeight);
    if($this->_type === IMAGETYPE_PNG) {
      imagealphablending($this->resized_image, false);
      imagesavealpha($this->resized_image, true);
    }
    imagecopyresampled($this->resized_image, $this->_resource, 0,0 , $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
  }

  public function save($where, $name = false)
  {
    if(!is_dir($where) or !is_writable($where))
      throw new \Exception('$path não é um diretório válido.');

    if($name)
      $this->_filename = $name;
    else
      $name = $this->_source_filename;

    return $this->_writeImageToDisk($where, $name);
  }

  public function open()
  {
    switch($this->_type) {
      case IMAGETYPE_JPEG:
        $this->_resource = imagecreatefromjpeg($this->_filepath);
        break;
      case IMAGETYPE_PNG:
        $this->_resource = imagecreatefrompng($this->_filepath);
        break;
      default:
        throw new \Exception('Tipo de Image não suportado.');
    }
  }

  public function close()
  {
    imagedestroy($this->_resource);
    imagedestroy($this->resized_image);
  }

  private function _writeImageToDisk($where, $name)
  {
    $fullpath = $where . $name;
    switch($this->_type) {
      case IMAGETYPE_PNG:
        imagepng($this->resized_image, $fullpath, 8);
        break;
      case IMAGETYPE_JPEG:
        imagejpeg($this->resized_image, $fullpath, 90);
        break;
      default:
        throw new \Exception('Tipo de Imagem inválido.');
    }

    if(!file_exists($fullpath))
      throw new \Exception('Não foi possível salvar a imagem');

    return new Image($fullpath);
  }
}
