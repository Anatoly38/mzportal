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
require_once ( 'model' . DS . 'assignment_query.php' );
require_once ( 'model' . DS . 'assignment_save.php' );
require_once ( 'model' . DS . 'assignment_delete.php' );
require_once ( 'views' . DS . 'assignment_list.php' );
require_once ( 'views' . DS . 'assignment_edit.php' );

class Assignments extends Component
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
            $m->enque_message('error', 'Идентификатор поручения не определен');
            $this->view_list();
        }
        else {
            $this->view_edit_item();
        }
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new AssignmentSave();
            $s->insert_data();
        } 
        else {
            $s = new AssignmentSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Идентификатор поручения не определен!');
            $this->view_list();
        } 
        $s = new AssignmentSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }

    protected function exec_delete()
    {
        $i = new AssignmentDelete($this->oid);
        $this->view_list();
    }

    protected function view_list()
    {
        $list = new AssignmentList();
        self::set_title('Список поручений');
        self::set_toolbar_button('new', 'new' , 'Создать');
        $edit_b = self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        $confirm = 'Удаление выбранных поручений';
        DeleteItems::set_confirm_dialog($confirm);        
        $this->set_content($list->get_items_page());
    }

    protected function view_new_item() 
    {
        self::set_title('Ввод нового поручения');
        $i = new AssignmentEdit();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_item()
    {
        $i = new AssignmentEdit($this->oid[0]);
        self::set_title('Редактирование данных поручения "' . $i->get_name() . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        $ab = self::set_toolbar_button('apply', 'apply' , 'Применить');
        $ab->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
}
?>