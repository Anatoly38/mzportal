<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Users
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
require_once ( MZPATH_BASE . DS .'modules' . DS .'mod_tasks' . DS . 'task_tree.php' );

class ACLSet 
{
    private $tasks;
    
    public function __construct($id)
    {
        $tmpl = TEMPLATES . DS. MZConfig::$frontpage_tmpl;
        $this->tasks = new TaskTree($tmpl);
        $this->tasks->set_links(false);
        $this->tasks->set_check_boxes(true);
        $this->tasks->set_js(true);
        $this->tasks->set_restriction(false);
        $this->tasks->set_id_input($id);
        $this->tasks->get_applications($id);
    }
    
    public function get_content()
    {
       $content = $this->tasks->get_page();
       return $content;
    }
}
?>