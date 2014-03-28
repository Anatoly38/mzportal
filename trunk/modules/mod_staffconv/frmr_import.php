<?php
/** 
* @version		$Id: frmr_import.php,v 1.0 2010/08/17 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	FRMR Import Module
* @copyright	Copyright (C) 2010 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 
Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
// Импорт данных из формата (XML) федерального регистра медработников 
class FrmrImport 
{
    private $doc = false; // DOM Document
    private $replace = true; 
    private $path;
    private $file;
    private $personnel = array();
    
    public function __construct($xml_file = false, $lpu = null) {
        if (!$xml_file) {
            throw new Exception("Не определен файл для импорта");
        }
        if (!$lpu) {
            throw new Exception("Не определено учреждение для импорта данных сотрудников");
        }
        $this->lpu = $lpu;
        $this->doc = new DOMdocument();
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true; 
        $this->path = FRMR_UPLOADS . DS . $xml_file;
        $this->file = $xml_file;
        If (!$this->doc->load($this->path, LIBXML_NOWARNING)) {
            throw new Exception("Ошибка загрузки XML файла");
        }
    }

    public function check_employee_reestr() // Проверка состава данных
    {
        $emps = $this->doc->getElementsByTagName('Employee');
        $count_emp = $emps->length;
        $tags = "";
        $i = 1;
        $j = 0;
        foreach ($emps as $emp) {
            $id = $emp->getElementsByTagName('ID')->item(0)->nodeValue;
            $name = $emp->getElementsByTagName('Name')->item(5)->nodeValue;
            $surname = $emp->getElementsByTagName('Surname')->item(0)->nodeValue;
            $patroname = $emp->getElementsByTagName('Patroname')->item(0)->nodeValue;
            $snils = $emp->getElementsByTagName('SNILS')->item(0)->nodeValue;
            $exist = AdapterPersonnelQuery::get_by_snils($snils, $this->lpu);
            $marked = null;
            if ($exist) {
                $marked = 'id="error"';
                $j++;
            }
            $tags .= "<p $marked>$i. <input type=\"checkbox\" name=\"uid[]\" value=\"$id\" checked=\"checked\" /> $surname $name $patroname СНИЛС: $snils</p>";
            $i++;
        }
        $title = "<p>Число сотрудников в загруженном файле - $count_emp</p>";
        $title .= "<p>Из них с совпадающим СНИЛС - $j (совпадающие записи будут перезаписаны!)</p>";
        $title .= "<p>В том числе:</p>";
        $title .= "<input type=\"hidden\" name=\"file\" value=\"{$this->file}\"/>";
        $title .= "<input type=\"hidden\" name=\"lpu_id\" value=\"{$this->lpu}\"/>";
        $tags = $title . $tags; 
        return $tags;
    }

    public function import_employee_reestr($uid = false) // Импорт данных о сотрудниках
    { 
        if (!$uid) {
            return false;
        }
        $emps = $this->doc->getElementsByTagName('Employee');
        $count_emp = $emps->length;
        $i = 0;
        foreach ($emps as $emp) {
            $id = $emp->getElementsByTagName('ID')->item(0)->nodeValue;
            $snils = $emp->getElementsByTagName('SNILS')->item(0)->nodeValue;
            $action = 'update';
            if (in_array($id, $uid)) {
                if (!$item = AdapterPersonnelQuery::get_by_snils($snils, $this->lpu)) {
                    $item =  new AdapterPersonnelQuery();
                    $action = 'insert';
                }
                $item->lpu_id = $this->lpu;
                $item->табельный_номер      = $emp->getElementsByTagName('TabelNumber')->item(0)->nodeValue;
                $item->снилс                = $snils; 
                $item->инн                  = $emp->getElementsByTagName('INN')->item(1)->nodeValue;
                $item->фамилия              = $emp->getElementsByTagName('Surname')->item(0)->nodeValue;
                $item->имя                  = $emp->getElementsByTagName('Name')->item(5)->nodeValue;
                $item->отчество             = $emp->getElementsByTagName('Patroname')->item(0)->nodeValue;
                $item->пол                  = Reference::get_from_frmr($emp->getElementsByTagName('Sex')->item(0)->nodeValue, 'administrative_sex');
                $item->дата_рождения        = $emp->getElementsByTagName('Birthdate')->item(0)->nodeValue;
                $item->дата_смерти          = $emp->getElementsByTagName('Deathdate')->item(0)->nodeValue;
                $item->$action();
                if (!$item->oid) {
                    throw new Exception("Ошибка добавления записи о сотруднике при импорте из ФРМР");
                }
                $doc_node = $emp->getElementsByTagName('Document')->item(0);
                $this->add_documents($item->oid, $doc_node);
                $record_node = $emp->getElementsByTagName('EmployeeRecords')->item(0);
                $this->add_records($item->oid, $record_node);
                $education_node = $emp->getElementsByTagName('EmployeeSpecialities')->item(0);
                $this->add_education($item->oid, $education_node);
                $i++;
            }
        }
        return $i++;
    }
    
    private function add_documents($oid, $doc_node)
    {
        $doc = new PersonnelDocumentQuery();
        $card_document_link = Reference::get_id('документы', 'link_types');
        $doc->тип_документа     = $doc_node->getElementsByTagName('ID')->item(0)->nodeValue;
        $doc->серия_документа   = $doc_node->getElementsByTagName('Serie')->item(0)->nodeValue;
        $doc->номер_документа   = $doc_node->getElementsByTagName('Number')->item(0)->nodeValue;
        $doc->кем_выдан         = $doc_node->getElementsByTagName('Issued')->item(0)->nodeValue;
        $doc->дата_выдачи       = $doc_node->getElementsByTagName('IssueDate')->item(0)->nodeValue;
        $doc->insert();
        LinkObjects::set_link($oid, $doc->oid, $card_document_link);
    }
    
    private function add_education($oid, $edu_node)
    {
        $educations = $edu_node->getElementsByTagName('DiplomaEducation');
        $card_edu_link = Reference::get_id('образование', 'link_types');
        foreach ($educations as $edu) {
            $item = new PersonnelEducationQuery();
            $item->учебное_заведение = $edu->getElementsByTagName('GraduatedFrom')->item(0)->firstChild->nodeValue;
            $item->год_окончания     = $edu->getElementsByTagName('GraduationDate')->item(0)->nodeValue;
            $item->серия_диплома     = $edu->getElementsByTagName('DiplomaSerie')->item(0)->nodeValue;
            $item->номер_диплома     = $edu->getElementsByTagName('DiplomaNumber')->item(0)->nodeValue;
            $item->специальность     = $edu->getElementsByTagName('GraduationSpeciality')->item(0)->firstChild->nodeValue;
            $item->тип_образования   = $edu->getElementsByTagName('Type')->item(0)->firstChild->nodeValue;
            $item->insert();
            LinkObjects::set_link($oid, $item->oid, $card_edu_link);
        }
    }
    
    private function add_records($oid, $record_node)
    {
        $recs = $record_node->getElementsByTagName('CardRecord');
        $card_record_link = Reference::get_id('должность', 'link_types');
        foreach ($recs as $rec) {
            $item = new PersonnelRecordQuery();
            $item->вид_должности                    = $rec->getElementsByTagName('RecrodPosition')->item(0)->firstChild->nodeValue;
            $item->тип_должности                    = $rec->getElementsByTagName('RecordPositionType')->item(0)->firstChild->nodeValue;
            $item->должность                        = $rec->getElementsByTagName('RecordPost')->item(0)->firstChild->nodeValue;
            $item->ставка                           = $rec->getElementsByTagName('Wage')->item(0)->nodeValue;
            $item->дата_начала_труд_отношений       = $rec->getElementsByTagName('DateBegin')->item(0)->nodeValue;
            $item->тип_записи_начало                = $rec->getElementsByTagName('TypeIn')->item(0)->firstChild->nodeValue;
            $item->номер_приказа_начало             = $rec->getElementsByTagName('OrderIn')->item(0)->nodeValue;
            @$item->дата_окончания_труд_отношений   = $rec->getElementsByTagName('DateEnd')->item(0)->nodeValue; 
            @$item->тип_записи_окончание            = $rec->getElementsByTagName('TypeOut')->item(0)->firstChild->nodeValue;
            @$item->номер_приказа_окончание         = $rec->getElementsByTagName('OrderOut')->item(0)->nodeValue;
            $item->режим_работы                     = $rec->getElementsByTagName('RecordRegime')->item(0)->firstChild->nodeValue;
            $item->военная_служба                   = $rec->getElementsByTagName('RecordMilitary')->item(0)->firstChild->nodeValue;
            $item->подразделение                    = $rec->getElementsByTagName('RecordSubdivision')->item(0)->firstChild->nodeValue;
            $item->вид_мп                           = $rec->getElementsByTagName('Care')->item(0)->firstChild->nodeValue;
            $item->условия_мп                       = $rec->getElementsByTagName('Conditions')->item(0)->firstChild->nodeValue;
            $item->insert();
            LinkObjects::set_link($oid, $item->oid, $card_record_link);
        }
    }
    
    public static function create_upload_form($lpu = null)
    {
        $tags = '<div>';
        $tags .= '<input type="hidden" id="lpu_id" name="lpu_id" value="'.$lpu.'" />';
        $tags .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />';
        $tags .= 'Выберите файл для загрузки: <input name="frmr_file" type="file" />';
        $tags .= '</div>';
        return $tags;
    }
}

?>