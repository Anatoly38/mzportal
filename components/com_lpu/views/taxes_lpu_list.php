<?php
/**
* @version		$Id: taxes_list.php,v 1.0 2014/06/01 13:40:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Tasks
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class TaxesLpuList extends ItemList
{
    protected $model = 'TaxQuery';
    protected $source = 'taxes_lpu';
    protected $namespace = 'taxes_lpu_list';
    protected $task         = 'edit_tax';
    protected $order_task   = 'taxes';
    protected $obj          = 'tax';
    protected $default_cols = array( 'oid', 'инн', 'кпп');
    protected $lpu; 
    
    public function __construct($lpu)
    {
        if (!$lpu) {
            throw new Exception("Учреждение не определено");
        }
        parent::__construct($this->model, $this->source, $this->namespace);
        $this->lpu = $lpu;
        $this->where = " AND s.subject = '{$this->lpu}' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('инн');
        $constr->get_filters();
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid'] = array('sort' => false, 'type' => 'checkbox' ); 
        return $options;
    }
    
}
?>