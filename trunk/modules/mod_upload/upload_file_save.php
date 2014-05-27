<?php
/** 
* @version      $Id: upload_file_save.php,v 1.0 2012/02/28 12:50:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Upload File Module
* @copyright    Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

class UploadException extends Exception {
    public $message;
    public $code;
    
    public function __construct($m = false, $c = false) {
        $this->message = $m;
        $this->code = $c;
    }
}

class UploadFileSave
{
    protected $upload_name;
    protected $upload_folder;
    protected $name;
    
    public function __construct($uploaded, $folder) 
    {
        $this->upload_name = $uploaded;
        $this->upload_folder = $folder;
    }

    public function save_file()
    {
        if ($_FILES[$this->upload_name]["error"] == 4) {
            throw new UploadException("Файл для загрузки не определен!", 1);
        }
        $this->name = $_FILES[$this->upload_name]["name"];
        $tmp_name   = $_FILES[$this->upload_name]["tmp_name"];
        $size       = $_FILES[$this->upload_name]["size"];
        $type       = $_FILES[$this->upload_name]["type"];
        //if ($type != 'text/xml') {
            //throw new UploadException("Загружаемый файл должен быть XML типа!", 2);
        //}
        if (move_uploaded_file($tmp_name, $this->upload_folder . DS . $this->name)) {
            return $this->name;
        } 
        else {
            return false;
        }
    }

    public function get_uploaded_name()
    {
        return $this->name;
    }
}

?>