<?php
/** 
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Quiz
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'quiz_q_temp_query.php' );
require_once ( 'quiz_a_temp_query.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel'.DS.'IOFactory.php' );

class ExcelQuestionImport 
{
    private $doc_excel;
    private $q_obj; // вопросы
    private $a_obj; // ответы
    private $path;
    private $file;
    private $topic;
    
    public function __construct($file = false, $topic = null) {
        if (!$file) {
            throw new Exception("Не определен файл для импорта");
        }
        if (!$topic) {
            throw new Exception("Не определена тема теста");
        }
        $this->path = UPLOADS . DS . $file;
        $this->file = $file;
        $this->topic = $topic;
        MZSession::set('temp_topic', $topic, 'question_import');
        $this->q_obj =  new QuizQTempQuery();
        $this->a_obj =  new QuizATempQuery();
        $this->clear_temp_tables();
    }
    
    public function clear_temp_tables()
    {
        $this->q_obj->truncate_table();
        $this->a_obj->truncate_table();
    }
    
    public function excel_convert()
    {
        $this->doc_excel = PHPExcel_IOFactory::load($this->path);
        $this->doc_excel->setActiveSheetIndex(0); // Лист с вопросами
        $a_sheet = $this->doc_excel->getActiveSheet();
        $q = 1;
        foreach ($a_sheet->getRowIterator() as $row) {
            $c1 = 'A'. $q;
            $c2 = 'B'. $q;
            $q_number = $a_sheet->getCell($c1)->getValue();
            $q_text = $a_sheet->getCell($c2)->getValue();
            $q_text = trim(str_replace("_x000D_", " ", $q_text));
            $this->insert_qestion_text($q_number, $q_text);
            $q++;
        }
        $this->doc_excel->setActiveSheetIndex(1); // Лист с ответами
        $a_sheet = $this->doc_excel->getActiveSheet();
        $a = 1;
        foreach ($a_sheet->getRowIterator() as $row) {
            $c1 = 'A'. $a;
            $c2 = 'B'. $a;
            $c3 = 'C'. $a;
            $a_number   = $a_sheet->getCell($c1)->getValue();
            $a_text     = $a_sheet->getCell($c2)->getValue();
            $a_correct  = $a_sheet->getCell($c3)->getValue();
            $a_text = trim(str_replace("_x000D_", " ", $a_text));
            $this->insert_answer_text($a_number, $a_text, $a_correct);
            $a++;
        }
        $converted = array();
        $converted['q_count'] = $q - 1;
        $converted['a_count'] = $a - 1;
        return $converted;
    }
    
    private function insert_qestion_text($n, $q)
    {
        if (!$n || !$q) {
            return false;
        }
        $this->q_obj->номер_пп      = $n;
        $this->q_obj->текст_вопроса = $q;
        $this->q_obj->insert();
        return true;
    }
    
    private function insert_answer_text($n, $a, $c)
    {
        if (!$n || !$a) {
            return false;
        }
        $this->a_obj->id = $n;
        $this->a_obj->текст_ответа  = $a;
        $this->a_obj->правильный    = $c;
        $this->a_obj->insert();
        return true;
    }
}

?>