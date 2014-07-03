<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Framework
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( COMPONENTS . DS . 'component.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'access_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'user_list.php' );
require_once ( MODULES    . DS . 'mod_user' . DS .'acl.php' );


class ComponentACL extends Component
{

    protected function exec_user_list()
    {
        if (!$this->oid[0]) {
            Message::error('Объекты не определены!');
            $this->view_list();
        }
        foreach ($this->oid as $o) {
            Content::set_route('object[]', $o);
        }
        $this->view_users_list();
    }

    protected function exec_current_acl()
    {
        if (!isset($this->oid[0])) {
            Message::error('Объект не определен!');
            $this->exec_default();
        } 
        else {
            $obj = new MZObject($this->oid[0]);
            $acl_id = $obj->acl_id;
            Content::set_route('object', $this->oid[0]);
            $this->view_current_users_acl($acl_id);
        }
    }
    
    protected function exec_new_acl()
    {
        $obj_id = Request::getVar('object');
        if (!$obj_id) {
            Message::error('Объект не определен!');
            $this->view_list();
        }
        else {
            $obj = new MZObject($obj_id);
            $this->view_add_users_acl($obj);
        }
    }
    
    protected function exec_add_users_acl()
    {
        $objects        = (array)Request::getVar('object');
        $permissions    = (array)Request::getVar('permissions');
        $users_add = $this->oid;
        if (!$objects[0]) {
            Message::error('Объекты изменения списка доступа не определен!');
            $this->view_list();
            return;
        }
        if (!$this->oid[0]) {
            Message::error('Пользователи для добавления в список доступа объекта не выбраны!');
            $this->view_list();
            return;
        }
        if (!$permissions[0]) {
            Message::error('Не выбраны разрешения для добавляемых в список доступа пользователей!');
            $this->view_list();
            return;
        }
        set_time_limit(0);
        foreach ($objects as $object) {
            $o = new MZObject($object);
            $new_acl = ACL::add_user_acl($o->acl_id, $users_add, $permissions);
            $o->acl_id = $new_acl;
            $o->update();
        }
        ACL::clean();
        Message::alert('В список доступа ' . count($objects) . ' объекта/ов добавлено ' . count($users_add) . ' пользователей' );
        $d = $this->default_view;
        $this->$d();
    }
   
    protected function exec_remove_users_acl()
    {
        $objects        = (array)Request::getVar('object');
        $users_remove   = $this->oid;
        if (!$objects[0]) {
            Message::error('Объекты изменения списка доступа не определен!');
            $this->view_list();
            return;
        }
        if (!$this->oid[0]) {
            Message::error('Пользователи для добавления в список доступа объекта не выбраны!');
            $this->view_list();
            return;
        }
        set_time_limit(0);
        foreach ($objects as $object) {
            $o = new MZObject($object);
            $new_acl = ACL::remove_user_acl($o->acl_id, $users_remove);
            $o->acl_id = $new_acl;
            $o->update();
        }
        ACL::clean();
        Message::alert('Из списка доступа ' . count($objects) . ' объекта/ов удалено ' . count($users_remove) . ' пользователей' );
        $d = $this->default_view;
        $this->$d();
    }

    protected function exec_change_user_rights()
    {
    
    }
    
    protected function exec_acl_cancel()
    {
        $this->exec_default();
    }

    protected function view_current_users_acl($acl_id)
    {
        self::set_title('Текущий список пользователей, имеющих доступ к объекту');
        $list = new UserList();
        $list->set_limit(0);
        $list->include_users_by_acl($acl_id);
        $this->set_content($list->get_items_page());        
        self::set_toolbar_button('new', 'new_acl' , 'Добавить');
        $edit_b = self::set_toolbar_button('edit', 'change_user_rights' , 'Изменить');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('delete', 'remove_users_acl' , 'Удалить');
        self::set_toolbar_button('cancel', 'acl_cancel' , 'Закрыть');
    }
    
    protected function view_add_users_acl($obj)
    {
        $list = new UserList();
        $list->set_limit(0);
        Content::set_route('object', $obj->obj_id);
        $list->exlude_users_by_acl($obj->acl_id);
        $this->set_content($list->get_items_page());
        $add = "<p><b>Изменить разрешения</b></p>";
        $add .= '<p><input type="checkbox" name="permissions[]" value="1" />Полный доступ</p>'; 
        $add .= '<p><input type="checkbox" name="permissions[]" value="20" />Удаление</p>'; 
        $add .= '<p><input type="checkbox" name="permissions[]" value="30" />Изменение/редактирование</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="40" />Чтение</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="50" />Создание/Добавление</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="60" />Изменение доступа</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="100" />Изменения статуса отчетного документа:  любые</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="101" />Изменения статуса отчетного документа:  редактор</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="102" />Изменения статуса отчетного документа:  куратор</p>';
        $this->set_content($add);
        self::set_toolbar_button('save', 'add_users_acl' , 'Сохранить список');
        self::set_toolbar_button('cancel', 'acl_cancel' , 'Закрыть');
    }
    
    protected function view_users_list()
    {
        $list = new UserList();
        $list->set_limit(0);
        self::set_title('Доваление/удаление пользователей из списка доступа');
        $this->set_content($list->get_items_page());
        $add  = '<p><b>Разрешения</b></p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="1" />Полный доступ</p>'; 
        $add .= '<p><input type="checkbox" name="permissions[]" value="20" />Удаление</p>'; 
        $add .= '<p><input type="checkbox" name="permissions[]" value="30" />Изменение/редактирование</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="40" />Чтение</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="50" />Создание/Добавление</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="60" />Изменение доступа</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="100" />Изменения статуса отчетного документа:  любые</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="101" />Изменения статуса отчетного документа:  редактор</p>';
        $add .= '<p><input type="checkbox" name="permissions[]" value="102" />Изменения статуса отчетного документа:  куратор</p>';
        $this->set_content($add);
        self::set_toolbar_button('new', 'add_users_acl' , 'Добавить');
        self::set_toolbar_button('delete', 'remove_users_acl' , 'Удалить');
        self::set_toolbar_button('cancel', 'acl_cancel' , 'Закрыть');
    }
}

?>