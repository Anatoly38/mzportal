<?php
/**
* @version		$Id: user_item.php,v 1.1 2014/05/26 20:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	User
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class TaskItem extends Item 
{
    protected $model = 'TaskQuery';
    protected $form = 'task_form_tmpl';
    public $query;
    
}

?>