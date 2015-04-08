<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Att_Admin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DossierTicketList extends ItemList
{
    protected $model        = 'QuizTicketQuery';
    protected $source       = 'attest_dossier_ticket_view';
    protected $namespace    = 'dossier_ticket';
    protected $task         = 'ticket_list';
    protected $obj          = 'ticket';
    protected $order_task   = 'answer_list';
    protected $default_cols = array( 'oid', 'текст_ответа', 'правильный');
    protected $question;
    
    public function __construct($question)
    {
        parent::__construct($this->model, $this->source, $this->namespace );
        $this->question = $question;
        $this->where = " AND s.question_id = '{$this->question}' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('текст_ответа');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']             = array('sort' => false, 'type' => 'checkbox' ); 
        $options['текст_ответа']    = array('sort' => true,  'type' => 'plain');
        $options['правильный']      = array('sort' => true,  'type' => 'plain'); 
        return $options;
    }
    
}
?>