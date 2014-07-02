<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Tasks
* @copyright	Copyright (C) 2009 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'task_query.php' );
require_once ( 'model' . DS . 'task_save.php' );
require_once ( 'views' . DS . 'task_list.php' );
require_once ( 'views' . DS . 'task_item.php' );

class Tasks extends ComponentACL
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
        $component = (array)Request::getVar('component');
        if (!$component[0]) {
            Message::error('Задача не определена!');
            $this->view_list();
        }
        else {
            $this->view_edit_item($component[0]);
        }
    }

    protected function exec_save()
    {
        if (empty($this->oid[0])) {
            $s = new TaskSave();
            $s->insert_data();
        } 
        else {
            $s = new TaskSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_current_acl()
    {
        $component = (array)Request::getVar('component');
        $this->oid[0] = $component[0];
        parent::exec_current_acl();
    }

    protected function view_list()
    {
        $list = new TaskList();
        self::set_title('Список задач');
        self::set_toolbar_button('new', 'new' , 'Создать');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');        
        $acl_b = self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
        $acl_b->set_option('obligate', true);
        $this->set_content($list->get_items_page());
    }

    protected function view_new_item() 
    {
        self::set_title('Ввод новой задачи');
        $i = new TaskItem();
        $i->new_item(); 
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_item($component)
    {
        $i = new TaskItem($component);
        self::set_title('Редактирование задачи "' . $i->query->наименование . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('apply', 'apply' , 'Применить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
}
?>