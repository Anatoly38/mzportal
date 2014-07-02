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


class PersonnelAdressList extends ItemList
{
    protected $model = 'AdressQuery';
    protected $source = 'personnel_adress';
    protected $namespace = 'personnel_adress_list';
    protected $task      = 'personnel_adress_list';
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
    
    protected function add(AdressQuery $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'adress[]' , 'title' => '', 'sort' => false, 'type' => 'checkbox' );
        $g[0][] = array('name' => 'индекс' ,'title' => 'Индекс', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'код_кладр' ,'title' => 'Код КЛАДР', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'район' ,'title' => 'Район', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'населенный_пункт' ,'title' => 'Населенный пункт', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'улица' ,'title' => 'Улица', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'дом','title' => 'Дом', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'квартира','title' => 'Квартира', 'sort' => true, 'type' => 'plain' ); 
       
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->индекс; 
                $g[$i][] = $item->код_кладр; 
                $g[$i][] = $item->район; 
                $g[$i][] = $item->населенный_пункт; 
                $g[$i][] = $item->улица; 
                $g[$i][] = $item->дом;
                $g[$i][] = $item->квартира ;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);
        $t->set_task('edit_personnel_adress');
        $t->set_object_name('adress');
        $t->set_order_task('personnel_adress_list');        
        $table = $t->render_table();
        $constr = Constraint::getInstance();
        $admin_form = $constr->get_constraints() . $table;
        return $admin_form;
    }
}
?>