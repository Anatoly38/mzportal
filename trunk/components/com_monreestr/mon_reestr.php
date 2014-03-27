<?php
/**
* @version		$Id: mon_reestr.php,v 1.0 2011/08/28 11:45:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'mon_reestr_query.php' );
require_once ( 'model' . DS . 'mon_save.php' );
require_once ( 'views' . DS . 'mon_list.php' );
require_once ( 'views' . DS . 'mon_item.php' );
require_once ( 'views' . DS . 'link_pattern_list.php' );
require_once ( MODULES . DS . 'mod_user'  . DS . 'acl.php' );
require_once ( MODULES . DS . 'mod_excel' . DS . 'excel_export.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'access_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'user_list.php' );

class MonReestr extends ComponentACL
{
    
    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_edit()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Мониторинг не определен!');
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
    
    protected function exec_close_lists()
    {
        $this->view_list();
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new MonSave();
            $s->insert_data();
        } 
        else {
            $s = new MonSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Мониторинг не определен!');
            $this->view_list();
        } 
        $s = new MonSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }
    
    protected function exec_delete()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Мониторинг(и) не определен(ы)!');
            $this->view_list();
        } 
        $lpu = new DeleteItems($this->oid);
        $this->view_list();
    }
    
    protected function exec_link_pattern_prompt()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Мониторинг(и) не определен(ы)!');
            $this->view_list();
        } 
        Content::set_route('mon', $this->oid[0]);
        $this->view_link_template_prompt();
    }
    
    protected function exec_link_pattern()
    {
        $patterns   = Request::getVar('pattern');
        $mon        = Request::getVar('mon');
        $mon_link   = Reference::get_id('мониторинг_шаблон', 'link_types');
        $m = Message::getInstance();
        if (!$patterns[0]) {
            $m->enque_message('error', 'Шаблоны для добавления не определены!');
        }
        else {
            $set_rights = false;
            $i = 0;
            foreach ($patterns AS $p) {
                LinkObjects::set_link($mon, $p, $mon_link);
                $i++;
            }
            $m->enque_message('alert', 'Добавлено ' . $i . ' шаблонов');
        }
        $this->view_list();
    }
    
    protected function exec_unlink_pattern_prompt()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Мониторинг(и) не определен(ы)!');
            $this->view_list();
        } 
        Content::set_route('mon', $this->oid[0]);
        $this->view_unlink_template_prompt($this->oid[0]);
    }

    protected function exec_unlink_pattern()
    {
        $patterns   = Request::getVar('pattern');
        $mon        = Request::getVar('mon');
        $mon_link   = Reference::get_id('мониторинг_шаблон', 'link_types');
        $m = Message::getInstance();
        if (!$patterns[0]) {
            $m->enque_message('error', 'Шаблоны для исключения не определены!');
        }
        else {
            $i = 0;
            foreach ($patterns AS $p) {
                LinkObjects::unset_link($mon, $p, $mon_link);
                $i++;
            }
            $m->enque_message('alert', 'Исключено ' . $i . ' шаблонов');
        }
        $this->view_list();
    }

    
    protected function exec_current_acl()
    {
        $this->set_route('mon[]', $this->oid[0]);
        parent::exec_current_acl($this->oid[0]);
    }
    
    protected function exec_add_user_acl()
    {
        $mon = Request::getVar('mon');
        parent::exec_add_user_acl($mon[0]);
    }

    protected function exec_set_new_acl()
    {
        $mon = Request::getVar('mon');
        parent::exec_set_new_acl($mon);
    }

// Представления данных (view)
    protected function view_list()
    {
        $title = 'Реестр мониторингов';
        $confirm = 'Удаление выбранных мониторингов';
        $list = new MonList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new' , 'Создать');
        $edit_b = self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        $link_b = self::set_toolbar_button('publish', 'link_pattern_prompt' , 'Добавить шаблоны');
        $link_b->set_option('obligate', true);
        $unlink_b = self::set_toolbar_button('unpublish', 'unlink_pattern_prompt' , 'Удалить шаблоны');
        $unlink_b->set_option('obligate', true);
        self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
        $this->set_content($list->get_items_page());
    }

    protected function view_edit_item() 
    {
        $i = new MonItem($this->oid[0]);
        self::set_title('Редактирование данных мониторинга');
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
    
    protected function view_new_item() 
    {
        self::set_title('Ввод нового мониторинга');
        $i = new MonItem();
        $i->new_item(); 
        self::set_toolbar_button('save', 'save' , 'Сохранить');
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_link_template_prompt()
    {
        self::set_title("Выберите доступные шаблоны документов для данного мониторинга");
        $list = new LinkPatternList();
        self::set_toolbar_button('save', 'link_pattern' , 'Сохранить список');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_unlink_template_prompt($mon)
    {
        self::set_title("Выберите шаблоны исключаемые из данного мониторинга");
        $list = new LinkPatternList($mon);
        self::set_toolbar_button('save', 'unlink_pattern' , 'Сохранить изменения');
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }

    protected function view_add_acl($obj)
    {
        $this->set_route('lpu[]', $obj->obj_id);
        parent::view_add_acl($obj);
    }
}

?>