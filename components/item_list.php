<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Components
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'pagination.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'constraint.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'reference.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'html'.DS.'grid.php' );

class ItemList
{
    protected $model        = null;
    protected $source       = null;
    protected $dbh;
    public    $items        = array();
    protected $limit        = null;
    protected $limitstart   = null;
    protected $total_items  = null;
    protected $where        = null;
    protected $conditions   = false;
    protected $constraints  = null;
    protected $order        = null;
    protected $direction    = null;
    protected $show_constraint = true;
    protected $show_pagination = true;
    protected $id           = 'oid'; // Для sql запросов
    protected $obj          = 'oid'; // Для передачи на html страницу
    protected $task         = 'default';
    protected $order_task   = 'default';
    protected $registry;
    protected $default_cols = null; // отображаемые столбцы, по умолчанию все 

    public function __construct($model, $source, $namespace = 'default')
    {
        $this->registry = Registry::getInstance();
        $this->model = $model;
        $this->source = $source;
        $this->dbh = new DB_mzportal();

        if ($namespace != false) {
            $this->set_session($namespace);
        }
        $this->set_constrains();
    }

    protected function set_session($namespace)
    {
        if (MZSession::hasNS($namespace)) {
            $this->limitstart = Request::set_value('limitstart', 0, $namespace);
            $this->limit = Request::set_value('limit', MZConfig::$list_limit, $namespace);
            $this->order = Request::set_value('order', null, $namespace);
            $this->direction = Request::set_value('direction', 'asc', $namespace);
        }
        else {
            $this->limitstart = 0;
            $this->limit = MZConfig::$list_limit;
            $this->order = null;
            MZSession::set('limitstart', 0, $namespace);
            MZSession::set('limit', MZConfig::$list_limit, $namespace);
            MZSession::set('order', null, $namespace);
        }
    }

    public function set_limit($new_limit = 0)
    {
        $this->limit = $new_limit;
    }
    
    public function set_limitstart($new_limitstart = 0)
    {
        $this->limitstart = $new_limitstart;
    }
    
    public function set_show_constraint($show = true)
    {
        $this->show_constraint = $show;
    }
    
    public function set_show_pagination($show = true)
    {
        $this->show_pagination = $show;
    }

    public function set_columns($c = null)
    {
        $this->default_cols = $c;
    }

    protected function set_constrains()
    {
    }

    public function set_condition($show_deleted = false, $with_acl = true) 
    {
        $obj_not_deleted = " ";
        $acl = " ";
        if (!$show_deleted) {
            $obj_not_deleted = " AND o.deleted ='0'";
        }
        if ($with_acl) {
            $user_id = $this->registry->user->user_id;
            if ($user_id != MZConfig::$root_uid) {
                $acl = " AND (a.uid = '$user_id' OR a.uid IN (SELECT ug.uid FROM sys_users_groups AS ug WHERE ug.gid = a.acl_id))";
            }
        }
        $constr = Constraint::getInstance();
        $add_cond = $constr->get_where();
        $where = $obj_not_deleted;
        $where .= $acl;
        $where .= $add_cond;
        $where .= $this->where;
        $this->conditions = $where;
        return $where;
    }

    public function items_count($where = null)
    {
        $count_query =  "SELECT COUNT(*) FROM (SELECT `s`.`{$this->id}` FROM 
                        {$this->source} AS s
                        JOIN `sys_objects` AS `o` ON `s`.`{$this->id}` = `o`.`oid`  
                        JOIN `sys_acl` AS `a` ON `o`.`acl_id` = `a`.`acl_id`
                        WHERE 1=1
                            {$where}
                        GROUP BY `s`.`{$this->id}`) AS source";
        //print_r($count_query);
        list($count) = $this->dbh->execute($count_query)->fetch_row();
        return $count;
    }

    public function get_items()
    {
        if (!$this->conditions) {
            $where = $this->set_condition();
        } 
        else {
            $where = $this->conditions;
        }
        if ($this->order) {
            $ord = " ORDER BY s.{$this->order} {$this->direction} ";
        }
        else {
            $ord = null;
        }
        $this->total_items = $this->items_count($where);
        if (!$this->total_items) {
            return;
        }
        if ($this->limitstart > $this->total_items) {
            $this->limitstart = 0;
        }
        $query =    "SELECT DISTINCT
                       s.{$this->id} 
                    FROM 
                        {$this->source} AS s
                        JOIN `sys_objects` AS `o` ON `s`.`{$this->id}` = `o`.`oid`  
                        JOIN `sys_acl` AS `a` ON `o`.`acl_id` = `a`.`acl_id`
                    WHERE 1=1
                        {$where} 
                        {$ord} ";
        if ($this->limit <> 0) {
            $query .= " LIMIT {$this->limitstart}, {$this->limit} ";
        }
        //print_r($query);
        $stmt = $this->dbh->execute($query)->fetch();
        foreach ($stmt as $id) {
            $this->add(new $this->model($id));
        }
        return $stmt;
    }

    public function get_items_page() 
    {
        $this->get_items();
        $page = $this->display_table();
        return $page;
    }

    protected function add($item)
    {
        $this->items[] = $item;
    }

    protected function list_options()
    {
        return array();
    }

    protected function get_param()
    {
        $param = $this->items[0]->get_fields_titles();
        return $param;
    }

    protected function get_array($items) 
    {
        $g = array();
        $p = $this->get_param();
        $o = $this->list_options();
        foreach ($p as $param => $title) {
            if ( !$this->default_cols || in_array($param , $this->default_cols) ) {
                $set = array ('name' => $param, 'title' => !empty($title) ? $title : $param);
                if (array_key_exists($param, $o)) {
                    $set['sort'] = $o[$param]['sort']; $set['type'] = $o[$param]['type'];
                } 
                else {
                    $set['sort'] = true; $set['type'] = 'plain';
                }
                $g[0][] = $set;
            }
        }
        if (count($items > 0)) {
            $j = 1;
            foreach ($items as $item) {
                foreach ($p as $param => $title) {
                    if ( !$this->default_cols || in_array($param , $this->default_cols) ) {
                        if (isset($o[$param]['ref'])) {
                            if ($o[$param]['ref'] !== 'bool') {
                                $g[$j][] = Reference::get_name($item->$param, $o[$param]['ref']);
                            }
                            else {
                                $item->$param == 1 ? $g[$j][] = 'Да' : $g[$j][] = 'Нет';
                            }
                        }
                        else {
                            $g[$j][] = $item->$param;
                        }
                    }
                }
                $j++;
            }
        }
        return $g;
    }

    protected function display_table()
    {
        if (count($this->items) == 0) {
            Message::alert('Нет данных');
            $table = '';
        } 
        else {
            $footer = null;
            $grid_data = $this->get_array($this->items);
            if ($this->show_pagination) {
                $footer = $this->display_pagination();
            }
            $t = new HTMLGrid($grid_data, $footer, $this->limitstart, $this->order, $this->direction);
            $t->set_task($this->task);
            $t->set_object_name($this->obj);
            $t->set_order_task($this->order_task);
            $table = $t->render_table();
        }
        $constraints = null;
        if ($this->show_constraint) {
            $constr = Constraint::getInstance();
            $constraints = $constr->get_constraints();
        }
        $admin_form = $constraints . $table;
        return $admin_form;
    }

    protected function display_pagination()
    {
        $page_nav_object = new Pagination($this->total_items, $this->limitstart, $this->limit, $this->task);
        $footer = $page_nav_object->getListFooter();
        return $footer;
    }

    public function export_to_excel()
    {
        if (count($this->items) == 0)
        {
            throw new Exception("Данные для экспорта в формат Excel не определены");
        }
        $data = $this->get_array($this->items);
        $exp = new ExcelExport();
        $exp->load_data($data);
        return $exp;
    }
}

?>