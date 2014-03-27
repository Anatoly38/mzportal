<?php
/**
* @version		$Id: assignment_delete.php,v 1.0 2010/12/09 11:45:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Assignments
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

class AssignmentDelete extends DeleteItems 
{
    protected $error_message = "Удаляемые записи не определены ";
    protected $alert_message = "Из списка удалены поручения ";
}

?>