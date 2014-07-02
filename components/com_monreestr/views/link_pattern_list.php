<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Document Patterns
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );
require_once ( COMPONENTS . DS . 'com_doc_pattern' . DS . 'views' . DS .'doc_pattern_list.php' );
require_once ( COMPONENTS . DS . 'com_doc_pattern' . DS . 'model' . DS .'doc_pattern_query.php' );

class LinkPatternList extends DocPatternList
{
    protected $model = 'DocPatternQuery';
    protected $source = 'mon_linked_patterns';
    protected $namespace = 'linked_patterns';
    
    public function __construct($mon = null)
    {
        parent::__construct($this->model, $this->source, $this->namespace ); 
        if ($mon) {
            $this->where = " AND s.mon_id ='$mon' ";
        } 
        else {
            $this->where = " AND s.mon_id IS NULL ";        
        }
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('статус' , 'dic_doc_pattern_status');
        $constr->get_filters();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'pattern[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'описание','title' => 'Описание', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'версия','title' => 'Версия', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'статус','title' => 'Статус', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'дата_утверждения','title' => 'Утверждено', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'дата_исключения','title' => 'Исключено', 'sort' => true, 'type' => 'plain' );
            
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование;
                $g[$i][] = $item->описание;
                $g[$i][] = $item->версия;
                $g[$i][] = Reference::get_name($item->статус, 'doc_pattern_status', 'dic_');
                $g[$i][] = $item->дата_утверждения;
                $g[$i][] = $item->дата_исключения;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('link_pattern_save');
        $t->set_object_name('pattern');
        $t->set_order_task('link_pattern_prompt');
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>