<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class DossierSave extends ItemSave
{
    protected $model = 'DossierQuery';
    
    public function get_post_values()
    {
        $this->query->номер_дела = Request::getVar('номер_дела');
        $this->query->фио        = Request::getVar('фио');
        $this->query->мо         = Request::getVar('мо');
        $this->query->экспертная_группа = Request::getVar('экспертная_группа');
    }
   
}
?>