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

class TerritoryList extends ItemList
{
    protected $model = 'TerritoryQuery';
    protected $source = 'pasp_territory';
    protected $namespace = 'territory_list';
    protected $task = 'default';
    protected $default_cols = array('oid', 'наименование', 'сокр_наименование', 'уровень', 'ОКАТО', 'код_ОУЗ', 'подчинение');

    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('уровень' , 'dic_territory_types');
        $constr->get_filters();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                 = array('sort' => false, 'type' => 'checkbox' ); 
        $options['наименование']        = array('sort' => true, 'type' => 'plain' );
        $options['сокр_наименование']   = array('sort' => true, 'type' => 'plain' );
        $options['уровень']             = array('sort' => true, 'type' => 'plain', 'ref' => 'territory_types');
        $options['ОКАТО']               = array('sort' => true, 'type' => 'plain' ); 
        $options['код_ОУЗ']             = array('sort' => true, 'type' => 'plain' );
        $options['подчинение']          = array('sort' => true, 'type' => 'plain' );

        return $options;
    }

}

?>