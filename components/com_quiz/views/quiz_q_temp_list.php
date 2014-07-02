<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizQTempList extends ItemList
{
    protected $model        = 'QuizQTempQuery';
    protected $source       = 'quiz_q_temp';
    protected $namespace    = 'quiz_q_temp';
    protected $task         = 'default';
    protected $obj          = 'quiz_q_temp';
    protected $id           = 'номер_пп';
    //protected $default_cols = array( 'id', 'текст_вопроса', 'тип_вопроса' );
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('текст_вопроса');
        $constr->get_filters();
    }
    
    public function items_count($where = null)
    {
        $count_query =  "SELECT COUNT(*) FROM `{$this->source}`";
        list($count) = $this->dbh->execute($count_query)->fetch_row();
        return $count;
    }

    public function get_items()
    {
        if (!$this->conditions) {
            $where = $this->set_condition();
        } 
        else {
            $where = $this->conditions;
        }
        $this->total_items = $this->items_count($where);
        if ($this->limitstart > $this->total_items) {
            $this->limitstart = 0;
        }
        $query =    "SELECT s.номер_пп FROM {$this->source} AS s";
        if ($this->limit <> 0) {
            $query .= " LIMIT {$this->limitstart}, {$this->limit} ";
        }
        //print_r($query);
        $stmt = $this->dbh->execute($query)->fetch();
        foreach ($stmt as $id) {
            $this->add(new $this->model($id));
        }
        return $stmt;
    }
    
    protected function list_options()
    {
        $options = array();
        $options['номер_пп']            = array('sort' => false, 'type' => 'checkbox' ); 
        $options['текст_вопроса']       = array('sort' => true,  'type' => 'plain');
        $options['тип_вопроса']   		= array('sort' => true,  'type' => 'plain', 'ref' => 'question_types' ); 
        return $options;
    }
  
}
?>