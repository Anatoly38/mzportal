<?php
/**
* @version		$Id: view_frontpage.php,v 1.1 2010/04/18 13:29:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Frontpage
* @copyright	Copyright (C) 2010 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE . DS .'components' . DS .'com_tasks' . DS . 'model' . DS . 'task_query.php' );
require_once ( MZPATH_BASE . DS .'modules' . DS .'mod_tasks' . DS . 'task_tree.php' );

class ViewFrontpage extends TaskTree
{

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $user_id = $this->registry->user->user_id;
        $tmpl = TEMPLATES . DS. MZConfig::$frontpage_tmpl;
        $this->load_template($tmpl);
//        $this->get_applications($user_id);
        $this->add_scripts();
        $this->set_tree(null, $user_id);
        $this->get_page();
    }
    
}
?>