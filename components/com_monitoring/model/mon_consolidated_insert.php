<?php
/**
* @version		$Id: mon_consolidated_insert.php v 1.0 2012/01/10 01:37:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( COMPONENTS.DS.'com_period'.DS.'model'.DS.'period_query.php' );

class MonConsolidatedInsert
{
    private $query; 
    private $registry; 

    public function __construct($pattern_id, $period_oid, $summarization = true)
    {
        $this->query = new MonDocumentQuery();
        $this->query->тип_отчета    = 2;
        $this->query->статус        = 1;
        $this->query->комментарий   = Request::getVar('комментарий');
        // ассоциированные объекты

        $period = $this->get_period($period_oid);
        $this->query->год = $period[2];
        $this->query->acl_id = $this->set_acl();
        if (!$this->dublicated($pattern_id, $period)) {
            $this->query->insert();
            if (!$this->query->oid) {
                throw new Exception("Ошибка сохранения нового документа"); 
            }
            $this->set_pattern($pattern_id);
            $this->set_period($period_oid);
            $sections = $this->insert_sections($pattern_id);
            //print_r($sections);
            if ($summarization) {
                $affected = self::data_summarization($sections, $pattern_id, $period_oid); 
            }
            Message::alert('Обработано разделов ' . $affected[0] . ', Ячеек ' . $affected[1]);
            return true;
        } 
        else {
            Message::error('Ошибка добавления: Дублирование отчета!');
        }
    }
    
    public function get_document_id()
    {
        return $this->query->oid;
    }

    public static function data_summarization($ss, $pt, $pr)
    {
        $dbh = new DB_mzportal();
        // Запрос получения данных для сводных таблиц
        $query1 = "REPLACE INTO mon_cellstorage (section, r, c, value) 
                SELECT :1, r, c, SUM(`stor`.`value`) 
                FROM ( `mon_cellstorage` AS `stor` 
                JOIN `mon_sections` AS `sect` on `stor`.`section` = `sect`.`section` 
                JOIN `mon_documents_view` AS `doc` on `sect`.`document` = `doc`.`oid`
                JOIN `sys_objects` AS `o` on `doc`.`oid` = `o`.`oid`)
                WHERE `sect`.`spattern` = :2 AND `doc`.`pattern_id` = :3 AND `doc`.`period_id` = :4 AND `o`.`deleted` = '0'
                GROUP BY `stor`.`r`, `stor`.`c`";
        // Запрос для заполнения лога суммирования
        $query2 = "REPLACE INTO mon_summarization_log (`sect`, `org`, `r`, `c`, `val`) 
                SELECT :1, `doc`.`lpu_id`, `stor`.`r`, `stor`.`c`, `stor`.`value` 
                FROM (`mon_cellstorage` AS `stor` 
                JOIN `mon_sections` AS `sect` on `stor`.`section` = `sect`.`section` 
                JOIN `mon_documents_view` AS `doc` on `sect`.`document` = `doc`.`oid`
                JOIN `sys_objects` AS `o` on `doc`.`oid` = `o`.`oid`)
                WHERE `sect`.`spattern` = :2 AND `doc`.`pattern_id` = :3 AND `doc`.`period_id` = :4 AND `o`.`deleted` = '0'";
        $i = 0;
        $affected = 0;
        foreach ($ss as $s) {
            $s_obj = new MonSectionQuery($s);
            $s_pat = $s_obj->spattern;
            $dbh->prepare($query1)->execute($s, $s_pat, $pt, $pr);
            $aff = mysql_affected_rows();
            $affected += $aff;
            if ($aff > 0) {
                $dbh->prepare($query2)->execute($s['s_id'], $s['s_pat'], $pt, $pr);
            }
            $i++;
        }
        $result = array();
        $result[0] = $i;
        $result[1] = $aff;
        return $result;
    }
    
    private function set_pattern($pattern_id)
    {
        if (!$pattern_id) {
            throw new Exception("Шаблон отчета не определен");
        }
        try {
            $link = Reference::get_id('отчет-шаблон', 'link_types');
            LinkObjects::set_link($this->query->oid, $pattern_id , $link); // Ассоциация Отчет - Шаблон
            return true;
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (Отчет, Шаблон) не сохранена!');
            return false;
        }
    }
    
    private function get_period($period_oid)
    {
        if (!$period_oid) {
            throw new Exception("Отчетный период не определен");
        }
        $period = array();
        $dbh = new DB_mzportal;
        $query = "SELECT начало, окончание FROM periods WHERE oid = :1";
        list($begin, $end) = $dbh->prepare($query)->execute($period_oid)->fetch_row();
        $period[0] = $begin;
        $period[1] = $end;
        $date = new DateTime($begin);
        $period[2] = $date->format("Y");
        return $period;

    }
    
    private function set_period($period_oid)
    {
        $link = Reference::get_id('отчет-период', 'link_types');
        LinkObjects::set_link($this->query->oid, $period_oid , $link); // Ассоциация Отчет - Период
    }    
    
    private function insert_sections($p)
    {
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM `mon_section_patterns` WHERE `doc_pattern_id` = :1";
        $spatterns = $dbh->prepare($query)->execute($p)->fetch();
        if (!$spatterns) {
            throw new Exception("Документ не содержит шаблонов для заполнения");
        }
        $doc_acl = ACL::get_obj_acl($this->query->oid);
        $ids = array();
        $i = 0;
        foreach ($spatterns as $s) {
            $section = new MonSectionQuery();
            $section->document = $this->query->oid;
            $section->spattern = $s;
            $section->acl_id = $this->query->acl_id;
            $section->insert();
            $ids[]    = $section->section;
            //print_r($section);
            $i++;
        }
        return $ids;
    }
    
    private function dublicated($pattern_id, $period)
    {
        $dbh = new DB_mzportal;
        $query = "SELECT `m`.`oid` FROM `mon_consolidated_view` `m`
                    JOIN `sys_objects` `o` ON `m`.`oid` = `o`.`oid` 
                    WHERE `m`.`pattern_id` = :1 AND `m`.`начало` = :2 AND `m`.`окончание` = :3 AND `o`.`deleted` = '0'";
        list($id) = $dbh->prepare($query)->execute($pattern_id, $period[0], $period[1])->fetch_row();
        if ($id) {
            return true;
        }
        else {
            return false;
        }
    }
    
    private function set_acl()
    {
        $r = Registry::getInstance();
        $acl_id =  ACL::get_component_acl($r->application);
        return $acl_id;
    }
}
?>