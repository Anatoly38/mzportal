<?php
/** 
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Quiz
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

class TrialQuiz 
{
    private $dbh;
    private $topic;
    private $q_qount;
    private $duration;
    
    public function __construct($topic = false, $q_qount  = null, $duration) {
        if (!$topic) {
            throw new Exception("Не определена тема тестирования");
        }
        if (!$q_qount) {
            throw new Exception("Не определено количество вопросов теста");
        }
        if (!$duration) {
            throw new Exception("Не определена продолжительность теста");
        }
        $this->dbh = new DB_mzportal();
        $this->topic    = $topic;
        $this->q_qount  = $q_qount;
        $this->duration = $duration;
        $this->append_script_tags();
        $this->append_html();
    }
    
    private function get_qestions()
    {
        if (!$n || !$q) {
            return false;
        }
        $this->q_obj->номер_пп      = $n;
        $this->q_obj->текст_вопроса = $q;
        $this->q_obj->insert();
        return true;
    }
    
    private function get_qestions_count()
    {
        $count_query =  "SELECT COUNT(*) FROM (SELECT `s`.`oid` FROM quiz_qestion_topic AS s 
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`  
                        WHERE `s`.`topic_id` = '{$this->topic}') AS source";
        //print_r($count_query);
        list($count) = $this->dbh->execute($count_query)->fetch_row();
        return $count;
    }
    

    private function get_answers($question) 
    {
        $qa = "SELECT * FROM `quiz_answer_question` WHERE `question_id` = '{$question}'";
        $ra = $this->dbh->execute($qa);
        if (!$ra) {
            return null;
            exit;
        }
        $a_arr_q = array(); // Массив с ответами
        $a_arr_c = array(); // Правильные ответы
        $res = array(); // Все вместе 
        $i = 1;
        
        while ($data = $ra->fetch_assoc()) {
            $answer = "'" . trim($data['текст_ответа']) . "'";
            $a_arr_q[] = $answer; 
            if ($data['правильный']) {
                $a_arr_c[] = $i;
            }
            $i++;
        }
        $ans_arr = "[";
        $ans_arr .= implode(",", $a_arr_q);
        $ans_arr .= "]";
        $corr_ans_arr = "[";
        $corr_ans_arr .= implode(",", $a_arr_c);
        $corr_ans_arr .= "]";
        
        $res['answers'] = $ans_arr;
        $res['correct_ans'] = $corr_ans_arr;
        return $res;
    }
    
    private function append_script_tags()
    {
        $seconds = $this->duration * 60;
        $css = CSS::getInstance();
        $css->add_style_link('quiz_styles.css');
        $css->add_style_link('timeTo.css');
        $js = Javascript::getInstance();
        $js->add_quiz();
        $jquizzy = "$('#quiz-container').jquizzy( { questions: init.questions, timeToTest: {$seconds} });";
        $js->add_jblock($jquizzy);
        
        
    }
    
    private function append_html()
    {
    $html = 
<<<HTML
<div id="countdown"></div>
<div id="quiz-container"></div>
HTML;
        $c = Content::getInstance();
        $c->add_content($html);
        return true;
    }

}

?>