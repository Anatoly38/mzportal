<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizSettingList extends ItemList
{
    protected $model        = 'QuizSettingQuery';
    protected $source       = 'quiz_setting';
    protected $namespace    = 'quiz_setting';
    protected $task         = 'settings_list';
    protected $obj          = 'quiz_setting';
    protected $order_task   = 'settings_list';
    protected $default_cols = array( 'oid', 'наименование', 'доп_тема1_наименование', 'доп_тема1_доля', 'доп_тема2_наименование', 
                                    'доп_тема2_доля', 'доп_тема3_наименование', 'доп_тема3_доля', 'количество_вопросов', 
                                    'продолжительность_теста' , 'сортировка', 'показ_ответов');
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
        $options['наименование']            = array('sort' => true,  'type' => 'plain');
        $options['доп_тема1_наименование']  = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_topics' ); 
        $options['доп_тема1_доля']          = array('sort' => true,  'type' => 'plain');
        $options['доп_тема2_наименование']  = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_topics' ); 
        $options['доп_тема2_доля']          = array('sort' => true,  'type' => 'plain');
        $options['доп_тема3_наименование']  = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_topics' ); 
        $options['доп_тема3_доля']          = array('sort' => true,  'type' => 'plain');
        $options['количество_вопросов']     = array('sort' => true,  'type' => 'plain');
        $options['сортировка']              = array('sort' => true,  'type' => 'plain', 'ref' => 'bool' ); 
        $options['показ_ответов']           = array('sort' => true,  'type' => 'plain', 'ref' => 'bool' ); 
        return $options;
    }
    
}
?>