<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2090-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizTopicList extends ItemList
{
    protected $model        = 'QuizTopicViewQuery';
    protected $source       = 'quiz_topic_countquestion';
    protected $namespace    = 'quiz_topic';
    protected $task         = 'topic_list';
    protected $order_task   = 'topic_list';
    protected $default_cols = array( 'oid', 'название_темы', 'аттестуемая_специальность', 'экспертная_группа', 'количество_вопросов');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('название_темы');
        $constr->add_filter('экспертная_группа', 'dic_expert_groups', 'наименование', 'наименование', 'экспертная группа');

        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['название_темы']               = array('sort' => true, 'type' => 'plain');
        $options['аттестуемая_специальность']   = array('sort' => true, 'type' => 'plain', 'ref' => 'attest_specialities' ); 
        $options['экспертная_группа']           = array('sort' => true, 'type' => 'plain', 'ref' => 'expert_groups' );
        $options['количество_вопросов']         = array('sort' => true, 'type' => 'plain');
        return $options;
    }
  
}
?>