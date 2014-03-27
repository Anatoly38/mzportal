<?php
/**
* @version		$Id: users.php,v 1.10 2010/04/15 11:13:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Users
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
require_once ( 'model' . DS . 'user_query.php' );
require_once ( 'model' . DS . 'group_query.php' );
require_once ( 'model' . DS . 'user_group_query.php' );
require_once ( 'model' . DS . 'user_save.php' );
require_once ( 'model' . DS . 'group_save.php' );
require_once ( 'model' . DS . 'user_delete.php' );
require_once ( 'model' . DS . 'member_delete.php' );
require_once ( 'model' . DS . 'group_member_set.php' );
require_once ( 'model' . DS . 'acl_save.php' );
require_once ( 'views' . DS . 'user_item.php' );
require_once ( 'views' . DS . 'group_item.php' );
require_once ( 'views' . DS . 'user_list.php' );
require_once ( 'views' . DS . 'group_list.php' );
require_once ( 'views' . DS . 'member_list.php' );
require_once ( 'views' . DS . 'member_add.php' );
require_once ( 'views' . DS . 'acl_set.php' );

class Users extends Component
{
    protected $default_view = 'view_user_list';

    protected function exec_user_new()
    {
        $this->view_new_user();
    }
    
    protected function exec_group_new()
    {
        $this->view_new_group();
    }
    
    protected function exec_cancel()
    {
        $this->view_user_list();
    }
    
    protected function exec_cancel_group()
    {
        $this->view_group_list();
    }
    
    protected function exec_users()
    {
        $this->view_user_list();
    }
    
    protected function exec_groups()
    {
        $this->view_group_list();
    }
    
    protected function exec_members()
    {
        $this->view_member_list();
    }

    protected function exec_edit_user()
    {
        if (!$this->oid[0]) {
            Message::error('Пользователь не определен!');
            $this->view_user_list();
        }
        else {
            $this->view_edit_user();
        }
    }
    
    protected function exec_edit_group()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Группа пользователей не определена!');
            $this->view_user_list();
        }
        else {
            $this->view_edit_group();
        }
    }

    protected function exec_save_user()
    {
        if (empty($this->oid[0])) {
            $s = new UserSave();
            $s->insert_data();
        } 
        else {
            $s = new UserSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_user_list();
    }
    
    protected function exec_save_group()
    {
        if (empty($this->oid[0])) {
            $s = new GroupSave();
            $s->insert_data();
        } 
        else {
            $s = new GroupSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_group_list();
    }
    
    protected function exec_set_group_members()
    {
        $uid = Request::getVar('uid');
        $s = new GroupMemberSet($uid);
        $this->view_member_list();    
    }
    
    protected function exec_set_acl()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Группа/Пользователь не определен!');
            $this->view_user_list();
        }
        else {
            $this->view_acl_set();
        }
    }
    
    protected function exec_save_acl()
    {
        if (!$this->oid[0]) {
            Message::error('Группа/Пользователь не определен!');
            $this->view_user_list();
        }
        $acl = new ACLSave($this->oid[0]);
    }
    
    protected function exec_delete()
    {
        $i = new UserDelete($this->oid);
        $this->view_user_list();    
    }

    protected function exec_member_add()
    {
        $this->view_member_add();        
    }
    
    protected function exec_member_delete()
    {
        $uid = Request::getVar('uid');
        $i = new MemberDelete($uid);
        $this->view_member_list();    
    }
    
    protected function view_user_list()
    {
        $list = new UserList();
        self::set_title('Пользователи');
        self::set_toolbar_button('edit', 'edit_user' , 'Редактировать', 'Выберите пользователя для редактирования');
        self::set_toolbar_button('switch', 'set_acl' , 'Доступ к приложениям');
        $this->set_default_toolbar();
        $this->set_content($list->get_items_page());
    }
    
    protected function view_group_list()
    {
        $list = new GroupList();
        self::set_title('Группы пользователей');
        $this->set_default_toolbar();
        self::set_toolbar_button('edit', 'edit_group' , 'Редактировать', 'Выберите группу для редактирования');
        $this->set_content($list->get_items_page());
    }
    
    protected function view_member_list()
    {
        $list = new MemberList();
        self::set_title("Пользователи - члены группы \"{$list->get_group_name()}\"");
        self::set_toolbar_button('adduser', 'member_add', 'Добавить пользователя');
        self::set_toolbar_button('delete', 'member_delete', 'Удалить пользователя', 'Выберите пользователей для удаления из группы');
        self::set_toolbar_button('cancel', 'cancel_group' , 'Закрыть');        
        $this->set_content($list->get_items_page());
    }
    
    protected function view_member_add()
    {
        $list = new MemberAdd();
        self::set_title("Добавление пользователей в группу \"{$list->get_group_name()}\"");
        self::set_toolbar_button('save', 'set_group_members' , 'Сохранить изменения');
        self::set_toolbar_button('cancel', 'cancel_group' , 'Закрыть');        
        $this->set_content($list->get_items_page());
    }

    
    private function set_default_toolbar()
    {
        self::set_toolbar_button('user', 'users', 'Список пользователей');
        self::set_toolbar_button('adduser', 'user_new', 'Новый пользователь');
        self::set_toolbar_button('groups', 'groups' , 'Группы пользователей');
        self::set_toolbar_button('addgroup', 'group_new' , 'Создать группу');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');
    }

    protected function view_new_user() 
    {
        self::set_title('Ввод нового пользователя');
        $i = new UserItem();
        $i->new_item(); 
        self::set_toolbar_button('save', 'save_user' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_group()
    {
        self::set_title('Ввод новой группы пользователей');
        $i = new GroupItem();
        $i->new_item(); 
        self::set_toolbar_button('save', 'save_group' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel_group' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_user()
    {
        $i = new UserItem($this->oid[0]);
        self::set_title('Пользователь "' . $i->query->name . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'save_user' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
        
    }
    
    protected function view_edit_group()
    {
        $i = new GroupItem($this->oid[0]);
        self::set_title('Группа пользователей "' . $i->query->name . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'save_group' , 'Сохранить');
        self::set_toolbar_button('switch', 'set_acl' , 'Доступ');
        self::set_toolbar_button('user', 'members', 'Члены группы');
        self::set_toolbar_button('cancel', 'cancel_group' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_acl_set()
    {
        self::set_title('Редактирование прав доступа Пользователя/Группы "' . $this->oid[0] . '"');
        $c = new ACLSet($this->oid[0]);
        self::set_toolbar_button('apply', 'save_acl' , 'Установить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $this->set_content($c->get_content());
    }
}
?>