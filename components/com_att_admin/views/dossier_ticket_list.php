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
    protected $order_task   = 'quiz_ticket';
    protected $default_cols = array( 'oid', 'тема', 'настройка', 'в_процессе', 'реализована');
    protected $dossier_id;
    
    public function __construct($dossier_id)
    {
        parent::__construct($this->model, $this->source, $this->namespace );
        $this->dossier_id = $dossier_id;
        $this->where = " AND s.dossier_id = '{$this->dossier_id}' ";
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
        $options['oid']         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['тема']        = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_topics' );
        $options['настройка']   = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_settings'); 
        $options['настройка']   = array('sort' => true,  'type' => 'plain', 'ref' => 'quiz_settings'); 
        $options['в_процессе']  = array('sort' => true,  'type' => 'plain', 'ref' => 'bool'); 
        $options['реализована'] = array('sort' => true,  'type' => 'plain', 'ref' => 'bool'); 
        return $options;
    }
    
}
?>