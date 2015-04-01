<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'lpu_temp_query.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel'.DS.'IOFactory.php' );

class ExcelLpuImport 
{
    private $doc_excel;
    private $lpu_obj; // ЛПУ (временное хранение до импорта
    private $path;
    private $file; // Эксельный файл экспортированный из 1С
    
    public function __construct($file = false) {
        if (!$file) {
            throw new Exception("Не определен файл для импорта");
        }
        set_time_limit(0);
        $this->path = UPLOADS . DS . $file;
        $this->file = $file;
        $this->clear_temp_table();
    }
    
    public function clear_temp_table()
    {
        return LpuTempQuery::truncate_table();
    }
    
    public function excel_convert()
    {
        //$this->doc_excel = PHPExcel_IOFactory::load($this->path);
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $this->doc_excel = $objReader->load($this->path);
        $this->doc_excel->setActiveSheetIndex(0); // Лист с перечнем ЛПУ
        $a_sheet = $this->doc_excel->getActiveSheet();
        
        $highestRow = $a_sheet->getHighestRow();
        $highestColumn = $a_sheet->getHighestColumn(); 
        $dformat = 'd.m.Y';        
        
        // начинаем с пятой строки
        for ($r = 5; $r <= $highestRow ; $r++) { 
            $ogrn       = $a_sheet->getCellByColumnAndRow(8, $r)->getValue(); // номера столбцов в соответствии с форматом текущей выгрузки из 1С МИАЦ Отчетность статданных
            $full_name  = $a_sheet->getCellByColumnAndRow(1, $r)->getValue(); 
            $short_name = $a_sheet->getCellByColumnAndRow(2, $r)->getValue();
            $d = $a_sheet->getCellByColumnAndRow(35, $r)->getValue();
            if ($d) {
                $date = DateTime::createFromFormat($dformat, $d);
                $date1c = $date->format('Y-m-d');
            }
            else {
                $date1c = null;
            }
            $this->insert_lpu_inf($r, $ogrn, $full_name, $short_name, $date1c);
        }    
        $converted = $r - 5;
        return $converted;
    }
    
    private function insert_lpu_inf($n, $o, $f, $s, $d1c)
    {
        // исключаем строки не указан ОГРН медицинской организации
        if (!$o || !$n) { 
            return false;
        }
        $lpu_obj =  new LpuTempQuery();
        $lpu_obj->номер_пп      = $n;
        $lpu_obj->огрн          = $o;
        $lpu_obj->наименование  = $f;
        $lpu_obj->сокращенное_наименование = $s;
        $lpu_obj->date1c        = $d1c;
        $lpu_obj->insert();
        return true;
    }

}

?>