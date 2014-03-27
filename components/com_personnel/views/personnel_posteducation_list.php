<?php
/**
* @version		$Id: personnel_posteducation_list.php,v 1.0 2011/04/05 13:40:30 shameev Exp $
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

class PersonnelPostEducationList extends ItemList
{
    protected $model = 'PersonnelPostEducationQuery';
    protected $source = 'personnel_posteducation';
    protected $namespace = 'personnel_posteducation_list';
    protected $task = 'personnel_posteducation_list';
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
        $g[0][] = array('name' => 'posteducation[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'базовая_организация' ,'title' => 'Базовая организация', 'sort' => true, 'type' => 'link' );  
        $g[0][] = array('name' => 'тип_образования' ,'title' => 'Тип образования', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'начало_прохождения' ,'title' => 'Начало прохождения', 'sort' => true, 'type' => 'plain' );          
        $g[0][] = array('name' => 'окончание_прохождения','title' => 'Окончание прохождения', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_получ_документа','title' => 'Дата получения документа', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'ученая_степень','title' => 'Ученая степень', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'специальность','title' => 'Специальность', 'sort' => true, 'type' => 'plain' ); 
       
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = Reference::get_name($item->базовая_организация, 'education_institution'); 
                $g[$i][] = Reference::get_name($item->тип_образования, 'posteducation_type'); 
                $g[$i][] = $item->начало_прохождения;
                $g[$i][] = $item->окончание_прохождения;
                $g[$i][] = $item->дата_получ_документа;
                $g[$i][] = Reference::get_name($item->ученая_степень, 'academic_degree' );
                $g[$i][] = Reference::get_name($item->специальность, 'sertificate_specialities' );
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel_posteducation');
        $t->set_object_name('posteducation');
        $t->set_order_task('personnel_posteducation_list');
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>