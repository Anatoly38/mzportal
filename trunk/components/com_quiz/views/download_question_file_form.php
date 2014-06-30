<?php
/** 
* @version		$Id: download_question_file_form.php,v 1.0 2014/06/02 17:24:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Quiz
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'modules'.DS.'mod_form'.DS.'form_template_loader.php' );

class DownloadQuestionFileForm 
{
    protected $form  = 'question_upload_form.xml';
    protected $form_loader;
    
    public function __construct() 
    {
        $full_path = TMPL.DS.$this->form;
        $this->form_loader = new Form_Template_Loader($full_path);
    }

    public function get_form()
    {
        $form_content = $this->form_loader->render();
        return $form_content;
    }
}

?>