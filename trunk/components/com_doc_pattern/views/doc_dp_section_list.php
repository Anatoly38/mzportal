<?php
/**
* @version		$Id: doc_dp_section_list.php,v 1.0 2011/05/03 16:33:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
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
    
    protected function add(DocDpSectionQuery $item)
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