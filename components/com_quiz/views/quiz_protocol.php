<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizProtocol
{
    private $text;
    private $dt_obj;
    private $page_breake = false;
    
    public function __construct($id = false)
    {
        if (!$id) {
            throw new Exception("Не определен идентификатор аттестационного дела");
        }
        $this->dt_obj = new AttestDossierTicketQuery($id);
    }
    
    public function show_title($title)
    {
        $t = '<h3 style="margin-bottom:0em">' . $title . '</h3>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_subtitle($title)
    {
        $t = '<h4 style="margin-bottom:0em">' . $title . '</h4>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_strong($title)
    {
        $t = '<p style="margin-top:0.5em;margin-bottom:0em"><b>' . $title . '</b></p>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_dossier()
    {
        $t = 'Номер дела: ' . $this->dt_obj->номер_дела . '<br/>';
        $t .= 'ФИО соискателя: ' . $this->dt_obj->фио . '<br/>';
        //$t .= 'Email: ' . $this->dt_obj->email . '<br/>';
        $t .= 'Медицинская организация: ' . Reference::get_name($this->dt_obj->мо, 'subordination') . '<br/>';
        $t .= 'Экспертная группа: ' . Reference::get_name($this->dt_obj->экспертная_группа, 'expert_groups') . '<br/>';
        $t .= 'Вид должности: ' . Reference::get_name($this->dt_obj->вид_должности, 'position_short') . '<br/>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_ticket()
    {
        //$t = 'Идентификатор попытки: ' . $this->dt_obj->oid . '<br/>';
        $t = 'Основная тема: ' . Reference::get_name($this->dt_obj->тема, 'quiz_topics') . '<br/>';
        $t .= 'Настройка тестирования: ' . Reference::get_name($this->dt_obj->настройка, 'quiz_settings') . '<br/>';
        $t .= 'Дата и время: ' . date('d.m.Y H:s', $this->dt_obj->начало_теста)  . '<br/>';
        $d = round(($this->dt_obj->окончание_теста-$this->dt_obj->начало_теста)/60);
        $t .= 'Продолжительность: ' . $d . ' мин. <br/>';
        $t .= 'Статус: ' . Reference::get_name($this->dt_obj->статус, 'quiz_status') . '<br/>';
        $t .= 'Доля правильных ответов (%): ' . $this->dt_obj->балл . '<br/>';
        $t .= "Оценка: " . $this->dt_obj->оценка . "<br/>";
        
        $this->add_text($t);
        return $t;
    }
    
    public function show_questions()
    {
        try {
            $res_obj = new QuizResult($this->dt_obj->oid);
            $questions = json_decode($res_obj->result, true);
            $q_quantity = count($questions);
            $t = 'Всего вопросов в задании: ' . $q_quantity . '<br/>';
            $t .= 'Были допущены ошибки при ответе на следующие вопросы:';
            $t .= '<span style="font-size:8pt"><table border="0">';
            $incorrect = 0;
            $answered = 0;
            foreach ($questions as $question) {
                $q_id = $question['questionNumber'];
                $answers_user = explode(',', $question['userAnswers']);
                if (!empty($answers_user[0])) {
                    $answered++;
                    $open = '';
                    $close = '';
                    $bopen = '';
                    $bclose = '';
                    //($q%2) ? $open = '<tr>' : $close = '</tr>';
                    $open = '<tr>' ; $close = '</tr>';
                    $answers_db = new QuizAnswers($q_id);
                    foreach ($answers_user as $a) {
                        if (!$answers_db->check_answer($a)) {
                            $incorrect++;
                            //$bopen = "<b>";
                            //$bclose = "</b>";
                            $q_obj = new QuizQuestionQuery($q_id);
                            $t .= $open;
                            $t .= '<td>'. $incorrect . '.'. $bopen . $q_obj->текст_вопроса . $bclose . '</td>';
                            $t .= $close;
                            break;
                        }
                    }
                }
            }
            $t .= "</table></span><br/>";
            if (!$incorrect) {
                $t .= " - все ответы верны<br/>";
            }
            $unanswered = $q_quantity-$answered;
            if ($unanswered) {
                $t .= "Кол-во вопросов без ответа: " . $unanswered;
            }
        } 
        catch (Exception $e) {
            //$t = 'Нет данных по пройденным вопросам теста' . $e;
            $t = 'Нет данных по пройденным вопросам теста';
        }
        $this->add_text($t);
        return $t;
    }
    
    public function set_pagebreake($p = true)
    {
        $this->page_breake = $p;
    }
    
    public function add_text($t)
    {
        $this->text .= $t;
    }
    
    public function get_text()
    {
        $all =  '';
        $this->page_breake ? $all .= '<div style="page-break-after:always;">' :  $all .= '';
        $all .= $this->text;
        $this->page_breake ? $all .= '</div>' :  $all .= '';
        return $all;
    }
  
}
?>