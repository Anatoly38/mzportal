<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class MonConsolidatedItem extends Item 
{
    protected $model    = 'MonDocumentQuery';
    protected $form     = 'mon_consolidated_form_tmpl';
    
}

?>