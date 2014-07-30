<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class TrialQuiz 
{
    private $dbh;
    private $topic;
    private $q_qount = null;
    private $duration;
    private $random;
    private $show_correct_answers;
    private $init_questions; // json объект
    
    public function __construct($topic = false, $q_qount  = null, $duration, $random = false, $show_correct_answers = true) {
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
        $this->topic = $topic;
        if ($q_qount) {
            $this->q_qount  = $q_qount;
        }
        $this->duration = $duration;
        $this->random = $random;
        $this->show_correct_answers = $show_correct_answers;
        $this->init_questions = $this->get_ordered_questions();
        $this->append_script_tags();
        $this->append_html();
        
    }
    
    private function get_ordered_questions()
    {
        $limit = '';
        if ($this->q_qount) {
            $limit = " LIMIT 0, {$this->q_qount} ";
        }
        $q = "SELECT * FROM quiz_question_topic AS s 
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`  
                        WHERE `s`.`topic_id` = '{$this->topic}' AND `o`.`deleted` <> '1' {$limit}";
        $r = $this->dbh->execute($q);
        $i = 1;
        $js_object = "var init = {'questions': [";
        while ($data = $r->fetch_assoc()) {
            $answers = $this->get_answers($data['oid']);
            $js_object .= "{ 'question':'" . $data['текст_вопроса'];
            $js_object .= "','answers':" . $answers['answers'];
            $js_object .= ",'ca':{$answers['correct_ans']},";
            $js_object .= "'qT':{$data['тип_вопроса']}},";
            
        }
        $js_object .= "]};";
        return $js_object;
    }
    
    private function get_shuffled_questions()
    {
        $limit = '';
        if ($this->q_qount) {
            $limit = " LIMIT 0, {$this->q_qount} ";
        }
        
        $query =    "SELECT DISTINCT
                       s.oid 
                    FROM 
                        quiz_question_topic AS s
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`
                    WHERE 1=1
                        `s`.`topic_id` = '{$this->topic}' AND `o`.`deleted` <> '1' ";
        //print_r($query);
        $stmt = $this->dbh->execute($query)->fetch();
        foreach ($stmt as $id) {
            //$this->add(new $this->model($id));
        }
        
        $q = "SELECT * FROM quiz_question_topic AS s 
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`  
                        WHERE `s`.`topic_id` = '{$this->topic}' AND `o`.`deleted` <> '1' {$limit}";
        $r = $this->dbh->execute($q);
        $i = 1;
        $js_object = "var init = {'questions': [";
        while ($data = $r->fetch_assoc()) {
            $answers = $this->get_answers($data['oid']);
            $js_object .= "{ 'question':'" . $data['текст_вопроса'];
            $js_object .= "','answers':" . $answers['answers'];
            $js_object .= ",'ca':{$answers['correct_ans']},";
            $js_object .= "'qT':{$data['тип_вопроса']}},";
            
        }
        $js_object .= "]};";
        return $js_object;
    }
    
    private function get_qestions_count()
    {
        $count_query =  "SELECT COUNT(*) FROM (SELECT `s`.`oid` FROM quiz_question_topic AS s 
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
        $this->show_correct_answers ? $show_ca = 'true' : $show_ca = 'false';
        $css = CSS::getInstance();
        $css->add_style_link('quiz_styles.css');
        $css->add_style_link('timeTo.css');
        $js = Javascript::getInstance();
        $js->add_js_text($this->init_questions);
        $js->add_quiz();
        $jquizzy = "$('#quiz-container').quiz( { questions: init.questions, timeToTest: {$seconds}, showCorrectAnswers: {$show_ca} });";
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