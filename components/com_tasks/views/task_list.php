<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Tasks
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class TaskList extends ItemList
{
    protected $model = 'TaskQuery';
    protected $source = 'tasks';
    protected $namespace = 'task_list';
    protected $obj = 'component';
    protected $default_cols = array('oid', 'наименование', 'описание', 'component_id', 'входит_в');
    
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
        $options['oid']                     = array('sort' => false, 'type' => 'checkbox' ); 
        $options['наименование']            = array('sort' => true, 'type' => 'plain' );
        $options['описание']                = array('sort' => true, 'type' => 'plain' );
        $options['component_id']            = array('sort' => true, 'type' => 'plain' ); 
        $options['входит_в']                = array('sort' => true, 'type' => 'plain' );
        return $options;
    }
    
}
?>