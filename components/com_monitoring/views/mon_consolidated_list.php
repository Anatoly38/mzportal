<?php
/**
* @version		$Id: mon_consolidated_list.php,v 1.0 2011/10/26 23:10:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitotings
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class MonConsolidatedList extends ItemList
{
    protected $model        = 'MonConsolidatedViewQuery';
    protected $source       = 'mon_consolidated_view';
    protected $namespace    = 'mon_consolidated';
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
        $constr->add_filter('monitoring_id', 'mon_monitorings');
        $constr->add_filter('статус', 'dic_doc_report_status');
        $constr->add_filter('год', 'dic_mon_years');
        $constr->add_filter('период', 'mon_period_patterns', 'код');
        $constr->get_filters();
    }
    
    protected function add(MonConsolidatedViewQuery $item)
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