<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'np_association_query.php' );
require_once ( 'model' . DS . 'np_association_save.php' );
require_once ( 'model' . DS . 'expert_group_query.php' );
require_once ( 'model' . DS . 'expert_group_save.php' );

require_once ( 'views' . DS . 'np_association_list.php' );
require_once ( 'views' . DS . 'np_association_item.php' );
require_once ( 'views' . DS . 'expert_group_list.php' );
require_once ( 'views' . DS . 'expert_group_item.php' );

class AttAdmin extends ComponentACL
{
    protected $default_view = 'view_np_association_list';
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            Message::error('Тема теста не определена!');
            $this->view_topic_list();
        } 
        $s = new QuizTopicSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }
    
    protected function exec_delete()
    {
        if (!$this->oid[0]) {
            Message::error('Тема(ы) не определен(ы)!');
            $this->view_theme_list();
        } 
        $lpu = new DeleteItems($this->oid);
        $this->view_theme_list();
    }

	// Медицинские ассоциации
    protected function exec_np_association_list()
    {
        $this->view_np_association_list();
    }
    
    protected function exec_new_np_association()
    {
        Content::set_route('np_association');
        $this->view_new_np_association_item();
    }
    
    protected function exec_edit_np_association()
    {
        $assoc = (array)Request::getVar('np_association');
        Content::set_route('np_association', $assoc[0]);
        $this->view_edit_np_association_item($assoc[0]);
    }
    
    protected function exec_np_association_save()
    {
        $assoc = (array)Request::getVar('np_association');
        if (!$assoc[0]) {
            $s = new NPAssociationSave();
            $s->insert_data();
        } 
        else {
            $s = new NPAssociationSave($assoc[0]);
            $s->update_data();
        }
        $this->view_np_association_list();
    }

    protected function exec_cancel_np_association_edit()
    {
        $this->view_np_association_list();
    }

    protected function exec_expert_group_list()
    {
        $this->view_expert_group_list();
    }
// Представления данных (view)

    // Медицинские ассоциации    
   protected function view_np_association_list()
    {
        $title = 'Медицинские ассоциации';
        $confirm = 'Удаление выбранных ассоциаций';
        $this->current_task = substr( __FUNCTION__ , 5);
        $list = new NPAssociationList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new_np_association' , 'Новая ассоциация');
        $edit_b = self::set_toolbar_button('edit', 'edit_np_association' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        $this->set_content($list->get_items_page());
    }
    
    protected function view_new_np_association_item() 
    {
        self::set_title('Ввод новой медицинской ассоциации');
        $i = new NPAssociationItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'np_association_save' , 'Сохранить данные ассоциации');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_np_association_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_np_association_item($q) 
    {
        self::set_title('Редактирование ассоциации');
        $i = new NPAssociationItem($q);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'np_association_save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_np_association_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
// экспертные группы
    protected function view_expert_group_list()
    {
        $title = 'Экспертные группы';
        $confirm = 'Удаление выбранных экспертных групп';
        $this->current_task = substr( __FUNCTION__ , 5);
        $list = new ExpertGroupList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new_expert_group' , 'Новая экспертная группа');
        $edit_b = self::set_toolbar_button('edit', 'edit_expert_group' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        $this->set_content($list->get_items_page());
    }

    protected function view_new_expert_group_item() 
    {
        self::set_title('Ввод новой экспертной группы');
        $i = new ExpertGroupItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'expert_group_save' , 'Сохранить данные экспертной группы');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_expert_group_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

}

?>