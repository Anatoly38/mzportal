<?php
/**
* @version		$Id: lpu_item.php,v 1.0 2014/06/02 09:02:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class LpuItem extends Item 
{
    protected $model    = 'LpuQuery';
    protected $form     = 'lpu_form_tmpl';

}

?>