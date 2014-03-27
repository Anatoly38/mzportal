<?php
/**
* @version		$Id: doc_section_list.php,v 1.0 2011/09/19 1:46:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DocSectionList extends ItemList
{
    protected $model = 'MonSectionView';
    protected $source = 'mon_sections_view';
    protected $namespace = 'mon_section_list';
    protected $id       = 'section';
    protected $task     = 'sections';
    protected $obj      = 'section';
    protected $doc_pattern; 
    protected $default_cols = array('section', 'наименование', 'описание', 'заполнение');

    
    public function __construct($doc_id = false)
    {
        if (!$doc_id) {
            throw new Exception("Отчетный документ не определен");
        }
        parent::__construct($this->model, $this->source, $this->namespace);
        $this->where = " AND s.oid = '{$doc_id}' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->get_filters();
    }
    
    protected function add(MonSectionView $item)
    {
        parent::add($item);
    }
    
    protected function list_options()
    {
        $options = array();
        $options['section'] = array('sort' => false, 'type' => 'checkbox' ); 
        $options['тип']     = array('sort' => true, 'type' => 'plain', 'ref' => 'dp_section_type' );
        return $options;
    }
}
?>