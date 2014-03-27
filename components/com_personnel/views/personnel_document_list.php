<?php
/**
* @version		$Id: personnel_document_list.php,v 1.0 2011/01/17 13:40:30 shameev Exp $
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

class PersonnelDocumentList extends ItemList
{
    protected $model = 'PersonnelDocumentQuery';
    protected $source = 'personnel_document';
    protected $namespace = 'personnel_document_list';
    protected $task = 'personnel_document_list';
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
        $constr->add_filter('тип_документа', 'dic_document_type' );
        $constr->get_filters();
    }
    
    protected function add(PersonnelDocumentQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'document[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'тип_документа' ,'title' => 'Тип документа', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'серия_документа' ,'title' => 'Серия документа', 'sort' => true, 'type' => 'plain' );          
        $g[0][] = array('name' => 'номер_документа','title' => 'Номер документа', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'кем_выдан','title' => 'Кем выдан', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дата_выдачи','title' => 'Дата выдачи', 'sort' => true, 'type' => 'plain' ); 
       
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = Reference::get_name($item->тип_документа, 'document_type'); 
                $g[$i][] = $item->серия_документа;
                $g[$i][] = $item->номер_документа;
                $g[$i][] = $item->кем_выдан;
                $g[$i][] = $item->дата_выдачи;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel_document');
        $t->set_object_name('document');
        $t->set_order_task('personnel_document_list');
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>