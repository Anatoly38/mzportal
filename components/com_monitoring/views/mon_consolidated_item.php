<?php
/**
* @version		$Id: mon_document_item.php,v 1.0 2012/01/09 21:56:30 shameev Exp $
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