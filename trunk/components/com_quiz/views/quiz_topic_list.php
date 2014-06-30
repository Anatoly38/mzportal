<?php
/**
* @version      $Id: quiz_topic_list.php,v 1.0 2014/06/11 12:51:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizTopicList extends ItemList
{
    protected $model        = 'QuizTopicQuery';
    protected $source       = 'quiz_topic';
    protected $namespace    = 'quiz_topic';
    protected $task         = 'default';
    //protected $obj          = 'quize_topic';
    protected $default_cols = array( 'oid', 'название_темы', 'аттестуемая_специальность', 'экспертная_группа' );
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('название_темы');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']                         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['название_темы']               = array('sort' => true, 'type' => 'plain');
        $options['аттестуемая_специальность']   = array('sort' => true, 'type' => 'plain', 'ref' => 'attest_specialities' ); 
        $options['экспертная_группа']           = array('sort' => true, 'type' => 'plain');
        return $options;
    }
  
}
?>