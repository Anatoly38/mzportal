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

class DocPatternList extends ItemList
{
    protected $model = 'DocPatternQuery';
    protected $source = 'mon_doc_patterns';
    protected $namespace = 'doc_patterns';
    protected $obj   = 'pattern';
    protected $default_cols = array('oid', 'наименование', 'описание', 'вид', 'периодичность' ,'статус' , 'дата_утверждения');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
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
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                     = array('sort' => false, 'type' => 'checkbox' ); 
        $options['наименование']            = array('sort' => true, 'type' => 'plain' );
        $options['описание']                = array('sort' => true, 'type' => 'plain' );
        $options['вид']                     = array('sort' => true, 'type' => 'plain', 'ref' => 'doc_pattern_kind' ); 
        $options['периодичность']           = array('sort' => true, 'type' => 'plain', 'ref' => 'periodicity' );
        $options['версия']                  = array('sort' => true, 'type' => 'plain' );
        $options['статус']                  = array('sort' => true, 'type' => 'plain', 'ref' => 'doc_pattern_status' );
        $options['дата_утверждения']        = array('sort' => true, 'type' => 'plain' ); 
        $options['дата_исключения']         = array('sort' => true, 'type' => 'plain' ); 
        return $options;
    }
    
/*     protected function get_array($items) 
    {
        $g = array();
        $g[0][] = array('name' => 'pattern[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'описание','title' => 'Описание', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'вид','title' => 'Вид', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'периодичность','title' => 'Периодичность', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'версия','title' => 'Версия', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'статус','title' => 'Статус', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'дата_утверждения','title' => 'Утверждено', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'дата_исключения','title' => 'Исключено', 'sort' => true, 'type' => 'plain' );
            
        if (count($items > 0)) {
            $i = 1;
            foreach ($items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование;
                $g[$i][] = $item->описание;
                $g[$i][] = Reference::get_name($item->вид, 'doc_pattern_kind', 'dic_');
                $g[$i][] = Reference::get_name($item->периодичность, 'periodicity', 'dic_');
                $g[$i][] = $item->версия;
                $g[$i][] = Reference::get_name($item->статус, 'doc_pattern_status', 'dic_');
                $g[$i][] = $item->дата_утверждения;
                $g[$i][] = $item->дата_исключения;
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
        $t->set_object_name($this->obj);
        $t->set_order_task($this->task);
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    } */
}
?>