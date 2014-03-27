<?php
/**
* @version		$Id: mon_item.php,v 1.0 2011/08/28 13:46:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2010 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class MonItem extends Item 
{
    protected $model    = 'MonReestrQuery';
    protected $form     = 'mon_form_tmpl';
}

?>