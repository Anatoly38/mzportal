<?php
/**
* @version		$Id: territory_subordinate.php,v 1.6 2009/09/21 00:50:30 shameev Exp $
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

class TerritorySubordinate extends ItemList
{
    private $territory;
    protected $model = 'TerritoryQuery';
    protected $source = 'pasp_territory';
    protected $namespace = 'territory_subordinate';    
    protected $task = 'subordinate';

    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );
        $this->territory = new TerritoryQuery($this->registry->oid[0]);
        // Территориальное образование может быть подчинено только территории более высокого уровня
        $this->where = "AND s.уровень < '" . $this->territory->уровень . "' "; 
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->model);
        $this->where .= $constr->get_where();
        $constr->set('наименование');
        $this->constraints = $constr->get_constraints();
    }
    
    protected function add(Territory_Query $item)
    {
        parent::add($item);
    }
    
    public function display_table()
    {
        $g = array();
        $g[0][] = array('name' => 'parent_id' , 'title' => '', 'sort' => false, 'type' => 'radio' ); 
        $g[0][] = array('name' => 'наименование' ,'title' => 'Наименование', 'sort' => true, 'type' => 'plain' );  
        $g[0][] = array('name' => 'уровень','title' => 'Уровень', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'ОКАТО','title' => 'Код ОКАТО', 'sort' => true, 'type' => 'plain' );
        $g[0][] = array('name' => 'код_ОУЗ','title' => 'Код ОУЗ', 'sort' => true, 'type' => 'plain' ); 
        $g[0][] = array('name' => 'default','title' => 'Образующая Территория', 'sort' => false, 'type' => 'image' ); 
        if (count($this->items > 0)) {
            $i = 1;
            foreach ($this->items as $item) {
                $g[$i][] = $item->oid;
                $g[$i][] = $item->наименование;
                $g[$i][] = Reference::get_name($item->уровень, 'territory_types', 'dic_');
                $g[$i][] = $item->ОКАТО;
                $g[$i][] = $item->код_ОУЗ;
                $g[$i][] = $this->territory->parent_id == $item->oid ? 'default' : null;
                $i++;
            }
        }
        $footer = $this->display_pagination();
        $current_obj = '<input type="hidden" name="oid[]" value="'. $this->registry->oid[0] .'" />';
        $t = new HTMLGrid($g, $footer, $this->limitstart, $this->order, $this->direction);    
        $table = $t->render_table();
        $admin_form = $this->constraints .  $current_obj . $table;
        return $admin_form;
    }
    
}

?>