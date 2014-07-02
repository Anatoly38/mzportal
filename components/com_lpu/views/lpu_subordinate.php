<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Indexes
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class LpuSubordinate extends ItemList
{
    protected $model = 'TerritoryQuery';
    protected $source = 'pasp_territory';
    protected $namespace = 'lpu_subordinate';
    protected $task         = 'subordinate';
    protected $order_task   = 'subordinate';
    protected $obj          = 'territory';
    protected $default_cols = array( 'oid', 'наименование', 'уровень');
    protected $lpu; 
    
    
    public function __construct($lpu)
    {
        if (!$lpu) {
            throw new Exception("Учреждение не определено");
        }
        parent::__construct($this->model, $this->source, $this->namespace);
        $this->lpu = $lpu;
        $this->where = "AND s.oid NOT IN (SELECT parent FROM pasp_lpu_parent_view WHERE код = '{$lpu}') ";      
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('уровень' , 'dic_territory_types');
        $constr->get_filters();
    }

    protected function list_options()
    {
        $options = array();
        $options['oid']     = array('sort' => false, 'type' => 'checkbox' );
        $options['уровень'] = array('sort' => true, 'type' => 'plain', 'ref' => 'territory_types' );
        
        return $options;
    }
}

?>