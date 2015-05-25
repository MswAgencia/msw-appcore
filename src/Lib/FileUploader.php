<?php
namespace AppCore\Lib;

/**
 * Uma classe que lida com upload de arquivos.
 * Originalmente por Dave Baker @fullybaked.co.uk, era um component para CakePHP 1.x.
 * Modificado para ser uma classe que pode ser instanciada em qualquer lugar.
 * 
 * @author Bruno Schmidt <bruno@masterstudioweb.com.br>
 * @version 1.0.0
 */
class FileUploader {
    /**
     * constant for error types
     * @var integer
     */
    const SUCCESS = 0;
	
	const FILESIZE_EXCEED_SERVER_MAX = 1;
 
    const FILESIZE_EXCEED_FORM_MAX = 2;
 
    const PARTIAL_UPLOAD = 3;
 
    const NO_FILE_UPLOAD = 4;
 
    const NO_DIRECTORY_FOR_UPLOAD = 6;
 
    const SERVER_WRITE_FAIL = 7;
 
	const FILESIZE_EXCEEDS_CODE_MAX = 98;
 
	const FILE_FORMAT_NOT_ALLOWED = 99;
	
	const DESTINATION_NOT_AVAILABLE = 100;
 
	/**
	 * array of mime types that will be accepted by the UploadComponent
	 * left empty it will accept any
	 */
    private $file_types = array();
 
    private $filename = null;
 
	private $destination = null;
 
	private $max_size = null;
    
    private $content_only = false;
 
	private $create_destination = true;
    
	public function __construct() {
		$this->destination(WWW_ROOT . 'files' . DS . 'uploads');
	}
		
	/**
	 * set the allowed file types for this upload
	 * values are passed as string arguments to the
	 * method
	 * @return UploaderComponent
	 */
	public function allowed_types() {
		$types = func_get_args();
		$this->file_types = $this->file_types + $types;
		return $this;
	}
 
	/**
	 * set a custom name to use for the final uploaded file
	 * if not set then the original name of the uploaded file is used
	 * @param string $name
	 * @return UploaderComponent
	 */
	public function custom_name($name) {
		$this->filename = $name;
		return $this;
	}
 
	/**
	 * set the destination path for the uploaded file
	 * @param string $path
	 * @return UploaderComponent
	 */
	public function destination($path) {
		// add trailing slash if there isn't one
		$last_char = substr($path, -1);
		if ($last_char !== '/') $path .= '/';
 
		$this->destination = $path;
		
		return $this;
	}
 	
 	public function getDestination(){
 		return $this->destination;
 	}
	/**
	 * set the max file size for uploads for added validation
	 * if $this->max_size = 0 (default) then upload size is governed
	 * by PHP.ini settings and/or form settings.
	 * @param integer $size - max size of upload in bytes
	 * @return UploaderComponent
	 */
	public function set_max_size($size) {
		$this->max_size = $size;
		return $this;
	}
    
	/**
	 * setter for the content_only flag
	 * if true then the upload is read into buffer
	 * and returned rather than being written to server
	 * @param boolean $flag
	 * @return UploaderComponent
	 */
    public function content_only($flag = true) {
        $this->content_only = $flag;
		return $this;
    }
    
	/**
	 * setter for the create destination flag. 
	 * can be turned off if an error on missing destination is required
	 * @param boolean $flag
	 * @return UploaderComponent
	 */
	public function create_destination($flag = true) {
		$this->create_destination = $flag;
		return $this;
	}
 
	/**
	 * run the upload.
	 * the path param is optional and can be set separately
	 * @param array $form_data - The posted form data for the file element
	 * @param string $path - Optionally set the destination path here
	 * @return mixed - 
	 */
	 public function upload($form_data, $path = null) {
	 	// silent fail on no image
		if ($form_data['error'] == self::NO_FILE_UPLOAD) {
			throw new \Exception ($this->errors(self::NO_FILE_UPLOAD), self::NO_FILE_UPLOAD);
		}
		
		// handle optional path passed in
	 	if (!empty($path)) $this->destination($path);
 
		$this->form_data = $form_data;
 
	 	// check we have a path - only if not returning the content
	 	if ($this->content_only === false) {
			if (empty($path) && empty($this->destination)) {
		 		$this->form_data['error'] = self::NO_DIRECTORY_FOR_UPLOAD;
		 	}
		}
 
		// check file types
		if (!empty($this->file_types)) {			
			if (!in_array($this->form_data['type'], $this->file_types)) {
				$this->form_data['error'] = self::FILE_FORMAT_NOT_ALLOWED;
			}
		}
 
		// check max size set in code
		if ($this->max_size > 0 && $this->form_data['size'] > $this->max_size) {
			$this->form_data['error'] = self::FILE_SIZE_EXCEEDS_CODE_MAX;
		}
 
		// check error code		
	 	if ($this->form_data['error'] !== self::SUCCESS) {
	 		throw new \Exception($this->errors($this->form_data['error']), $this->form_data['error']);
	 	}
        
        // if only content required read file and return
        if ($this->content_only) {
            return file_get_contents($this->form_data['tmp_name']);
        }
						
		// parse out class params to make the final destination string
		if (empty($this->filename)) {
			$destination = $this->destination . $this->form_data['name'];
		} else {
			$destination = $this->destination . $this->filename;
		}
		
		// create the destination unless otherwise set
		if ($this->create_destination) {
			$dir = dirname($destination);
	        if (!is_dir($dir)) {
	            mkdir($dir, 0777, true);
	        }
		} else {
			$dir = dirname($destination);
	        if (!is_dir($dir)) {
				throw new \Exception($this->errors(self::DESTINATION_NOT_AVAILABLE), self::DESTINATION_NOT_AVAILABLE);
			}
		}
                
		if (move_uploaded_file($this->form_data['tmp_name'], $destination)) {
			return $destination;
		} else {
			throw new \Exception($this->errors(self::SERVER_WRITE_FAIL), self::SERVER_WRITE_FAIL);
		}
		
		// if we get here without returning something has definitely gone wrong
		throw new \Exception($this->errors());
	 }
	 
     /**
      * parse the response type and return an error string
      * @param integer $type
      * @return string - error text
      */
     private function errors($type = null) {
         switch ($type) {
             case self::FILESIZE_EXCEED_SERVER_MAX:
                 return 'File size exceeds allowed size for server';
                 break;
             case self::FILESIZE_EXCEED_FORM_MAX:
                 return 'File size exceeds allowed size in form';
                 break;
             case self::PARTIAL_UPLOAD:
                 return 'File was partially uploaded. Please check your Internet connection and try again';
                 break;
             case self::NO_FILE_UPLOAD:
                 return 'No file was uploaded.';
                 break;
             case self::NO_DIRECTORY_FOR_UPLOAD:
                 return 'No upload directory found';
                 break;
             case self::SERVER_WRITE_FAIL:
                 return 'Failed to write to the server';
                 break;
             case self::FILE_FORMAT_NOT_ALLOWED:
             	return 'File format of uploaded file is not allowed';
             	break;
             case self::FILESIZE_EXCEEDS_CODE_MAX:
             	return 'File size exceeds maximum allowed size';
             	break;
 			case self::DESTINATION_NOT_AVAILABLE:
 				return 'Destination path does not exist';
 				break;
             default:
                 return 'There has been an unexpected error, processing upload failed';
         }
     }
 }

 ?>
