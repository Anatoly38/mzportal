<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Tasks
* @copyright	Copyright (C) 2009 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class TaskSave extends ItemSave
{
    protected $model = 'TaskQuery';
    
    public function get_post_values()
    {
        $this->query->наименование = Request::getVar('наименование');
        $this->query->описание = Request::getVar('описание');
        $this->query->component_id = Request::getVar('component_id');
    }
}
?>