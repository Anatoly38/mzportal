<?php
/** 
* @version		$Id: question_import.php,v 1.0 2014/07/01 12:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Quiz
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'quiz_q_temp_query.php' );
require_once ( 'quiz_a_temp_query.php' );

class QuestionImport 
{
    private $q_obj; // вопросы
    private $a_obj; // ответы
    private $topic; // тема теста, куда добавляем импортированные вопросы
    private $topic_question_link; // связь между темой и импортируемым вопросом
    private $question_answer_link; // связь между импортируемым вопросом и ответом к нему
    private $question_type = true; // устанавливаем тип вопроса автоматически или нет (по умолчанию - да)
    
    public function __construct($topic = null, $scope = 'all') {
        if (!$topic) {
            throw new Exception("Не определена тема теста");
        }
        $this->topic = $topic;
        $this->topic_question_link = Reference::get_id('тема-вопрос', 'link_types');
        $this->question_answer_link = Reference::get_id('вопрос-ответ', 'link_types');
    }
    
    public function import_all()
    {
        $question = new QuizQTempList();
        $question->set_limit(0);
        $items = $question->get_items();
        if (!$items) {
            throw new Exception("В БД нет вопросов для импорта");
        }
        $q = 0;
        $a = 0;
        foreach ($items as $item) {
            $q_temp = new QuizQTempQuery($item);
            $q_oid = $this->insert_qestion($q_temp->текст_вопроса);
            $ret = $this->insert_answers($item, $q_oid);
            $a = $a + $ret;
            $q++;
        }
        $converted = array();
        $converted['q_count'] = $q;
        $converted['a_count'] = $a;
        return $converted;
    }
    
    private function insert_qestion($text)
    {
        if (!$text) {
            return false;
        }
        $q_obj = new QuizQuestionQuery();
        $q_obj->текст_вопроса = $text;
        $q_obj->insert();
        try {
            LinkObjects::set_link($this->topic, $q_obj->oid, $this->topic_question_link); // Ассоциация между темой и вопросом теста
        }
        catch (Exception $e) {
            Message::error('Ошибка: Ассоциация между объектами (Тема теста, Вопрос теста) не сохранена!');
            return false;
        }
        return $q_obj->oid;
    }
    
    private function insert_answers($temp_id, $q_oid)
    {
        if (!$temp_id || !$q_oid) {
            return false;
        }
        $dbh = new DB_mzportal();
        $query = "SELECT * FROM `quiz_a_temp` AS s WHERE s.`номер_пп` = '{$temp_id}' ";
        $temp_answ = $dbh->execute($query)->fetchall_assoc();
        $total_answer_count = 0;
        $correct_answer_count = 0;
        foreach ($temp_answ as $a_temp) {
            $a = new QuizAnswerQuery();
            $a->текст_ответа    = $a_temp['текст_ответа'];
            $a->правильный      = $a_temp['правильный'];
            $a->insert();
            if ($a_temp['правильный']) {
                $correct_answer_count++;
            }
            try {
                LinkObjects::set_link($this->topic, $a->oid, $this->question_answer_link); // Ассоциация между темой и вопросом теста
            }
            catch (Exception $e) {
                Message::error('Ошибка: Ассоциация между объектами (Вопрос теста, Ответ на вопрос) не сохранена!');
                return false;
            }
        }
        $ret = array();
        $ret['c_qount'] = $total_answer_count;
        $ret['c_qount'] = $correct_answer_count;
        return $ret;
    }
    
    public function set_q_type_mode($t = true) {
        $this->question_type = $t;
        return true;
    }
    
    private function set_question_type($q_id, $q_type) {
        if (!$q_id || !q_type) {
            return false;
        }
        $q_obj = new QuizQuestionQuery($q_id);
        $q_obj->тип_вопроса = $q_type;
        $q_obj->update();
        return true;
    }

?>