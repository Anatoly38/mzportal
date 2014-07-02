<?php
/**
* @version		$Id$
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

class PersonnelEducationList extends ItemList
{
    protected $model = 'PersonnelEducationQuery';
    protected $source = 'personnel_education';
    protected $namespace = 'personnel_education_list';
    protected $task = 'personnel_education_list';
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
        $constr->add_filter('тип_образования', 'dic_education_type' );
        $constr->get_filters();
    }
    
    protected function add(PersonnelEducationQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'education[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'учебное_заведение' ,'title' => 'Учебное заведение', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'год_окончания' ,'title' => 'Год окончания', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'серия_диплома' ,'title' => 'Серия диплома', 'sort' => true, 'type' => 'plain' );          
        $g[0][] = array('name' => 'номер_диплома','title' => 'Номер диплома', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'специальность','title' => 'Специальность', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'тип_образования','title' => 'Тип образования', 'sort' => true, 'type' => 'plain' ); 
       
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = Reference::get_name($item->учебное_заведение, 'education_institution'); 
                $g[$i][] = $item->год_окончания;
                $g[$i][] = $item->серия_диплома;
                $g[$i][] = $item->номер_диплома;
                $g[$i][] = Reference::get_name($item->специальность, 'education_specialities' );
                $g[$i][] = Reference::get_name($item->тип_образования, 'education_type');
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel_education');
        $t->set_object_name('education');
        $t->set_order_task($this->task);        
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>