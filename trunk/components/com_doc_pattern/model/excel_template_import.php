<?php
/** 
* @version		$Id: excel_template_import.php,v 1.0 2012/02/28 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel'.DS.'IOFactory.php' );

class ExcelTemplateImport 
{
    private $doc_excel;
    private $sect_obj;
    private $path;
    private $file;
    
    public function __construct($file = false, $section = null, $cell_range = null) {
        if (!$file) {
            throw new Exception("Не определен файл для импорта");
        }
        if (!$section) {
            throw new Exception("Не определен раздел отчетного документа");
        }
        $this->sect_obj = new DocDpSectionQuery($section);
        $this->path = UPLOADS . DS . $file;
        $this->file = $file;
    }
    
    public function excel_convert()
    {
        $this->doc_excel = PHPExcel_IOFactory::load($this->path);
        $this->doc_excel->setActiveSheetIndex(0);
        $a_sheet = $this->doc_excel->getActiveSheet();
        $html  = '<div id="grid" class="ui-widget-content grid"><table><tbody>';
        $r = 0;
        foreach ($a_sheet->getRowIterator() as $row) {
            $html .= '<tr>';
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);                                                      
            $c = 0;
            foreach ($cellIterator as $cell) {
                $data_type = $cell->getDataType();
                $id = "c{$c}_r{$r}";
                $st = $a_sheet->getStyle($cell->getCoordinate());
                $protection = $st->getProtection()->getLocked();
                $b_start_tag = ''; $b_end_tag = ''; 
                $bfont = $st->getFont()->getBold();
                if ($bfont) {
                    $b_start_tag = '<b>'; $b_end_tag = '</b>'; 
                }
                $f = $st->getNumberFormat()->getFormatCode();
                $type = '';
                if ($f == '0') {
                    if (strstr($f, '.')) {
                        $type = 'type="float"';
                    }
                    else {
                        $type = 'type="int"';
                    }
                }
                $class = '';
                if ($protection !== 'unprotected') {
                    $class = ' class="cellProtected"';
                }
                $html .= '<td id ="' . $id . '" ' . $type . $class .'>' . $b_start_tag . $cell->getValue() .  $b_end_tag  . '</td>' . "\n";
                $c++;
            }
            $html .= '</tr>';
            $r++;
        }
        $html .= '</tbody></table></div>';
        //print_r($html);
        $this->update_template_text($html);
        $converted = array();
        $converted['rows'] = $r;
        $converted['cells'] = $c;
        return $converted;
    }
    
    private function update_template_text($html = null)
    {
        if (!$html) {
            throw new Exception("Текст шаблона пуст"); 
        }
        $this->sect_obj->шаблон = $html;
        $this->sect_obj->save_template();
        return true;
    }
}

?>