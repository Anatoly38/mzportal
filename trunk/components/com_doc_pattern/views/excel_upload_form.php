<?php
/** 
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'modules'.DS.'mod_form'.DS.'form_template_loader.php' );

class ExcelUploadForm 
{
    protected $form  = 'excel_upload_form.xml';
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