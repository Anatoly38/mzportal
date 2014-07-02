<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
define( 'PATTERNS', MZPATH_BASE .DS. 'templates' .DS. 'doc_patterns' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'views' . DS . 'doc_excel_export.php' );

class Monitoring extends ComponentACL
{
    protected $model;

    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_edit()
    {
        if (!$this->oid[0]) {
            Message::error('Отчетный документ не определен!');
            $this->view_list();
        }
        else {
            $this->view_edit_item();
        }
    }
    
    protected function exec_cancel()
    {
        $this->view_list();
    }
    
    protected function exec_excel_export()
    {
        $document = (array)Request::getVar('document');
        if (!$document) {
            Message::error('Отчетный документ не определен!');
            $this->view_list();
            return;
        }
        $doc = new $this->model($document[0]);
        if (empty($doc->шаблон_печати))  {
            Message::error('Шаблон печати не определен!');
            $this->view_list();
            return;
        }
        $list = new DocSectionList($document[0]);
        $list->get_items();
        $exp = new DocExcelExport($list->items, $doc->шаблон_печати);
        $exp->render();
    }
    
    protected function exec_sections()
    {
        $document = (array)Request::getVar('document');
        if (!$document) {
            Message::error('Отчетный документ не определен!');
            $this->view_list();
        }
        else {
            Content::set_route('document', $document[0]);
            $list = new DocSectionList($document[0]);
            $with_deleted = false;
            $with_acl = false;
            $restrict = $list->set_condition($with_deleted, $with_acl);
            $count = $list->items_count($restrict);
            //print_r($count);
            if ($count > 1) {
                $this->view_sections($document[0]);
            } 
            elseif ($count == 1) {
                $list->get_items();
                Content::set_route('section', $list->items[0]->section);
                Content::set_route('source', ''); 
                $this->view_edit_report_data($document[0], $list->items[0]->section);
            }
            elseif (!$count) {
                throw new Exception("Разделы не определены!");
            }
        }
    }
    
    protected function check_authority()
    {
        $authority = false; // никто
        if ( in_array(Reference::get_id('Изменения статуса отчетного документа:  редактор', 'rights'), $this->rights) ) {
            $authority = 1; // редактор
        }
        if ( in_array(Reference::get_id('Изменения статуса отчетного документа:  куратор', 'rights'), $this->rights) ) {
            $authority = 2; // куратор МО
        }
        if ( in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) 
            || in_array(Reference::get_id('Изменения статуса отчетного документа:  любые', 'rights'), $this->rights) ) {
            $authority = 3; // куратор Минздрава или администатор
        }
        return $authority;
    }
    
    protected function exec_delete()
    {
        if (!in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) && !in_array(Reference::get_id('Удаление', 'rights'), $this->rights)) {
            Message::error("Недостаточно прав на удаление документа/ов");
            $this->view_list();
            return;
        }
        $document = (array)Request::getVar('document');
        if (!$document) {
            Message::error('Документ не определен(ы)!');
            $this->view_list();
        }
        $authority = $this->check_authority();
        $doc_to_delete = array();
        $sect_to_delete = array();
        $objects_to_delete = array();
        foreach ($document as $d) {
            $o = new MonDocumentQuery($d);
            if ( $authority == 3 ) {
                $doc_to_delete[] = $d;
            } elseif ($authority == 2 && $o->статус != Reference::get_id('проверен', 'doc_report_status') && $o->статус != Reference::get_id('утвержден', 'doc_report_status')) {
                $doc_to_delete[] = $d;
            } elseif ($authority == 1 && $o->статус < 16) {
                $doc_to_delete[] = $d;
            }
        }
        foreach ($doc_to_delete as $dd) {
            $s = new DocSectionList($dd);
            $sections = $s->get_items();
            $sect_to_delete = array_merge($sect_to_delete, $sections);
        }
        $objects_to_delete = array_merge($doc_to_delete, $sect_to_delete);
        if (isset($objects_to_delete[0])) {
            $del = new DeleteItems($objects_to_delete);
            // Очистка данных ячеек форм
            foreach ($sect_to_delete as $sd) {
                SectionDataEdit::clear_data($sd);
            }
        }
        Message::alert("Удалено " . count($doc_to_delete) .  " документа/ов (включая " . count($sect_to_delete) . " раздел/ов) из " . count($document) . " выбранных");
        $this->view_list();
    }
    
    protected function exec_status_change()
    {
        $document = (array)Request::getVar('document');
        if (!$document[0]) {
            Message::error("Документ/ы не определен");
            $this->view_list();
            return;
        }
        $new_status = Request::getVar('new_status');
        $authority = $this->check_authority();
        $all = count($document);
        $new = 0;
        foreach ($document as $d) {
            $o = new MonDocumentQuery($d);
            if ( $authority == 3 ) {
                $o->set_status($new_status);
                $new++;
            } elseif ($authority == 2 && $o->статус != Reference::get_id('проверен', 'doc_report_status') &&  $o->статус != Reference::get_id('утвержден', 'doc_report_status')) {
                $o->set_status($new_status);
                $new++;
            } elseif ($authority == 1 && ($o->статус == Reference::get_id('редактирование', 'doc_report_status') || $o->статус == Reference::get_id('на доработке', 'doc_report_status')) ) {
                if ($new_status != Reference::get_id('редактирование', 'doc_report_status')) {
                    $o->set_status($new_status);
                    $new++;
                }
            }
        }
        Message::alert("Изменен статус у {$new} документа(ов) из {$all} выбранных");
        $this->view_list();
    }

    protected function exec_data_entering()
    {
        $document   = Request::getVar('document');
        $section    = Request::getVar('section');
        if (!$section[0]) {
            Message::error('Раздел отчетного документа не определен!');
            $this->view_list();
        }
        else {
            Content::set_route('document', $document);
            Content::set_route('section', $section[0]);
            Content::set_route('source', ''); 
            $this->view_edit_report_data($document, $section[0]);
        }
    }
    
    protected function exec_data_saving()
    {
        if (!in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) && !in_array(Reference::get_id('Изменение/Редактирование', 'rights'), $this->rights)) {
            Message::error("Недостаточно прав на редактирование документа/ов");
            $this->view_list();
            return;
        }
        $document   = Request::getVar('document');
        $section    = Request::getVar('section');
        $data       = Request::getVar('source');
        Content::set_route('document', $document);
        Content::set_route('section', $section);
        Content::set_route('source', ''); 
        if (!$data) {
            Message::alert('Нет данных для сохранения');
            $this->view_edit_report_data($document, $section);
            return;
        } 
        else {
            $d = new MonDocumentQuery($document);
            if ($d->статус != Reference::get_id('новый', 'doc_report_status') 
                && $d->статус != Reference::get_id('редактирование', 'doc_report_status') 
                && $d->статус != Reference::get_id('на доработке', 'doc_report_status')
                && !in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) 
                && !in_array(Reference::get_id('Изменения статуса отчетного документа:  любые', 'rights'), $this->rights) 
                && !in_array(Reference::get_id('Изменения статуса отчетного документа:  куратор', 'rights'), $this->rights)) {
                Message::error('Недостаточно прав для сохранения изменений в документе');
                $this->view_edit_report_data($document, $section);
                return;
            }
            if (($d->статус == Reference::get_id('проверен', 'doc_report_status') || $d->статус == Reference::get_id('утвержден', 'doc_report_status'))
                && !in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) 
                && in_array(Reference::get_id('Изменения статуса отчетного документа:  куратор', 'rights'), $this->rights)) {
                Message::error('Недостаточно прав для сохранения изменений в документе');
                $this->view_edit_report_data($document, $section);
                return;
            }
            $c = new MonCellStore($section);
            $s = new MonSectionQuery($section);
            $res = $c->save($data);
            $completeness = $res['filled']/$res['all']*100;
            if ($d->статус == 4 && $res['filled'] !==0) {
                $d->set_status(8);
            }
            $s->заполнение = $completeness;
            $s->update();
            Message::alert("Изменения сохранены. Заполнено {$res['filled']} значения/ий из {$res['all']} возможных");
            $this->view_edit_report_data($document, $section);
            return;
        }
    }

    protected function exec_current_acl()
    {
        if (!in_array(Reference::get_id('Полный доступ', 'rights'), $this->rights) && !in_array(Reference::get_id('Изменение доступа', 'rights'), $this->rights)) {
            Message::error("Отсутствуют права на изменение списков доступа");
            $this->view_list();
            return;
        }
        $document = (array)Request::getVar('document');
        if (!$document[0]) {
            Message::error("Документ/ы не определен");
            $this->view_list();
            return;
        }
        $this->oid = $document;
        if (count($document) > 1) {
            parent::exec_user_list();
            return;
        } else {
            parent::exec_current_acl();
            return;
        }
    }
    
    protected function exec_add_users_acl()
    {
        parent::exec_add_users_acl();
        $objects = (array)Request::getVar('object');
        foreach ($objects as $o) {
            $new_acl = ACL::get_obj_acl($o);
            $sections = new DocSectionList($o);
            $sections->get_items();
            foreach ($sections->items as $s) {
                $s_obj = new MZObject($s->section);
                $s_obj->acl_id = $new_acl;
                $s_obj->update();
            }
        }
    }

    protected function exec_remove_users_acl()
    {
        parent::exec_remove_users_acl();
        $object = (array)Request::getVar('object');
        $o = new MZObject($object);
        $new_acl = $o->acl_id;
        $sections = new DocSectionList($object);
        $sections->get_items();
        foreach ($sections->items as $s) {
            $s_obj = new MZObject($s->section);
            $s_obj->acl_id = $new_acl;
            $s_obj->update();
        }
    }
    
    protected function view_sections($doc_id)
    {
        $list = new DocSectionList($doc_id);
        $with_deleted = false;
        $with_acl = false;
        $restrict = $list->set_condition($with_deleted, $with_acl);
        self::set_title('Выберите раздел');
        $edit_b = self::set_toolbar_button('tableedit', 'data_entering' , 'Ввод данных');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $list->set_condition(false, false);
        $c = $list->get_items_page();
        $this->set_content($c);
    }
}
?>