<?php
/**
* @version      $Id $
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DossierList extends ItemList
{
    protected $model        = 'DossierQuery';
    protected $source       = 'attest_dossier';
    protected $namespace    = 'dossier';
    protected $task         = 'dossier_list';
    protected $obj          = 'dossier';
    protected $order_task   = 'dossier_list';
    protected $default_cols = array( 'oid', 'номер_дела', 'фио','мо', 'экспертная_группа');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('номер_дела');
        $constr->add_filter('фио');
        $constr->add_filter('экспертная_группа', 'dic_expert_groups', 'наименование', 'наименование', 'экспертная группа');
        $constr->add_filter('мо', 'pasp_lpu', 'сокращенное_наименование', 'сокращенное_наименование' ,'медицинская организация');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['номер_дела']  = array('sort' => true,  'type' => 'plain');
        $options['фио']         = array('sort' => true,  'type' => 'plain');
        $options['мо']          = array('sort' => true,  'type' => 'plain', 'ref' => 'subordination' );
        $options['экспертная_группа'] = array('sort' => true,  'type' => 'plain', 'ref' => 'expert_groups' );
        return $options;
    }
  
}
?>