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

class ExpertGroupSave extends ItemSave
{
    protected $model = 'ExpertGroupQuery';
    
    public function get_post_values()
    {
        $this->query->наименование  = Request::getVar('наименование');
    }
}
?>