<?php
/**
* @version      $Id: quiz_question_list.php,v 1.0 2014/06/11 12:51:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizQuestionList extends ItemList
{
    protected $model        = 'QuizQuestionQuery';
    protected $source       = 'quiz_question';
    protected $namespace    = 'quiz_question';
    protected $task         = 'default';
    protected $obj          = 'quiz_question';
    protected $default_cols = array( 'oid', 'текст_вопроса', 'тип_вопроса' );
    
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
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                 = array('sort' => false, 'type' => 'checkbox' ); 
        $options['текст_вопроса']       = array('sort' => true,  'type' => 'plain');
        $options['тип_вопроса']   		= array('sort' => true,  'type' => 'plain', 'ref' => 'question_type' ); 
        return $options;
    }
  
}
?>