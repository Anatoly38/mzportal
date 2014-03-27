<?php
/**
* @version		$Id: group_list.php,v 1.1 2010/06/23 20:31:30 shameev Exp $
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class GroupList extends ItemList
{
    protected $model = 'GroupQuery';
    protected $source = 'sys_groups';
    protected $namespace = 'group_list';
    protected $id = 'gid';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('name');
        $constr->add_filter('block' , 'dic_user_status');
        $constr->get_filters();
    }
    
    protected function add(UserQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'gid' ,'title' => 'Идентификатор', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'name' ,'title' => 'Имя группы', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'description','title' => 'Описание', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'blocked','title' => 'Статус', 'sort' => true, 'type' => 'plain' );
            
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->gid;
                $g[$i][] = $item->gid;
                $g[$i][] = $item->name;
                $g[$i][] = $item->description;
                $g[$i][] = $item->blocked == 0 ? 'активна' : 'заблокирована';
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_group');
        $t->set_order_task('groups');
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>