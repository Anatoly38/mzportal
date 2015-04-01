<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MODULES . DS . 'mod_upload' . DS . 'upload_file_save.php' );

class ExcelQuestionUploadFileSave extends UploadFileSave
{
    protected $upload_name = 'question_list_file';
    protected $upload_folder = UPLOADS;

    public function __construct() { }
}

?>