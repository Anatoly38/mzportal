<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class ExpertGroupList extends ItemList
{
    protected $model        = 'ExpertGroupQuery';
    protected $source       = 'attest_expert_group';
    protected $namespace    = 'expert_group';
    protected $task         = 'expert_group_list';
    protected $obj          = 'expert_group';
    protected $order_task   = 'expert_group';
    protected $default_cols = array( 'oid', 'наименование');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']             = array('sort' => false, 'type' => 'checkbox' ); 
        $options['наименование']    = array('sort' => true,  'type' => 'plain');
        return $options;
    }
  
}
?>