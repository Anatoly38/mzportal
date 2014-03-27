<?php
/**
* @version		$Id: lpu_list.php,v 1.0 2011/05/12 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport LPU
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );
require_once ( MODULES . DS . 'mod_excel' . DS . 'excel_export.php' );

class LpuList extends ItemList
{
    protected $model        = 'LpuQuery';
    protected $source       = 'pasp_lpu_view';
    protected $namespace    = 'lpu';
    protected $obj          = 'lpu';
    protected $default_cols = array('oid', 'код_территории', 'огрн', 'сокращенное_наименование', 'почтовый_адрес' ,'вэб_сайт' , 'опф', 'руководитель');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('territory_id', 'pasp_territory', 'сокр_наименование' , 'сокр_наименование' , 'муниципальное образование');
        $constr->add_filter('ОПФ' , 'dic_legal_form');
        $constr->add_filter('состояние' , 'dic_inst_state');
        $constr->add_filter('обособленность' , 'dic_inst_affiliation');
        $constr->add_filter('уровень' , 'dic_level');
        $constr->add_filter('уровень_мп' , 'dic_medical_care_level', 'наименование' , 'наименование' , 'уровень мед. помощи');
        $constr->get_filters();
    }
    
    protected function add(LpuQuery $item)
    {
        parent::add($item);
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                     = array('sort' => false, 'type' => 'checkbox' ); 
        $options['обособленность']          = array('sort' => true, 'type' => 'plain', 'ref' => 'inst_affiliation' );
        $options['наименование']            = array('sort' => true, 'type' => 'link' );
        $options['сокращенное_наименование'] = array('sort' => true, 'type' => 'plain' );
        $options['опф']                     = array('sort' => true, 'type' => 'plain', 'ref' => 'legal_form' ); 
        $options['состояние']               = array('sort' => true, 'type' => 'plain', 'ref' => 'inst_state' );
        $options['уровень']                 = array('sort' => true, 'type' => 'plain', 'ref' => 'level' );
        $options['номенклатура']            = array('sort' => true, 'type' => 'plain', 'ref' => 'nomenclature' );
        $options['уровень_мп']              = array('sort' => true, 'type' => 'plain', 'ref' => 'medical_care_level' ); 
        return $options;
    }
    
}
?>