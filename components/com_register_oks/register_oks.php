<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	OKS Register
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
require_once ( 'model' . DS . 'adapter_oks_query.php' );
require_once ( 'model' . DS . 'patient_save.php' );
require_once ( 'model' . DS . 'patient_delete.php' );
require_once ( 'views' . DS . 'register_oks_list.php' );
require_once ( 'views' . DS . 'patient_oks.php' );

class RegisterOks extends Component
{
    
    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_cancel()
    {
        $this->view_list();
    }

    protected function exec_edit()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Идентификатор пациента не определен');
            $this->view_list();
        }
        else {
            $this->view_edit_item();
        }
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new PatientSave();
            $s->insert_data();
        } 
        else {
            $s = new PatientSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Идентификатор пациента не определен!');
            $this->view_list();
        } 
        $s = new PatientSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }

    protected function exec_delete()
    {
        $i = new PatientDelete($this->oid);
        $this->view_list();
    }

    protected function view_list()
    {
        $list = new RegisterOksList();
        self::set_title('Список пациентов с острым коронарным синдромом');
        self::set_toolbar_button('new', 'new' , 'Создать');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');        
        $this->set_content($list->get_items_page());
    }

    protected function view_new_item() 
    {
        self::set_title('Ввод нового пациента');
        $i = new PatientOks();
        $i->new_item(); 
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_item()
    {
        $i = new PatientOks($this->oid[0]);
        self::set_title('Редактирование данных пациента "' . $i->get_name() . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('apply', 'apply' , 'Применить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }

}
?>