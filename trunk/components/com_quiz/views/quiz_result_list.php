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

class QuizResultList extends ItemList
{
    protected $model        = 'QuizResultQuery';
    protected $source       = 'quiz_result';
    protected $namespace    = 'quiz_result';
    protected $task         = 'result_list';
    protected $order_task   = 'result_list';
    protected $obj          = 'quiz_result';
    protected $default_cols = array( 'oid', 'uid', 'topic_id', 'начало_теста', 'продолжительность_теста', 'оценка', 'балл', 'время_сохранения');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('topic_id', 'dic_quiz_topics', 'код', 'наименование' , 'тема теста');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['uid']                         = array('sort' => true, 'type' => 'plain');
        $options['topic_id']                    = array('sort' => true, 'type' => 'plain', 'ref' => 'quiz_topics' ); 
        $options['начало_теста']                = array('sort' => true, 'type' => 'plain');
        $options['продолжительность_теста']     = array('sort' => true, 'type' => 'plain');
        $options['оценка']                      = array('sort' => true, 'type' => 'plain');
        $options['балл']                        = array('sort' => true, 'type' => 'plain');
        $options['время_сохранения']            = array('sort' => true, 'type' => 'plain');
        return $options;
    }
  
}
?>