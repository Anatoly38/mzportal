<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'quiz_topic_query.php' );
require_once ( 'model' . DS . 'quiz_topic_save.php' );
require_once ( 'model' . DS . 'quiz_topic_qcount_query.php' );
require_once ( 'model' . DS . 'quiz_question_query.php' );
require_once ( 'model' . DS . 'quiz_question_view_query.php' );
require_once ( 'model' . DS . 'quiz_question_save.php' );

require_once ( 'model' . DS . 'quiz_setting_query.php' );
require_once ( 'model' . DS . 'quiz_setting_save.php' );

require_once ( 'model' . DS . 'quiz_answer_query.php' );
require_once ( 'model' . DS . 'quiz_answer_save.php' );

require_once ( 'model' . DS . 'excel_question_upload_file_save.php' );
require_once ( 'model' . DS . 'excel_question_import.php' );
require_once ( 'model' . DS . 'question_import.php' );

require_once ( 'views' . DS . 'quiz_topic_list.php' );
require_once ( 'views' . DS . 'quiz_topic_item.php' );
require_once ( 'views' . DS . 'quiz_question_list.php' );
require_once ( 'views' . DS . 'quiz_question_item.php' );

require_once ( 'views' . DS . 'quiz_setting_list.php' );
require_once ( 'views' . DS . 'quiz_setting_item.php' );

require_once ( 'views' . DS . 'quiz_answer_list.php' );
require_once ( 'views' . DS . 'quiz_answer_item.php' );

require_once ( 'views' . DS . 'quiz_result_list.php' );
require_once ( 'views' . DS . 'download_question_file_form.php' );
require_once ( 'views' . DS . 'quiz_q_temp_list.php' );
require_once ( 'views' . DS . 'trial_testing_selection_form.php' );
require_once ( 'views' . DS . 'trial_quiz.php' );

require_once ( MODULES . DS . 'mod_user'  . DS . 'acl.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'access_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'user_list.php' );

class Quiz extends ComponentACL
{
    protected $default_view = 'view_topic_list';
    
// темы тестирования
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
    
    protected function exec_topic_list()
    {
        $this->view_topic_list();
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
            if ($s->insert_data()) {
                Message::alert('Данные по новой теме теста сохранены');
            };
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
            $this->view_topic_list();
        } 
        $lpu = new DeleteItems($this->oid);
        $this->view_topic_list();
    }

// Вопросы тестов
    protected function exec_question_list()
    {
        $this->view_question_list();
    }
    
    protected function exec_new_question()
    {
        Content::set_route('question');
        $this->view_new_question_item();
    }
    
    protected function exec_edit_question()
    {
        $question = (array)Request::getVar('quiz_question');
        Content::set_route('question', $question[0]);
        Content::set_route('updated_answers');
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
    
    protected function exec_delete_question()
    {
        $question = (array)Request::getVar('quiz_question');
        if (!$question[0]) {
            Message::error('Вопрос(ы) не определен(ы)!');
            $this->view_question_list();
        } 
        $qd = new DeleteItems($question);
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
            Message::alert('Импортировано ' .$ret['q_count'] . ' вопросов и ' . $ret['a_count'] . ' ответов по теме "' . Reference::get_name($temp_topic, 'quiz_topics') . '"' );
            $this->view_question_list();
        }
    }

    // Настройки тестирования
    protected function exec_settings_list()
    {
        $this->view_settings_list();
    }
    
    protected function exec_setting_new()
    {
        Content::set_route('quiz_setting');
        $this->view_new_setting_item();
    }
    
    protected function exec_setting_edit()
    {
        $quiz_setting = (array)Request::getVar('quiz_setting');
        Content::set_route('quiz_setting', $quiz_setting[0]);
        $this->view_edit_setting_item($quiz_setting[0]);
    }
    
    protected function exec_setting_save()
    {
        $quiz_setting = (array)Request::getVar('quiz_setting');
        if (!$quiz_setting[0]) {
            $s = new QuizSettingSave();
            $s->insert_data();
        } 
        else {
            $s = new QuizSettingSave($quiz_setting[0]);
            $s->update_data();
        }
        $this->view_settings_list();
    }
    
    protected function exec_setting_delete()
    {
        $setting = (array)Request::getVar('quiz_setting');
        if (!$setting[0]) {
            Message::error('Вопрос(ы) не определен(ы)!');
            $this->view_settings_list();
        } 
        $qd = new DeleteItems($setting);
        $this->view_settings_list();
    }

    protected function exec_setting_cancel_edit()
    {
        $this->view_settings_list();
    }

// Работа с ответами

    protected function exec_edit_answer()
    {
        $answer = (array)Request::getVar('quiz_answer');
        $question = Request::getVar('question');
        Content::set_route('question', $question);
        Content::set_route('answer', $answer[0]);
        $this->view_edit_answer_item($answer[0]);
    }
    
    protected function exec_save_answer()
    {
        $answer = Request::getVar('answer');
        $question = Request::getVar('question');    
        if (!$answer) {
            $s = new QuizAnswerSave();
            $s->insert_data();
        } 
        else {
            $s = new QuizAnswerSave($answer);
            $s->update_data();
        }
        Content::set_route('question', $question);
        $this->view_edit_question_item($question);
    }
    
    protected function exec_cancel_edit_answer()
    {
        $question = Request::getVar('question');  
        Content::set_route('question', $question);        
        $this->view_edit_question_item($question);
    }
    
    protected function exec_corranswer_asinc_save()
    {
        $answers = Request::getVar('answers');
        if (is_array($answers) && count($answers) < 1) {
            echo 'Нет данных для сохранения';
            exit;
        }
        $json_decoded =json_decode($answers, true);
        $i = 0;
        
        foreach($json_decoded as $answer) {
            $a = new QuizAnswerQuery($answer);
            $a->правильный ? $a->правильный = 0 : $a->правильный = 1;
            $a->update();
            $i++;
        } 
        //echo 'Изменено вариантов ответа: ' . $i ;
        print_r($json_decoded);
    }
    
    protected function exec_set_correct_answer()
    {
        $u = Request::getVar('updated_answers');
        $q = Request::getVar('question');
        Content::set_route('question', $q);
        Content::set_route('updated_answers');
        Message::alert($u);
        $this->view_edit_question_item($q);
    }

// Пробное тестирование

    protected function exec_trial_testing_selection()
    {
        $this->view_trial_testing_selection();
    }    
    
    protected function exec_cancel_trial_test()
    {
        $this->view_topic_list();
    }
    
    protected function exec_start_trial_test()
    {
        $topic = Request::getVar('topic');
        if (!$topic) {
            Message::error('Не определена основная тема теста');
            $this->view_topic_list();
        } 
        $setting        = Request::getVar('setting');
        $q_count        = Request::getVar('q_count');
        $duration       = Request::getVar('duration');
        $q_order        = Request::getVar('q_order');
        $show_answers   = Request::getVar('show_answers');
        try {
            $q = new TrialQuiz($topic);
            if ($setting) {
                $q->set_settings($setting);
            }        
            if ($q_count) {
                $q->set_qcount($q_count);
            }
            if ($duration) {
                $q->set_duration($duration);
            }
            $q->show_ordered($q_order);
            $q->show_correct_answers($show_answers);
            Content::set_route('source', '');
            $q->start_quiz();      
        }
        catch (Exception $e) {
            Message::error($e);
        }
        $this->view_trial_testing($topic);
    }
    
    protected function exec_save_result()
    {
        print_r($_POST);
        $b = $_POST['begined']/1000;
        $e = $_POST['ended']/1000;
        echo "Начало теста " . date('Y-m-d H:i:s', $b);
        echo "Окончание теста " . date('Y-m-d H:i:s', $e);
    }
    
    protected function exec_get_question()
    {
        $questions = json_decode($_POST['ids']);
        $q_texts = array();
        foreach ($questions as $q) {
            $q_obj = new QuizQuestionQuery($q);
            $q_texts[] = $q_obj->текст_вопроса;
        }
        //$ret = implode(',' , $q_texts);
        echo json_encode($q_texts);
        //echo $ret;
    }
    
// Результаты тестирования    
    
    protected function exec_result_list()
    {
        $this->view_result_list();
    }

// Представления данных (view) ****************************************************
    protected function view_topic_list()
    {
        $title = 'Темы тестов';
        $confirm = 'Удаление выбранных тем';
        $this->current_task = 'topic_list';
        $list = new QuizTopicList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new' , 'Создать');
        $edit_b = self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        //self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
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
        $del_b = self::set_toolbar_button('delete', 'delete_question' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm, 'delete_question');
        //self::set_toolbar_button('upload', 'download_question_file' , 'Загрузить файл');        
        $this->set_content($list->get_items_page());
    }
    
    protected function view_new_question_item() 
    {
        self::set_title('Ввод нового вопроса теста');
        $i = new QuizQuestionItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'question_save' , 'Сохранить вопрос');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_question_edit', 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_question_item($q) 
    {
        self::set_title('Редактирование вопроса теста');
        $i = new QuizQuestionItem($q);
        $i->edit_item(); 
        $i->get_answers();
        $sb = self::set_toolbar_button('save', 'question_save' , 'Сохранить вопрос');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_question_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $ea = self::set_toolbar_button('edit', 'edit_answer' , 'Редактировать ответ');
        $ea->track_dirty(true);
        $ea->set_option('obligate', true);
        $correct = self::set_toolbar_button('check', 'set_correct_answer' , 'Установить/Снять правильный ответ');
        $correct->set_option( 'action', $this->_set_correct_answer_js() );
        $correct->set_option('obligate', true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    private function _set_correct_answer_js() 
    {
        $code = 
<<<JS
function(){
    var collate =[];
    i = 0;
    $(".grid_row.ui-state-highlight").each(function() {
        collate.push($(this).attr("id"));
    });
    q_count = collate.length;
    output = '[' + collate.join(",") + ']';
    $.ajax(
        {
            type: 'POST',
            url: 'asinc.php?app=54&task=corranswer_asinc_save',
            data: { answers: output }
        }
        ).done(function( msg ) { 
            message = '<div class="ui-state-highlight ui-corner-all" id="message">';
            message += '<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>';
            message += '<strong> Изменено вариантов ответа - ' + q_count + '</strong><br /></p></div>';
            for (var i = 0; i < q_count; i++) {
                if ($("#" + collate[i]).find(".правильный").text() == 'Да') {
                    $("#" + collate[i]).find(".правильный").text("Нет");
                }
                else {
                    $("#" + collate[i]).find(".правильный").text("Да");
                }
            }
            $(message).appendTo('.message');
        });
}
JS;
        return $code;
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
   
    protected function view_edit_answer_item($a) 
    {
        self::set_title('Редактирование ответа на вопрос теста');
        $i = new QuizAnswerItem($a);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'save_answer' , 'Сохранить ответ');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_edit_answer' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }   

// Работа с настройками теста    
   protected function view_settings_list()
    {
        $title = 'Настройки тестирования';
        $confirm = 'Удаление выбранных настроек';
        $this->current_task = 'settings_list';
        $list = new QuizSettingList();
        self::set_title($title);
        self::set_toolbar_button('new', 'setting_new' , 'Новая настройка');
        $edit_b = self::set_toolbar_button('edit', 'setting_edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'setting_delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm, 'setting_delete');
        $this->set_content($list->get_items_page());
    }
    
    protected function view_new_setting_item() 
    {
        self::set_title('Ввод новой настройки тестирования');
        $i = new QuizSettingItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'setting_save' , 'Сохранить настройку');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'setting_cancel_edit', 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_setting_item($q) 
    {
        self::set_title('Редактирование настройки теста');
        $i = new QuizSettingItem($q);
        $i->edit_item(); 
        $sb = self::set_toolbar_button('save', 'setting_save' , 'Сохранить вопрос');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'setting_cancel_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }


// Пробное тестирование
    protected function view_trial_testing_selection()
    {
        self::set_title('Пробное тестирование');
        $db = self::set_toolbar_button('new', 'start_trial_test' , 'Начать тест');
        $db->validate(true);
        self::set_toolbar_button('cancel', 'cancel_trial_test' , 'Закрыть');
        $u = new TrialTestingSelectionForm();
        $this->set_content($u->get_form());
    }
    
    protected function view_trial_testing($topic)
    {
        $obj = new QuizTopicQuery($topic);
        self::set_title('Пробное тестирование по теме "' . $obj->название_темы . '"'); 
        $stop_test = self::set_toolbar_button('cancel', 'cancel_trial_test' , 'Прервать выполнение теста');
        $stop_test->set_option('action', "function () { $('#quiz-container').quiz('stopQuiz', 'Тест прерван пользователем' ); }");
        //$save_res = self::set_toolbar_button('save', 'save_test_result' , 'Сохранить результат теста');
        //$save_res->set_option('showStatus', false );
        
    }
 
// Результаты тестирования
    
    protected function view_result_list()
    {
        $title = 'Результаты тестирования';
        $this->current_task = 'result_list';
        $list = new QuizResultList();
        self::set_title($title);
        //$edit_b = self::set_toolbar_button('edit', 'edit_question' , 'Редактировать');
        //$edit_b->set_option('obligate', true);
        $this->set_content($list->get_items_page());
    }
}
?>