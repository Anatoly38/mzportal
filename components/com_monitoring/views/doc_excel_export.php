<?php
/** 
* @version      $Id: doc_excel_export.php,v 1.1 2012/03/24 14:50:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Monitoring
* @copyright    Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel'.DS.'IOFactory.php' );

class DocExcelExport 
{
    private $title = false;
    private $docExcel;
    private $aSheet;
    private $returned_file;
    
    public function __construct($sections, $print_template = null) 
    {
        if (!$sections) {
            throw new Exception("Не определен раздел документа для экспорта в формат Excel");
        }
        if ($print_template) {
            $path = TEMPLATES . DS . 'excel' . DS . $print_template . '.xlsx';
        } else {
            $path = TEMPLATES . DS . 'excel' . DS . $sect_obj->spattern . '.xlsx';
        }
        if (!file_exists($path)) {
            Message::error("Файл шаблона для экспорта в формат Excel не найден");
            return false;
        }
        $this->docExcel = PHPExcel_IOFactory::load($path);
        $i = 0;
        foreach ($sections as $s) {
            $sect_obj = new  MonSectionQuery($s->section);
            $this->docExcel->setActiveSheetIndex($i++);
            $this->aSheet =  $this->docExcel->getActiveSheet();
            $print_range = $this->get_cell_range($sect_obj->spattern, 'print');
            $data_range = $this->get_cell_range($sect_obj->spattern, 'data');
            $this->load_data($s->section, $print_range, $data_range);
        }
    }
    
    private function get_cell_range($spattern, $target = 'print')
    {
        $spat_obj = new DocDpSectionQuery($spattern);
        if ($target == 'print') {
            $rc_range = $spat_obj->диапазон_печати;
        }
        else if ($target == 'data') {
            $rc_range = $spat_obj->диапазон_данных;
        }
        if (!$rc_range) {
            return null;
        }
        $corners = explode(':', $rc_range);
        $left_upper = explode('_', $corners[0]); 
        $left_upper_col = substr($left_upper[0], 1); 
        $left_upper_row = substr($left_upper[1], 1);
        $right_bottom = explode('_', $corners[1]);
        $right_bottom_col = substr($right_bottom[0], 1);
        $right_bottom_row = substr($right_bottom[1], 1);
        $cell_range = array();
        $cell_range['l']['col'] = $left_upper_col;
        $cell_range['l']['row'] = $left_upper_row;
        $cell_range['r']['col'] = $right_bottom_col;
        $cell_range['r']['row'] = $right_bottom_row;
        return $cell_range;
    }
    
    public function set_title($title = null)
    {
        $this->pExcel->getProperties()->setTitle($title);
    }
    
    public function set_creator($creator = null)
    {
        $this->pExcel->getProperties()->setCreator($creator);
    }
    
    public function set_properties($subject = null, $description = null, $creator = null)
    {
        $this->pExcel->getProperties()->setCreator()
                        ->setSubject($subject)
                        ->setDescription($description);
    }
    
    private function load_data($s = null, $p, $d)
    {
        if (!$s) {
            return;
            //throw new Exception("Раздел для экспорта в формат Excel не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT r, c, value FROM mon_cellstorage WHERE section = :1";
        $data = $dbh->prepare($query)->execute($s)->fetchall_assoc();
        if (!$data) {
            //Message::alert("Раздел пуст");
            return;
        }
        if (!$p || !$d) {
            throw new Exception("Диапазон ячеек для экспорта в формат Excel не определен");
        }
        $data_to_print = array();
        for ($i = $d['l']['row']; $i <= $d['r']['row']; $i++)
        {
            for ($j = $d['l']['col']; $j <= $d['r']['col']; $j++)
            {
                $data_to_print[$i][$j] = 0;
            }
        }
        foreach ($data as $row) {
            $data_to_print[$row['r']][$row['c']] = $row['value'];
        }
        $vert_offset = $d['l']['row'];
        for ($i = $p['l']['row']; $i <= $p['r']['row']; $i++)
        {
            $hor_offset = $d['l']['col'];
            for ($j = $p['l']['col']; $j <= $p['r']['col']; $j++)
            {
                $v = $data_to_print[$vert_offset][$hor_offset];
                if ($v != 0) {
                    $this->aSheet->setCellValueByColumnAndRow($j, $i, $v); 
                    }
                $hor_offset++;
            }
            $vert_offset++;
        }
    }

    public function render($file_name)
    {
        if (!$file_name) {
            $file_name = 'excel_output';
        }
        $writer = PHPExcel_IOFactory::createWriter($this->docExcel, 'Excel2007');
        ob_end_clean();
        //ini_set('zlib.output_compression','Off'); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
?>
