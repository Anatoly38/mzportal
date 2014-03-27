<?php
/**
* @version		$Id: data_section_edit.php,v 1.0 2011/09/21 15:59:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitoring
* @copyright	Copyright (C) 2010 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( COMPONENTS.DS.'com_doc_pattern'.DS.'model'.DS.'doc_dp_section_query.php' );

class SectionDataEdit
{
    private $grid;
    private $content;
    public $section_title;

    public function __construct($doc_id, $section_id)
    {
        if (!$doc_id || !$section_id) {
            throw new Exception("Для вызова данных необходим код документа и раздела");
        }
        $section = new MonSectionQuery($section_id);
        $s = new DocDpSectionQuery($section->spattern);
        $f = $s->get_template_text();
        $this->section_title = $s->наименование;
        if (!$f) {
            throw new Exception("Шаблон пуст");
        }
        $this->grid = new DOMDocument();
        $this->grid->loadXML($f);
        $rng = '<grammar xmlns="http://relaxng.org/ns/structure/1.0" datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes">
            <start><element><anyName/><ref name="anythingID"/></element></start>
            <define name="anythingID">
                <zeroOrMore>
                    <choice>
                        <element>
                            <anyName/>
                            <ref name="anythingID"/>
                        </element>
                        <attribute name="id">
                            <data type="ID"/>
                        </attribute>
                        <zeroOrMore>
                            <attribute><anyName/></attribute>
                        </zeroOrMore>
                        <text/>
                    </choice>
                </zeroOrMore>
            </define>
        </grammar>
        ';
        $this->grid->relaxNGValidateSource($rng); 
        //print_r($this->grid->saveXML());
        $this->load_data($section_id, $this->grid);
        $this->grid_edit();
    }

    private function load_data($s, $g)
    {
        $dbh = new DB_mzportal;
        $query = "SELECT * FROM mon_cellstorage WHERE section = :1";
        $data = $dbh->prepare($query)->execute($s)->fetchall_assoc();
        if (!$data) {
            Message::alert("Раздел пуст");
            return;
        }
        foreach ($data as $row) {
            $c = $row['c'];
            $r = $row['r'];
            $v = $row['value'];
            $loc = "c{$c}_r{$r}";
            $cell = $g->getElementById($loc);
            $classes = explode(' ', $cell->getAttribute('class'));
            $protected = in_array('cellProtected', $classes) ? 1 : 0;
            $calculated = in_array('cellCalculated', $classes) ? 1 : 0;
            $type = $cell->getAttribute('type');
            if (!$protected) {
                //if (!empty($v) && $v !=='0.00') {
                if (!is_null($v)) {
                     switch (true) {
                        case (!$type || $type == 'int') :
                            $v = (int)$v;
                        break;
                        case ($type == 'float') :
                            $v = (float)$v;
                        break;                        
                    } // Обработка строковых и значений других типов, пока не актуальна
                    $cell->nodeValue = $v;
                }
            }
            
        }
    }

    private function grid_edit()
    {
        $js = Javascript::getInstance();
        $js->add_grid();
    }

    public function get_content()
    {
        return $this->grid;
    }
    
    public static function clear_data($section)
    {
        if (!$section) {
            return;
        }
        $dbh = new DB_mzportal;
        $query1 = "DELETE FROM mon_cellstorage WHERE section = :1";
        $query2 = "DELETE FROM mon_summarization_log WHERE sect = :1";
        $dbh->prepare($query1)->execute($section);
        $dbh->prepare($query2)->execute($section);
        return;
    }
}

?>