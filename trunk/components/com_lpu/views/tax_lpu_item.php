<?php
/**
* @version		$Id: tax_lpu_item.php,v 1.0 2010/07/31 09:02:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2010 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class TaxLpuItem extends Item 
{
    protected $model    = 'TaxQuery';
    protected $form     = 'tax_form_tmpl';
}

?>