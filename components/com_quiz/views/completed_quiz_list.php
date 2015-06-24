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

class CompletedQuizList extends ItemList
{
    protected $model        = 'AttestDossierTicketQuery';
    protected $source       = 'attest_dossier_ticket_view';
    protected $namespace    = 'result_list';
    protected $task         = 'result_list';
    protected $obj          = 'ticket';
    protected $order_task   = 'result_list';
    protected $default_cols = array( 'oid', 'фио', 'мо' ,'тема', 'начало_теста', 'продолжительность', 'настройка', 'статус', 'оценка', 'балл');
    protected $order = 'начало_теста';
    protected $default_order    = 'начало_теста';
    protected $direction        = 'desc';
    protected $default_direction    = 'desc';    

    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );
        $this->where = " AND s.реализована <>  '0' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('фио');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['фио']         = array('sort' => true,  'type' => 'plain'); 
        $options['мо']          = array('sort' => true,  'type' => 'plain', 'ref' => 'subordination' );
        $options['тема']        = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_topics' );
        $options['начало_теста']        = array('sort' => true,  'type' => 'plain', 'ref' => 'date_time' ); 
        $options['продолжительность']   = array('sort' => true,  'type' => 'plain', 'ref' => 'sec_to_min' ); 
        $options['настройка']   = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_settings'); 
        $options['статус']      = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_status'); 
        $options['оценка']      = array('sort' => true,  'type' => 'plain'); 
        $options['балл']        = array('sort' => true,  'type' => 'plain'); 
        return $options;
    }
}
?>