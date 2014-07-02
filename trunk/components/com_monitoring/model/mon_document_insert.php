<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( COMPONENTS.DS.'com_period'.DS.'model'.DS.'period_query.php' );

class MonDocumentInsert
{
    private $model = 'MonDocumentQuery';
    private $query; 

    public function __construct()
    {
        $this->query = new $this->model();
        $this->insert_document();
    }
    
    public function insert_document()
    {
        $this->query->тип_отчета    = 1;
        $this->query->год           = Request::getVar('год');
        $this->query->статус        = 4; // новый
        $this->query->комментарий   = Request::getVar('комментарий');
        // ассоциированные объекты
        $lpu_id                     = Request::getVar('lpu_id');
        $pattern_id                 = Request::getVar('pattern_id');
        $year                       = Request::getVar('год');
        $period_id                  = Request::getVar('period');
        if (empty($year)) {
            throw new Exception("Не выбран текущий год");
        }
        $period = $this->get_period($period_id, $year);
        if (!$this->dublicated($lpu_id, $pattern_id, $period)) {
            $this->query->insert();
            if (!$this->query->oid) {
                throw new Exception("Ошибка сохранения нового документа"); 
            }
            $this->set_lpu($lpu_id);
            $this->set_pattern($pattern_id);
            $this->set_period($period);
            $this->query->acl_id = $this->set_acl($this->query->oid, $lpu_id);
            $this->insert_sections($pattern_id);
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

    private function set_lpu($lpu_id)
    {
        if (!$lpu_id) {
            throw new Exception("Учреждение выполняещее отчет не определено");
        }
        try {
            $link = Reference::get_id('отчет-лпу', 'link_types');
            LinkObjects::set_link($this->query->oid, $lpu_id , $link); // Ассоциация Отчет - Лечебное учреждение
            return true;
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (Отчет, ЛПУ) не сохранена!');
            return false;
        }
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
    
    private function get_period($period_id, $year)
    {
        $period = array();
        if (!$period_id) {
            throw new Exception("Отчетный период не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT начало, окончание FROM mon_period_patterns WHERE код = :1";
        list($begin, $end) = $dbh->prepare($query)->execute($period_id)->fetch_row();
        $period[0] = str_replace('9999', $year, $begin);
        $period[1] = str_replace('9999', $year, $end);
        $leap_year = date('L', $this->u_time($period[1]));
        if ($leap_year) {
            $period[1] = str_replace('28', '29', $period[1]);
        }
        $period[2] = $period_id;
        return $period;

    }
    
    private function u_time($date) {
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/",$date, $res)) {
            $info["year"]=$res[1];
            $info["month"]=$res[2];
            $info["day"]=$res[3];
            $u_time=mktime(0,0,0,$info["month"],$info["day"],$info["year"]);
            return($u_time);
        } else {
            return false;
        }
    }
    
    private function set_period($p)
    {
        try {
            $period = PeriodQuery::find_period($p[0], $p[1]);
            Message::alert('Отчет добавлен к существующему периоду');
        }
        catch (Exception $e) {
            $period = new PeriodQuery(); 
            $period->начало     = $p[0];
            $period->окончание  = $p[1];
            $period->шаблон_периода  = $p[2];
            $period->insert();
            Message::alert('Введен новый отчетный период');
        }
        try {
            $link = Reference::get_id('отчет-период', 'link_types');
            LinkObjects::set_link($this->query->oid, $period->oid , $link); // Ассоциация Отчет - Период
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (Отчет - Период) не сохранена!');
            return false;
        }
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
        foreach ($spatterns as $s) {
            $section = new MonSectionQuery();
            $section->document = $this->query->oid;
            $section->spattern = $s;
            $section->acl_id = $doc_acl;
            $section->insert();
        }
    }
    
    private function dublicated($lpu_id, $pattern_id, $period)
    {
        $dbh = new DB_mzportal;
        $query = "SELECT * FROM `mon_documents_view` `m`
                    JOIN `sys_objects` `o` on `m`.`oid` = `o`.`oid` 
                    WHERE `m`.`pattern_id` = :1 AND `m`.`lpu_id` = :2 AND `m`.`начало` = :3 AND `m`.`окончание` = :4 AND `o`.`deleted` = '0'";
        list($id) = $dbh->prepare($query)->execute($pattern_id, $lpu_id, $period[0], $period[1])->fetch_row();
        if ($id) {
            return true;
        }
        else {
            return false;
        }
    }
    
    private function set_acl($doc_id, $lpu_id)
    {
        if (!$doc_id || !$lpu_id) {
            throw new Exception("Не определен документ и/или учреждений для настройки разрешений");
        }
        $res = ACL::inherit($doc_id, $lpu_id);
        return $res;
    }
}
?>