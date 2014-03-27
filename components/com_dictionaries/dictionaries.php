<?php
/**
* @version		$Id: dictionaries.php,v 1.0 2011/03/22 13:51:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
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
require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'dic_query.php' );
require_once ( 'views' . DS . 'dictionaries_list.php' );
require_once ( 'views' . DS . 'dictionary_items_list.php' );
require_once ( MODULES . DS . 'mod_staffconv' . DS . 'frmr_dictionary_import.php' );
require_once ( MODULES . DS . 'mod_staffconv' . DS . 'frmr_upload_file_save.php' );

class Dictionaries extends Component
{
    
    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_edit()
    {
        $dic_name = Request::getVar('dic_name');
        if (!$dic_name[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Словарь для редактирования не определен!');
            $this->view_list();
        }
        else {
            $this->view_dic_items($dic_name[0]);
        }
    }
    
    protected function exec_cancel()
    {
        $this->view_list();
    }
    
    protected function exec_close_lists()
    {
        $this->view_list();
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new LpuSave();
            $s->insert_data();
        } 
        else {
            $s = new LpuSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Описание учреждения не определено!');
            $this->view_list();
        } 
        $s = new LpuSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }
    
    function exec_upload_xml_form()
    {
        $dic_name = Request::getVar('dic_name');
        if (!$dic_name[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Справочник для импорта не определен!');
            $this->view_list();
        }
        else {
            $this->view_upload_form($dic_name[0]);
        }
    }

    function exec_upload_dict_save()
    {
        $dic_name = Request::getVar('dic_name');
        if (!$dic_name) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Словарь для импорта данных не определен!');
            $this->view_list();
        }
        try {
            $uploaded = new FrmrDicUploadFileSave();
            $uploaded->save_file();
        }
        catch (UploadException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->message . 'Код ошибки ' . $e->code);
            $this->view_list();
        }
        $this->view_check_uploaded($dic_name, $uploaded->get_uploaded_name());
    }
    
    function exec_save_imported_records()
    {
        $m = Message::getInstance();
        $dic_name = Request::getVar('dic_name');
        if (!$dic_name) {
            $m->enque_message('error', 'Словарь для импорта данных не определен!');
            $this->view_list();
        }
        $uid = Request::getVar('uid');
        if (!$uid) {
            $m->enque_message('error', 'Не выбрано ни одной записи из словаря для импорта данных!');
        }
        $file = Request::getVar('file');
        if (!$file) {
            $m->enque_message('error', 'Не определен файл для импорта данных!');
        }
        try {
            $f = new FrmrDictionaryImport($file, $dic_name);
            $cnt = $f->import_dictionary($uid);
        }
        catch (UploadException $e) {
            $m->enque_message('error', $e->message . 'Код ошибки ' . $e->code);
        }
        $m->enque_message('alert', "Импортировано $cnt записей" );
        $this->view_list();
    }
    
    function exec_cancel_import() 
    {
        $lpu = Request::getVar('lpu_id');
        if ($lpu) {
            $this->view_personnel_list($lpu);
        } 
        else {
            $this->view_list();
        }
    }

    protected function view_list()
    {
        $dic_list = new DictionariesList();
        self::set_title('Список словарей системы');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать', 'Выберите словарь для редактирования');
        self::set_toolbar_button('upload', 'upload_xml_form' , 'Загрузка справочника ФРМР');
        $this->set_content($dic_list->get_items_page());
    }
    
    protected function view_dic_items($dic)
    {
        $dic_items = new DictionaryItemsList($dic);
        self::set_title('Значения словаря');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать значение', 'Выберите элемент для редактирования');
        $this->set_content($dic_items->get_items_page());
    }
    
    protected function view_edit_item() 
    {
        $i = new LpuItem($this->oid[0]);
        self::set_title('Редактирование данных учреждения "' . $i->get_name() . '"');
        $i->edit_item();
        self::set_toolbar('save', 'Сохранить');
        self::set_toolbar('apply', 'Применить');
        self::set_toolbar('close', 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_item() 
    {
        self::set_title('Ввод нового учреждения');
        $i = new LpuItem();
        $i->new_item(); 
        self::set_toolbar('save', 'Сохранить');
        self::set_toolbar('close', 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }


    // Загрузка словарей ФРМР в формате XML файлов
    protected function view_upload_form($dic_name = null)
    {
        $c = Content::getInstance();
        $c->set_modal();
        self::set_title('Импорт словаря из формата ФРМР');
        self::set_toolbar_button('upload', 'upload_dict_save' , 'Загрузить');
        self::set_toolbar_button('cancel', 'close_lists' , 'Закрыть');
        $form = FrmrDictionaryImport::create_upload_form($dic_name);
        $this->set_content($form);
    }
    
    protected function view_check_uploaded($dic_name, $uploaded)
    {
        $c = Content::getInstance();
        $c->set_modal();
        self::set_title("Импорт словаря $dic_name  из формата ФРМР");
        self::set_toolbar_button('save', 'save_imported_records' , 'Импортировать выбранные записи');
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $f = new FrmrDictionaryImport($uploaded, $dic_name);
        $file_info = $f->check_dictionary();
        $this->content = $file_info;
    }
}

?>