<?php
/**
* @version		$Id: mon_list.php,v 1.0 2011/08/28 12:29:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitotings
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'com_territory'.DS.'model' .DS. 'territory_query.php' );

class MonList extends ItemList
{
    protected $model = 'MonReestrQuery';
    protected $source = 'mon_monitorings';
    protected $namespace = 'mon_reestr';
    protected $task = 'default';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->get_filters();
    }
    
    protected function add(MonReestrQuery $item)
    {
        parent::add($item);
    }
    
    protected function get_array($items) 
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'наименование' , 'title' => 'Наименование', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'описание' ,'title' => 'Описание', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'рег_документ' ,'title' => 'Регламентирующий документ', 'sort' => true, 'type' => 'plain' );  
          
        if (count($items > 0)) {
            $i = 1;
            foreach ($items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование;
                $g[$i][] = $item->описание;
                $g[$i][] = $item->рег_документ;
                $i++;
            }
        }
        return $g;
    }
    
    public function display_table()
    {
        $grid_data = $this->get_array($this->items);
        $footer = $this->display_pagination();
        $t = new HTMLGrid($grid_data, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_order_task($this->task);
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
    
}
?>