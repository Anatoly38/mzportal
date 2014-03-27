<?php
/**
* @version		$Id: personnel_record_list.php,v 1.0 2011/02/15 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2011 МИАЦ ИО
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

class PersonnelRecordList extends ItemList
{
    protected $model = 'PersonnelRecordQuery';
    protected $source = 'personnel_record';
    protected $namespace = 'personnel_record_list';
    protected $task      = 'personnel_record_list';
    protected $personnel; 
    
    public function __construct($personnel = false)
    {
        parent::__construct($this->model, $this->source, $this->namespace);
        if (!$personnel) {
            $this->personnel = $this->registry->oid[0];
        }
        else {
            $this->personnel = new AdapterPersonnelQuery($personnel);
        }
        $this->where = "AND s.personnel_id = '{$this->personnel->oid}' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('тип_должности', 'dic_position_type' );
        $constr->get_filters();
    }
    
    protected function add(PersonnelRecordQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'record[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'тип_должности' ,'title' => 'Тип занятия должности', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'должность' ,'title' => 'Должность', 'sort' => true, 'type' => 'plain' );          
        $g[0][] = array('name' => 'дата_начала_труд_отношений','title' => 'Дата начала трудовых отношений', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_окончания_труд_отношений','title' => 'Дата окончания трудовых отношений', 'sort' => true, 'type' => 'plain' ); 
       
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = Reference::get_name($item->тип_должности, 'position_type'); 
                $g[$i][] = Reference::get_name($item->должность, 'post'); 
                $g[$i][] = $item->дата_начала_труд_отношений;
                $g[$i][] = $item->дата_окончания_труд_отношений == '0000-00-00' ? '' : $item->дата_окончания_труд_отношений ;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel_record');
        $t->set_object_name('record');
        $t->set_order_task('personnel_record_list');        
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>