<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'att_topic_query.php' );
require_once ( 'model' . DS . 'att_topic_save.php' );
require_once ( 'model' . DS . 'att_question_query.php' );
require_once ( 'model' . DS . 'att_question_view_query.php' );
require_once ( 'model' . DS . 'att_question_save.php' );
require_once ( 'model' . DS . 'att_answer_query.php' );

require_once ( 'views' . DS . 'att_topic_list.php' );
require_once ( 'views' . DS . 'att_topic_item.php' );
require_once ( 'views' . DS . 'att_question_list.php' );
require_once ( 'views' . DS . 'att_question_item.php' );

class AttAdmin extends ComponentACL
{
    protected $default_view = 'view_att_doc_list';
    
    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_edit()
    {
        if (!$this->oid[0]) {
            Message::error('Тема теста не определена!');
            $this->view_topic_list();
        }
        else {
            $this->view_edit_item();
        }
    }
    
    protected function exec_cancel()
    {
        $this->view_topic_list();
    }
    
    protected function exec_close_lists()
    {
        $this->view_topic_list();
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new QuizTopicSave();
            $s->insert_data();
        } 
        else {
            $s = new QuizTopicSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_topic_list();
    }
    
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

	// Вопросы тестов
    protected function exec_question_list()
    {
        $this->view_question_list();
    }
    
    protected function exec_new_question()
    {
        $this->view_new_question_item();
    }
    
    protected function exec_edit_question()
    {
        $question = (array)Request::getVar('quiz_question');
        Content::set_route('question', $question[0]);
        $this->view_edit_question_item($question[0]);
    }
    
    protected function exec_question_save()
    {
        $question = (array)Request::getVar('question');
        if (!$question[0]) {
            $s = new QuizQuestionSave();
            $s->insert_data();
        } 
        else {
            $s = new QuizQuestionSave($question[0]);
            $s->update_data();
        }
        $this->view_question_list();
    }

    protected function exec_cancel_question_edit()
    {
        $this->view_question_list();
    }

    protected function exec_download_question_file()
    {
        $this->view_upload_file();
    }
    
    protected function exec_uploaded_file_import()
    {
        $topic = Request::getVar('topic');
        if (!$topic) {
            Message::error('Не определена тема теста!');
            $this->view_question_list();
        }
        try {
            $uploaded = new ExcelQuestionUploadFileSave();
            $f = $uploaded->save_file();
            $i = new ExcelQuestionImport($f, $topic);
            $ret = $i->excel_convert();
            Message::alert('Полученный файл содержит '. $ret['q_count'] . ' вопросов и ' . $ret['a_count'] . ' ответов');
        }
        catch (UploadException $e) {
            Message::error($e->message . 'Код ошибки ' . $e->code);
        }
        //Content::set_route('topic', $topic); 
        $this->view_q_for_import_list();
    }
    
    protected function exec_cancel_import()
    {
        $this->view_question_list();
    }

    protected function exec_q_temp_list()
    {
        $this->view_q_for_import_list();
    }
    
    protected function exec_import_all()
    {
        $temp_topic = MZSession::get('temp_topic', 'question_import');
        if (!$temp_topic) {
            Message::error('Не определена тема теста!');
            $this->view_question_list();
        } else {
            $import = new QuestionImport($temp_topic);
            $ret = $import->import_all();
            //Message::alert($temp_topic);
            Message::alert('Импортировано ' .$ret['q_count'] . ' вопросов и ' . $ret['a_count'] . ' ответов');
            $this->view_question_list();
        }
    }

// Представления данных (view)
    protected function view_topic_list()
    {
        $title = 'Темы тестов';
        $confirm = 'Удаление выбранных тем';
        $list = new QuizTopicList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new' , 'Создать');
		self::set_toolbar_button('education', 'question_list' , 'Вопросы тестов');
        $edit_b = self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
        $this->set_content($list->get_items_page());
    }

    protected function view_edit_item() 
    {
        $i = new QuizTopicItem($this->oid[0]);
        self::set_title('Редактирование темы тестов');
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
        self::set_title('Ввод новой темы теста');
        $i = new QuizTopicItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

    // Работа с вопросами тестов    
   protected function view_question_list()
    {
        $title = 'Вопросы';
        $confirm = 'Удаление выбранных вопросов';
        $this->current_task = 'question_list';
        $list = new QuizQuestionList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new_question' , 'Новый вопрос');
        $edit_b = self::set_toolbar_button('edit', 'edit_question' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        self::set_toolbar_button('upload', 'download_question_file' , 'Загрузить файл');        
        $this->set_content($list->get_items_page());
    }
    
    protected function view_new_question_item() 
    {
        self::set_title('Ввод нового вопроса теста');
        $i = new QuizQuestionItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'question_save' , 'Сохранить вопрос');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_question_item($q) 
    {
        self::set_title('Редактирование вопроса теста');
        $i = new QuizQuestionItem($q);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'question_save' , 'Сохранить вопрос');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_question_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_upload_file()
    {
        self::set_title('Импорт вопросов для тестирования медработников (формат Excel)');
        $db = self::set_toolbar_button('upload', 'uploaded_file_import' , 'Загрузить');
        $db->validate(true);
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $u = new DownloadQuestionFileForm();
        $this->set_content($u->get_form());
    }
    
    protected function view_q_for_import_list()
    {
        $title = 'Полученный файл содержит следующие вопросы:';
        $list = new QuizQTempList();
        self::set_title($title);
        self::set_toolbar_button('new', 'import_all' , 'Импортировать все');
        $edit_b = self::set_toolbar_button('edit', 'import_selected' , 'Импортировать выбранные');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $this->set_content($list->get_items_page());
    }
}

?>