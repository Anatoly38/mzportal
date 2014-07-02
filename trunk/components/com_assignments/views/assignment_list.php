<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Assignments
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

class AssignmentList extends ItemList
{
    protected $model = 'AssignmentQuery';
    protected $source = 'doc_assignments';
    protected $namespace = 'assignments';
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('руководитель' , 'dic_administrative_sex');
        $constr->get_filters();
    }
    
    protected function add(AssignmentQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'oid[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' ); 
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'описание' ,'title' => 'Описание', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'содержание' ,'title' => 'Содержание', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'дата_вынесения' ,'title' => 'Дата вынесения', 'sort' => true, 'type' => 'plain' );        
        $g[0][] = array('name' => 'руководитель' ,'title' => 'Руководитель', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'выполнение','title' => 'Выполнение', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_выполнения','title' => 'Дата выполнения', 'sort' => true, 'type' => 'plain' ); 
           
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование; 
                $g[$i][] = $item->описание;
                $g[$i][] = $item->содержание;                
                $g[$i][] = $item->дата_вынесения;
                $g[$i][] = $item->руководитель;
                $g[$i][] = $item->выполнение;
                $g[$i][] = $item->дата_выполнения;
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