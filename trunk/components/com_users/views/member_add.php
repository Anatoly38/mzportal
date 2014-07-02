<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Users
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class MemberAdd extends ItemList
{
    protected $model = 'UserQuery';
    protected $source = 'sys_users';
    protected $namespace = 'member_add';
    protected $id = 'uid';
    private $group;
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );
        $this->group = new GroupQuery($this->registry->oid[0]);
        $this->where = "AND s.uid NOT IN (SELECT ug.uid FROM sys_users_groups AS ug WHERE ug.gid = {$this->group->gid}) "; 
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        Constraint::set_namespace($this->namespace);
        $constr->set('name');
        $this->constraints = $constr->get_constraints();
        $this->where = $constr->get_where();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'uid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'uid' ,'title' => 'Идентификатор', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'name' ,'title' => 'Имя пользователя', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'description','title' => 'Описание', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'blocked','title' => 'Статус', 'sort' => true, 'type' => 'plain' );
            
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->uid;
                $g[$i][] = $item->uid;
                $g[$i][] = $item->name;
                $g[$i][] = $item->description;
                $g[$i][] = $item->blocked == 0 ? 'активен' : 'заблокирован';
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $current_gid = '<input type="hidden" name="oid[]" value="'. $this->registry->oid[0] .'" />';
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_order_task('members');
        $table = $t->render_table();
        $admin_form = $current_gid . $this->constraints . $table;
        return $admin_form;
    }
    
    public function get_group_name()
    {
        $name = $this->group->name;
        return $name;
    }
}
?>