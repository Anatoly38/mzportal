<?php
/** 
* @version		$Id: frmr_import.php,v 1.0 2011/06/20 23:21:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	FRMR Import Module
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 
Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'modules'.DS.'mod_form'.DS.'form_template_loader.php' );

class FrmrUploadForm 
{
    protected $form  = 'personnel_upload_form.xml';
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