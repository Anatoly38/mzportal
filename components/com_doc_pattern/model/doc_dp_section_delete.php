<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Register OKS
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
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );

class DocDpSectionDelete extends DeleteItems 
{
    protected $error_message = "Удаляемые разделы не определены ";
    protected $alert_message = "Из регистра удалены разделы ";
}

?>