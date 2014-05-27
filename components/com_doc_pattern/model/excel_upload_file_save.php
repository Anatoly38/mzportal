<?php
/** 
* @version		$Id: excel_upload_file_save.php,v 1.0 2014/05/23 15:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Upload File Module
* @copyright	Copyright (C) 2009-2012 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MODULES . DS . 'mod_upload' . DS . 'upload_file_save.php' );

class ExcelUploadFileSave extends UploadFileSave
{
    protected $upload_name = 'excel_template_file';
    protected $upload_folder = UPLOADS; 

    public function __construct() { }
}

?>