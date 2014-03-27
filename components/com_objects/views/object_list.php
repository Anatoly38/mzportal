<?php
/**
* @version		$Id: object_list.php,v 1.0 2010/05/12 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class ObjectList extends ItemList
{
    protected $model = 'ObjectQuery';
    protected $source = 'sys_objects';
    protected $namespace = 'object_manager';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('name');
        $constr->add_filter('deleted' , 'dic_object_state');
        $constr->add_filter('classID' , 'dic_class_names');
        $constr->get_filters();
    }
    
    public function get_items_page() 
    {
        if ($this->order) {
            $ord = " ORDER BY s.{$this->order} {$this->direction} ";
        }
        else {
           $ord = " ORDER BY s.oid";
        }
        $dbh = new DB_mzportal();
        $count_query =  "SELECT 
                            count(*) 
                        FROM 
                             sys_objects AS s 
                        WHERE classID <> -1
                            {$this->where}";
                            //print_r($count_query);
        list($this->total_items) = $dbh->execute($count_query)->fetch_row();
        $query =    "SELECT 
                       s.oid 
                    FROM 
                        sys_objects AS s 
                    WHERE classID <> -1
                        {$this->where} 
                        $ord ";        
        if ($this->limit <> 0) {
            $query .= "LIMIT {$this->limitstart}, {$this->limit}";
        }
        //print_r($query);
        $stmt = $dbh->execute($query)->fetch();
        foreach ($stmt as $id) {
            self::add(new $this->model($id));
        }
        $page = $this->display_table();
        return $page;
    }

    protected function add(ObjectQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'oid' , 'title' => 'Идентификатор', 'sort' => true, 'type' => 'link' ); 
        $g[0][] = array('name' => 'classID' , 'title' => 'Класс', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'name' ,'title' => 'Имя', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'description' ,'title' => 'Описание', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'created' ,'title' => 'Создан', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'changed' ,'title' => 'Изменен', 'sort' => true, 'type' => 'plain' );

           
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->oid;
                $g[$i][] = Reference::get_name($item->classID, 'class_names');
                $g[$i][] = $item->name;
                $g[$i][] = $item->description;
                $g[$i][] = $item->created;
                $g[$i][] = $item->changed;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>