<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizQuestionList extends ItemList
{
    protected $model        = 'QuizQuestionViewQuery';
    protected $source       = 'quiz_question_topic';
    protected $namespace    = 'quiz_question';
    protected $task         = 'question_list';
    protected $obj          = 'quiz_question';
    protected $order_task   = 'question_list';
    protected $default_cols = array( 'oid', 'текст_вопроса', 'тип_вопроса', 'тема_теста', 'количество_ответов' );
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('текст_вопроса');
        $constr->add_filter('topic_id',     'dic_quiz_topics',      'код', 'наименование' , 'тема теста');
        $constr->add_filter('тип_вопроса',  'dic_question_types',   'код', 'наименование' , 'тип вопроса');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                 = array('sort' => false, 'type' => 'checkbox' ); 
        $options['текст_вопроса']       = array('sort' => true,  'type' => 'plain');
        $options['тип_вопроса']   		= array('sort' => true,  'type' => 'plain', 'ref' => 'question_types' ); 
        $options['тема_теста']          = array('sort' => true,  'type' => 'plain');
        $options['количество_ответов']  = array('sort' => true,  'type' => 'plain');
        return $options;
    }
  
}
?>