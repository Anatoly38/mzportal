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

class NPAssociationList extends ItemList
{
    protected $model        = 'NPAssociationQuery';
    protected $source       = 'attest_np_association';
    protected $namespace    = 'np_association';
    protected $task         = 'np_association_list';
    protected $obj          = 'np_association';
    protected $order_task   = 'np_association';
    protected $default_cols = array( 'oid', 'наименование', 'аббревиатура' );
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('наименование');
        $constr->add_filter('аббревиатура');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']             = array('sort' => false, 'type' => 'checkbox' ); 
        $options['наименование']    = array('sort' => true,  'type' => 'plain');
        $options['аббревиатура']    = array('sort' => true,  'type' => 'plain');
        return $options;
    }
  
}
?>