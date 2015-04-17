<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

require_once ( MZPATH_BASE .DS.'components'.DS.'com_quiz' .DS.'model'.DS.'quiz_ticket_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'com_quiz' .DS.'model'.DS.'quiz_ticket_save.php' );
require_once ( 'model' . DS . 'dossier_query.php' );
require_once ( 'model' . DS . 'dossier_cab_query.php' );
require_once ( 'model' . DS . 'dossier_save.php' );
require_once ( 'model' . DS . 'attest_cab_user_query.php' );
require_once ( 'model' . DS . 'attest_cab_user_save.php' );
require_once ( 'model' . DS . 'attest_ticket_save.php' );

require_once ( 'model' . DS . 'np_association_query.php' );
require_once ( 'model' . DS . 'np_association_save.php' );
require_once ( 'model' . DS . 'expert_group_query.php' );
require_once ( 'model' . DS . 'expert_group_save.php' );

require_once ( 'views' . DS . 'dossier_list.php' );
require_once ( 'views' . DS . 'dossier_ticket_list.php' );
require_once ( 'views' . DS . 'dossier_item.php' );
require_once ( 'views' . DS . 'dossier_profile.php' );
require_once ( 'views' . DS . 'ticket_item.php' );
require_once ( 'views' . DS . 'ticket_items.php' );
require_once ( 'views' . DS . 'attest_cab_user_item.php' );

require_once ( 'views' . DS . 'np_association_list.php' );
require_once ( 'views' . DS . 'np_association_item.php' );
require_once ( 'views' . DS . 'expert_group_list.php' );
require_once ( 'views' . DS . 'expert_group_item.php' );

class AttAdmin extends Component
{
    protected $default_view = 'view_dossier_list';
    
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
        if ( !isset($this->oid[0]) ) {
            Message::error('Объект(ы) для удаления не определен(ы)!');
        } 
        else {
            $lpu = new DeleteItems($this->oid);
        }
        $this->exec_default();
    }

    // Аттестационные дела
    protected function exec_dossier_list()
    {
        $this->view_dossier_list();
    }
    
    protected function exec_new_dossier()
    {
        Content::set_route('dossier');
        $this->view_dossier_item();
    }
    
    protected function exec_edit_dossier()
    {
        $dossier = (array)Request::getVar('dossier');
        Content::set_route('dossier', $dossier[0]);
        $this->view_edit_dossier_item($dossier[0]);
    }
    
    protected function exec_dossier_save()
    {
        $dossier = (array)Request::getVar('dossier');
        if (!$dossier[0]) {
            $s = new DossierSave();
            //$s->insert_data();
        } 
        else {
            $s = new DossierSave($dossier[0]);
            //$s->update_data();
        }
        $s->save();
        $this->view_dossier_list();
    }
    
    protected function exec_cancel_dossier_edit()
    {
        $this->view_dossier_list();
    }    
    
    protected function exec_dossier_delete()
    {
        $dossier = (array)Request::getVar('dossier');
        if (!$dossier[0]) {
            Message::error('Аттестационные дела не определен(ы)!');
            $this->view_dossier_list();
        } 
        $qd = new DeleteItems($dossier);
        $this->view_dossier_list();
    }
    
    protected function exec_edit_attest_cab_user()
    {
        $dossier = (array)Request::getVar('dossier');
        if ( count($dossier) > 1 ) {
            Message::alert("Выделите только одно дело из списка для редактирования");
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier[0]);
        try {
            $d = new DossierCabQuery($dossier[0]);
            Content::set_route('cab_user', $d->uid);
            $this->view_attest_cab_user_item($d->uid);
        }
        catch (Exception $e) {
            Message::error('Логин и пароль для этого аттестационного дела еще не созданы, введите новые');
            Content::set_route('cab_user');
            $this->view_attest_cab_user_item();
        }
    }
    
    protected function exec_attest_cab_user_save()
    {
        $dossier  = (array)Request::getVar('dossier');
        $cab_user = (array)Request::getVar('cab_user');
        $s = new AttestCabUserSave($cab_user[0]);
        $id = $s->save();
        if (!$cab_user[0]) {
            $link_type = Reference::get_id('аттестационное_дело-пользователь', 'link_types');
            $s->set_left_obj($dossier[0]);
            $s->set_right_obj($id);
            $s->set_association($link_type);
        }
        $this->view_dossier_list();
    }
    
    protected function exec_cancel_attest_cab_user_edit()
    {
        $this->view_dossier_list();
    }

    protected function exec_quiz_tickets()
    {
        $dossier = (array)Request::getVar('dossier');
        if ( count($dossier) > 1 ) {
            Message::alert("Выделите только одно дело из списка для редактирования");
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier[0]);
        $this->view_dossier_ticket_list($dossier[0]);
    }
    
    protected function exec_new_ticket()
    {
        $dossier = (array)Request::getVar('dossier');
        if (!$dossier[0]) {
            Message::error('Аттестационное дело не определено');
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier[0]);
        $this->view_new_ticket_items();
    }
    
    protected function exec_ticket_edit()
    {
        $dossier = (array)Request::getVar('dossier');
        $ticket = (array)Request::getVar('ticket');
        if (!$dossier[0]) {
            Message::error('Аттестационное дело не определено');
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier[0]);
        if (!$ticket[0]) {
            Message::error('Не определена попытка тестирования для редактирования');
            $this->view_dossier_ticket_list($dossier[0]);
        }
        Content::set_route('ticket', $ticket[0]);
        $this->view_ticket_item($ticket[0]);
    }
    
    protected function exec_tickets_insert()
    {
        $dossier    = Request::getVar('dossier');
        if (!$dossier) {
            Message::error('Аттестационное дело не определено');
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier);
        $ticket_qount = (int)Request::getVar('ticket_count');
        $main_topic = Request::getVar('main_topic');
        $setting    = Request::getVar('setting');
        $link_type = Reference::get_id('аттестационное_дело-тикет', 'link_types');
        for ($i = 0; $i < $ticket_qount; $i++) {
            $t = new AttestTicketSave();
            $t->тема        = $main_topic;
            $t->настройка   = $setting;
            $t->в_процессе  = false;
            $t->реализована = false;
            $t->save();
            $t->set_left_obj($dossier);
            $t->set_right_obj($t->get_item());
            $t->set_association($link_type);
        }
        $this->view_dossier_ticket_list($dossier);
    }
    
    protected function exec_ticket_save()
    {
        $dossier = Request::getVar('dossier');
        $ticket = Request::getVar('ticket');
        if (!$dossier || !$ticket) {
            Message::error('Не определено аттестационное дело и/или попытка тестирования для редактирования');
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier);
        $t = new QuizTicketSave($ticket);
        $t->save();
        $this->view_dossier_ticket_list($dossier);
    }
    
    protected function exec_ticket_delete()
    {
        $dossier = Request::getVar('dossier');
        $ticket = (array)Request::getVar('ticket');
        if (!$dossier || !$ticket[0]) {
            Message::error('Не определено аттестационное дело и/или попытка тестирования для удаления');
            $this->view_dossier_list();
        }
        Content::set_route('dossier', $dossier);
        $qd = new DeleteItems($ticket);
        $this->view_dossier_ticket_list($dossier);
    }
    
    protected function exec_cancel_tickets_edit()
    {
        $this->view_dossier_list();
    }
    
    protected function exec_print_dossier_profile()
    {
        $dossier = explode(',', Request::getVar('dossier'));
        $this->view_dossier_profile($dossier[0]);
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
            //$s->insert_data();
        } 
        else {
            $s = new NPAssociationSave($assoc[0]);
           // $s->update_data();
        }
        $s->save();
        $this->view_np_association_list();
    }

    protected function exec_cancel_np_association_edit()
    {
        $this->view_np_association_list();
    }

// экспертные группы  
    protected function exec_expert_group_list()
    {
        $this->view_expert_group_list();
    }
    
    protected function exec_new_expert_group()
    {
        Content::set_route('expert_group');
        $this->view_new_expert_group_item();
    }
    
    protected function exec_edit_expert_group()
    {
        $eg = (array)Request::getVar('expert_group');
        Content::set_route('expert_group', $eg[0]);
        $this->view_edit_expert_group_item($eg[0]);
    }
    
    protected function exec_expert_group_save()
    {
        $eg = (array)Request::getVar('expert_group');
        if (!$eg[0]) {
            $s = new ExpertGroupSave();
            $s->insert_data();
        } 
        else {
            $s = new ExpertGroupSave($eg[0]);
            $s->update_data();
        }
        $this->view_expert_group_list();
    }
    
    protected function exec_cancel_expert_group_edit()
    {
        $this->view_expert_group_list();
    }
    
// Представления данных (view)

    // Аттестационные дела    
   protected function view_dossier_list()
    {
        $title = 'Аттестационные дела';
        $confirm = 'Удаление выбранных аттестационных дел';
        $this->current_task = substr( __FUNCTION__ , 5);
        $list = new DossierList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new_dossier' , 'Новое аттестационное дело');
        $edit_b = self::set_toolbar_button('edit', 'edit_dossier' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $user_b = self::set_toolbar_button('user', 'edit_attest_cab_user' , 'Доступ в личный кабинет');
        $user_b->set_option('obligate', true);
        $ticket_b = self::set_toolbar_button('quiz', 'quiz_tickets' , 'Попытки тестирования');
        $ticket_b->set_option('obligate', true);
        $pb = self::set_toolbar_button('print', 'print_dossier_profile' , 'Распечатать профиль');
        $pb->set_option('obligate', true);
        $js_func = 
<<<JS
function () { 
    objects = "dossier=";
    $(".grid_row.ui-state-highlight").each( function () {
            objects += $(this).attr("id") + ',';
        }
    );
    window.open('print.php?app={$this->app}&task=print_dossier_profile&' + objects); 
}
JS;
        $pb->set_option('action', $js_func);
        $del_b = self::set_toolbar_button('delete', 'dossier_delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        $del_b->set_option('confirmDelete', true);
        //DeleteItems::set_confirm_dialog($confirm);
        $this->set_content($list->get_items_page());
    }
    
    protected function view_dossier_item() 
    {
        Page_Title::set('Ввод нового аттестационного дела');
        $i = new DossierItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'dossier_save' , 'Сохранить данные аттестационного дела');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_dossier_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_dossier_item($d) 
    {
        self::set_title('Редактирование аттестационного дела');
        $i = new DossierItem($d);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'dossier_save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_dossier_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

    private function _gen_pwd()
    {
        $js = Javascript::getInstance();
        $js->add_js_link('jquery.pGenerator.js');
        $code = 
<<<JS
$("#pvdGen").pGenerator({
        'bind': 'click',
        'passwordElement': '#pwd',
        'displayElement': null,
        'passwordLength': 6,
        'uppercase': true,
        'lowercase': true,
        'numbers':   true,
        'specialChars': false
});
JS;
        $js->add_jblock($code);
    }
    
    protected function view_attest_cab_user_item($u = null) 
    {
        self::set_title('Ввод логина и пароля для пользователя личного кабинета аттестационной комиссии');
        $i = new AttestCabUserItem($u);
        if (!$u) {
            $i->new_item();
        } 
        else {
            $i->edit_item();
        }
        $sb = self::set_toolbar_button('save', 'attest_cab_user_save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_attest_cab_user_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $this->_gen_pwd();
        $form = $i->get_form();
        $this->set_content($form);
    }

   protected function view_dossier_ticket_list($d)
    {
        $title = 'Предоставленные попытки тестирования ';
        $confirm = 'Удаление выбранных попыток тестирования';
        $this->current_task = substr( __FUNCTION__ , 5);
        $list = new DossierTicketList($d);
        self::set_title($title);
        self::set_toolbar_button('new', 'new_ticket' , 'Предоставить новые попытки');
        $edit_b = self::set_toolbar_button('edit', 'ticket_edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'ticket_delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        $del_b->set_option('confirmDelete', true);
        $cb = self::set_toolbar_button('cancel', 'cancel_tickets_edit' , 'Закрыть');
        $this->set_content($list->get_items_page());
    }
    
    protected function view_new_ticket_items()
    {
        Page_Title::set('Ввод новых тикетов (попыток) тестирования');
        $i = new TicketItems(); 
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'tickets_insert' , 'Сохранить данные о попытках тестирования');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_tickets_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_ticket_item($t) 
    {
        self::set_title('Редактирование параметров попытки тестирования');
        $i = new TicketItem($t);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'ticket_save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_tickets_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }  
  
    protected function view_dossier_profile($d) 
    {
        $p = new DossierProfile($d);
        $p->show_title("Профиль аттестационного дела");
        $p->show_dossier();
        $p->show_title("Доступ в личный кабинет");
        $p->show_cab_user();
        $p->show_title("Прохождение тестов");
        $p->show_quiz_attempts();
        $this->set_content($p->get_text());
    }  
    
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
    
    protected function view_edit_np_association_item($na) 
    {
        self::set_title('Редактирование ассоциации');
        $i = new NPAssociationItem($na);
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
    
    protected function view_edit_expert_group_item($eg) 
    {
        self::set_title('Редактирование экспертной группы');
        $i = new ExpertGroupItem($eg);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'expert_group_save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_expert_group_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

}

?>