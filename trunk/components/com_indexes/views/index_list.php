<?php
/**
* @version		$Id: index_list.php,v 1.6 2009/09/21 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Indexes
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

class IndexList extends ItemList
{
    protected $model = 'IndexQuery';
    protected $source = 'mon_indexes';
    protected $namespace = 'index_list';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->add_filter('наименование');
        $constr->add_filter('вид', 'dic_index_types');
        $constr->add_filter('группа', 'dic_index_groups');
        $constr->add_filter('тип', 'dic_data_types');
        $constr->get_filters();
    }
    
    protected function add(IndexQuery $item)
    {
        parent::add($item);
    }

    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'описание' ,'title' => 'Описание', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'вид','title' => 'Вид', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'группа','title' => 'Группа', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'тип','title' => 'Тип', 'sort' => true, 'type' => 'plain' ); 
            
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование;
                $g[$i][] = $item->описание;
                $g[$i][] = Reference::get_name($item->вид, 'index_types');
                $g[$i][] = Reference::get_name($item->группа, 'index_groups');
                $g[$i][] = Reference::get_name($item->тип, 'data_types');
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