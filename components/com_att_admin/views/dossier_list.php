<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2090-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DossierList extends ItemList
{
    protected $model        = 'AttestDossierTicketcountCabuserQuery';
    protected $source       = 'attest_dossier_ticketcount_cabuser_view';
    protected $namespace    = 'dossier';
    protected $task         = 'dossier_list';
    protected $obj          = 'dossier';
    protected $order_task   = 'dossier_list';
    protected $order = 'oid';
    protected $default_order    = 'oid';
    protected $direction        = 'desc';
    protected $default_direction    = 'desc';
    
    //protected $default_cols = array( 'oid', 'номер_дела', 'фио', 'email', 'мо', 'экспертная_группа', 'вид_должности', 'Кол_во_попыток_тестирования', 'Доступ_в_личный_кабинет');
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('номер_дела');
        $constr->add_filter('фио');
        $constr->add_filter('экспертная_группа', 'dic_expert_groups', 'наименование', 'наименование', 'экспертная группа');
        $constr->add_filter('вид_должности', 'dic_position_short', 'наименование', 'наименование', 'вид должности');
        $constr->add_filter('мо', 'pasp_lpu', 'сокращенное_наименование', 'сокращенное_наименование' ,'медицинская организация');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid']         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['номер_дела']  = array('sort' => true,  'type' => 'plain');
        $options['фио']         = array('sort' => true,  'type' => 'plain');
        $options['email']       = array('sort' => true,  'type' => 'plain');
        $options['мо']          = array('sort' => true,  'type' => 'plain', 'ref' => 'subordination' );
        $options['экспертная_группа']   = array('sort' => true,  'type' => 'plain', 'ref' => 'expert_groups' );
        $options['вид_должности']       = array('sort' => true,  'type' => 'plain', 'ref' => 'position_short' );
        $options['Кол_во_попыток_тестирования'] = array('sort' => true,  'type' => 'plain' );
        $options['Доступ_в_личный_кабинет']     = array('sort' => true,  'type' => 'plain' );
        return $options;
    }
  
}
?>