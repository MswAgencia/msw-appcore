<?php
namespace AppCore\Lib\Image;

class Image {
  private $resource;
  private $type;

  public function __construct($filepath)
  {
    $imageinfo = getimagesize($filepath);
    $this->type = $imageinfo[2];

    switch($this->type) {
      case IMAGETYPE_JPEG:
        $this->resource = imagecreatefromjpeg($filepath);
        break;
      case IMAGETYPE_PNG:
        $this->resource = imagecreatefrompng($filepath);
        break;
      default:
        throw new \Exception('Tipo de Image não suportado.');
    }
  }

  // -- resize to max, then crop to center
  public function resizeAndCrop($width, $height)
  {
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

    //create new image
    $this->resized_image = imagecreatetruecolor($applyWidth, $applyHeight);
    if($this->type === IMAGETYPE_PNG) {
      imagealphablending($this->resized_image, false);
      imagesavealpha($this->resized_image, true);
    }
    imagecopyresampled($this->resized_image, $this->resource, 0,0 , $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
  }

  public function resizeTo($width, $height, $keepRatio = true)
  {
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

    //create new image
    $this->resized_image = imagecreatetruecolor($applyWidth, $applyHeight);
    if($this->type === IMAGETYPE_PNG) {
      imagealphablending($this->resized_image, false);
      imagesavealpha($this->resized_image, true);
    }
    imagecopyresampled($this->resized_image, $this->resource, 0,0 , $startX, $startY, $applyWidth, $applyHeight, $oldWidth, $oldHeight);
  }

  /**
   * $path é o nome do arquivo novo com caminho ou só o diretório?
   */
  public function save($path)
  {
    if(!is_dir($path))
      throw new \Exception('$path não é um diretório válido.');

    switch($this->type) {
      case IMAGETYPE_PNG:
        imagepng($this->resized_image, $path, 8);
        break;
      case IMAGETYPE_JPEG:
        imagejpeg($this->resized_image, $path, 90);
      default:
        throw new \Exception('Tipo de Imagem inválido.');
    }
  }

  public function close()
  {
    imagedestroy($this->resource);
    imagedestroy($this->resized_image);
  }
}
