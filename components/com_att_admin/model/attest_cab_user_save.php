<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Attest
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class AttestCabUserSave extends ItemSave
{
    protected $model = 'AttestCabUserQuery';
    
    public function get_post_values()
    {
        $this->query->name  = Request::getVar('name');
        $this->query->pwd   = Request::getVar('pwd');
    }

}
?>