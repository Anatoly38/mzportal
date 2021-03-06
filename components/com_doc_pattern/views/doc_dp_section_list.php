<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport_LPU
* @copyright    Copyright (C) 2090-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DocDpSectionList extends ItemList
{
    protected $model = 'DocDpSectionQuery';
    protected $source = 'mon_section_patterns';
    protected $namespace = 'section_list';
    protected $task = 'section_list';
    protected $obj   = 'section';
    protected $doc_pattern; 
    protected $default_cols = array('oid', 'наименование', 'описание', 'тип');

    
    public function __construct($doc_pattern = false)
    {
        if (!$doc_pattern) {
            throw new Exception("Шаблон документа не определен");
        }
        parent::__construct($this->model, $this->source, $this->namespace);
        $this->doc_pattern = new DocPatternQuery($doc_pattern);
        $this->where = " AND s.doc_pattern_id = '{$this->doc_pattern->oid}' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('тип', 'dic_dp_section_type' );
        $constr->get_filters();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    protected function list_options()
    {
        $options = array();
        $options['oid'] = array('sort' => false, 'type' => 'checkbox' ); 
        $options['тип'] = array('sort' => true, 'type' => 'plain', 'ref' => 'dp_section_type' );
        return $options;
    }

}
?>