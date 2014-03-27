<?php
/**
* @version		$Id: mon_document_list.php,v 1.0 2011/09/05 20:51:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitotings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class MonDocumentList extends ItemList
{
    protected $model        = 'MonDocumentViewQuery';
    protected $source       = 'mon_documents_view';
    protected $namespace    = 'mon_documents';
    protected $task         = 'default';
    protected $obj          = 'document';
    protected $default_cols = array( 'oid', 'мониторинг', 'шаблон', 'лпу', 'год' ,'Период', 'статус' );
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('monitoring_id', 'mon_monitorings', 'наименование' , 'наименование' , 'мониторинг');
        $constr->add_filter('pattern_id', 'mon_linked_patterns', 'наименование' , 'наименование' , 'отчет' , ' AND mon_id IS NOT NULL ');
        $constr->add_filter('статус', 'dic_doc_report_status');
        $constr->add_filter('territory_id', 'pasp_territory', 'сокр_наименование' , 'сокр_наименование' , 'муниципальное образование');
        $constr->add_filter('period_id', 'mon_periods_view', 'код', 'наименование' , 'период');
        $constr->get_filters();
    }
    
    protected function add(MonDocumentViewQuery $item)
    {
        parent::add($item);
    }

    protected function list_options()
    {
        $options = array();
        $options['oid']         = array('sort' => false, 'type' => 'checkbox' ); 
        $options['тип_отчета']  = array('sort' => true, 'type' => 'plain', 'ref' => 'doc_report_kind' );
        $options['статус']      = array('sort' => true, 'type' => 'plain', 'ref' => 'doc_report_status' );
        return $options;
    }
  
}
?>