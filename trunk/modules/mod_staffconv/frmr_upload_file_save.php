<?php
/** 
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	FRMR Import Module
* @copyright	Copyright (C) 2010 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 
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
        if ($type != 'text/xml') {
            throw new UploadException("Загружаемый файл должен быть XML типа!", 2);
        }
        if (move_uploaded_file($tmp_name, $this->upload_folder . DS . $this->name)) {
            return true;
        }
    }

    public function get_uploaded_name()
    {
        return $this->name;
    }
}

class FrmrUploadFileSave extends UploadFileSave
{
    protected $upload_name = 'frmr_file';
    protected $upload_folder = FRMR_UPLOADS; 

    public function __construct() { }
}

class FrmrDicUploadFileSave extends UploadFileSave
{
    protected $upload_name = 'dic_file';
    protected $upload_folder = FRMR_DICTIONARY_UPLOADS; 

    public function __construct() { }
}

?>