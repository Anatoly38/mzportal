<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Territory
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'territory_query.php' );
require_once ( 'model' . DS . 'territory_delete.php' );
require_once ( 'model' . DS . 'territory_save.php' );
require_once ( 'views' . DS . 'territory_list.php' );
require_once ( 'views' . DS . 'territory_item.php' );
require_once ( 'views' . DS . 'territory_subordinate.php' );
require_once ( MODULES    . DS . 'mod_user' . DS . 'acl.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'access_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'user_list.php' );

class Territory extends ComponentACL
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
            $m->enque_message('error', 'Территория не определена!');
            $this->view_list();
        }
        else {
            $this->view_item();
        }
    }

    protected function exec_save()
    {
        if (empty($this->oid[0])) {
            $s = new TerritorySave();
            $s->insert_data();
        }
        else {
            $s = new TerritorySave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }

    protected function exec_delete()
    {
        $i = new TerritoryDelete($this->oid);
        $this->view_list();
    }

    protected function exec_subordinate()
    {
        $link_type = MZConfig::$territory_lpu;
        $parent_id = Request::getVar('parent_id');
        if (!empty($parent_id)) {
            LinkObjects::set_link($parent_id, $this->oid[0], $link_type);
        }
        $this->view_subordinate();
    }

    protected function exec_subordinate_cancel()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Территория не определена!');
            $this->view_list();
        }
        else {
            $this->view_item();
        }
    }

// Управление списками доступа к териториям и подчиненным объектам

    protected function view_list()
    {
        self::set_title('Список территорий');
        self::set_toolbar_button('new', 'new' , 'Создать');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        self::set_toolbar_button('delete', 'delete' , 'Удалить');
        self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
        $list = new TerritoryList();
        $this->set_content($list->get_items_page());
    }

    protected function view_new_item()
    {
        self::set_title('Ввод новой территории');
        $i = new TerritoryItem();
        $i->new_item();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }

    protected function view_item()
    {
        $i = new TerritoryItem($this->oid[0]);
        self::set_title('Редактирование данных территории "' . $i->query->наименование . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        self::set_toolbar_button('apply', 'apply' , 'Применить');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }

    protected function view_subordinate()
    {
        $item = new TerritoryQuery($this->oid[0]);
        $list = new TerritorySubordinate($this->oid[0]);
        self::set_title('Выбор образующей территории для "' . $item->наименование . '"');
        self::set_toolbar_button('default', 'subordinate' , 'Закрыть');
        self::set_toolbar_button('cancel', 'subordinate_cancel' , 'Закрыть');
        $this->set_content($list->get_items_page());
    }

}
?>