<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class TrialQuiz 
{
    private $dbh;
    private $topic;
    public $setting = null;
    private $q_count = 0;
    private $duration = 60; // продолжительность теста в минутах
    private $ordered = false;
    private $correct_answers = false;
    private $init_questions; // json объект
    
    public function __construct($topic = false) {
        if (!$topic) {
            throw new Exception("Не определена тема тестирования");
        }
        if (!self::qcount($topic)){
            throw new Exception("Нет вопросов по основной теме");
        }
        $this->dbh = new DB_mzportal();
        $this->topic = $topic;
    }
    
    public function start_quiz()
    {
        $this->init_questions = $this->compose_set();
        $this->append_script_tags();
        $this->append_html();    
    }
    
    public function set_settings($s_id)
    {
        try {
            $this->setting  = new QuizSettingQuery($s_id);
            $this->q_count  = $this->setting->количество_вопросов;
            $this->duration = $this->setting->продолжительность_теста;
            $this->ordered  = $this->setting->сортировка;
            $this->correct_answers =$this->setting->показ_ответов;
        }
        catch (Exception $e) {
            Message::error('Ошибки инициализации настроек тестирования');
        }
    }
    
    public function set_qcount($q)
    {
        $this->q_count = (int)$q;
        return $this->q_count;
    }
    
    public function set_duration($d)
    {
        $this->duration = (int)$d;
        return $this->duration;
    }
    
    public function show_ordered($t = true)
    {
        $this->ordered = (bool)$t;
        return $this->ordered;
    }
    
    public function show_correct_answers($t = true)
    {
        $this->correct_answers = (bool)$t;
        return $this->correct_answers;
    }

    private function compose_set()
    {
        if (!$this->q_count) {
            $this->q_count  = $this->get_questions_count(); // Если не указано конкретное число вопросов в квизе выводим все вопросы по указанной теме
        } 
        if (!$this->setting) {
            $ret = $this->get_questions($this->topic, $this->q_count);
        } 
        else {
            $add_topic1 = $this->setting->доп_тема1_наименование;
            $add_topic2 = $this->setting->доп_тема2_наименование;
            $add_topic3 = $this->setting->доп_тема3_наименование;
            $add_topic4 = $this->setting->доп_тема4_наименование;
            $qcount1 =  round($this->q_count/100*$this->setting->доп_тема1_доля);
            $qcount2 =  round($this->q_count/100*$this->setting->доп_тема2_доля);
            $qcount3 =  round($this->q_count/100*$this->setting->доп_тема3_доля);
            $qcount4 =  round($this->q_count/100*$this->setting->доп_тема4_доля);
            $main_topic_qount = $this->q_count-($qcount1+$qcount2+$qcount3+$qcount4);
            $main = $this->get_questions($this->topic, $main_topic_qount);
            $ret1 = $this->get_questions($add_topic1, $qcount1);
            $ret2 = $this->get_questions($add_topic2, $qcount2);
            $ret3 = $this->get_questions($add_topic3, $qcount3);
            $ret4 = $this->get_questions($add_topic4, $qcount4);
            $ret = $main . $ret1 . $ret2 . $ret3. $ret4;
        }
        $js_object_string = "var init = {'questions': [";
        $js_object_string .= $ret;
        $js_object_string .= "]};";
        return $js_object_string;
    }
    
    private function get_questions($topic, $qcount) 
    {
        if (!$this->ordered) {
            $ret = $this->get_shuffled_questions($topic, $qcount);
        } 
        else {
            $ret = $this->get_ordered_questions($topic, $qcount);
        }
        return $ret;
    }
    
    public static function qcount($topic)
    {
        $dbh = new DB_mzportal();
        $q = "SELECT count(s.oid) FROM quiz_question_topic AS s 
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`  
                        WHERE `s`.`topic_id` = :1 AND `o`.`deleted` <> '1'";
        list($count) = $dbh->prepare($q)->execute($topic)->fetch_row();
        return $count;
    }
    
    private function get_ordered_questions($topic, $qcount = null)
    {
        $limit = '';
        if ($qcount) {
            $limit = " LIMIT 0, {$qcount} ";
        }
        $q = "SELECT * FROM quiz_question_topic AS s 
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`  
                        WHERE `s`.`topic_id` = '{$topic}' AND `o`.`deleted` <> '1' {$limit}";
        $r = $this->dbh->execute($q);
        $i = 1;
        $js_object = "";
        while ($data = $r->fetch_assoc()) {
            $answers = $this->get_answers($data['oid']);
            //$js_object .= "{ 'question':'" . $data['текст_вопроса'];
            $js_object .= "{ 'question':'" ;
            $js_object .= "','answers':" . $answers['answers'] . ",";
            $js_object .= "'ca':{$answers['correct_ans']},";
            $js_object .= "'qId':{$data['oid']},";
            $js_object .= "'aId':{$answers['ids']},";
            $js_object .= "'qT':{$data['тип_вопроса']}},";
        }
        return $js_object;
    }
    
    private function get_shuffled_questions($topic, $qcount = null)
    {
        $query =    "SELECT DISTINCT
                       s.oid 
                    FROM 
                        quiz_question_topic AS s
                        JOIN `sys_objects` AS `o` ON `s`.`oid` = `o`.`oid`
                    WHERE `s`.`topic_id` = '{$topic}' AND `o`.`deleted` <> '1' ";
        //print_r($query);
        $stmt = $this->dbh->execute($query)->fetch();
        shuffle($stmt);
        $js_object = "";
        if (count($stmt) < $qcount) {
            $qcount = count($stmt);
        }
        for ($i = 0; $i <  $qcount; $i++) {
            $o = new QuizQuestionQuery($stmt[$i]);
            $answers = $this->get_answers($o->oid);
            //$js_object .= "{ 'question':'" . $o->текст_вопроса;
            $js_object .= "{ 'question':'" ;
            $js_object .= "','answers':" . $answers['answers'] . ",";
            $js_object .= "'ca':{$answers['correct_ans']},";
            $js_object .= "'qId':{$o->oid},";
            $js_object .= "'aId':{$answers['ids']},";
            $js_object .= "'qT':{$o->тип_вопроса}},";            
        }
        return $js_object; 
    }
    
    private function get_questions_count()
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
        $a_arr_ids = array(); // Массив с Id ответов
        $res = array(); // Все вместе 
        $i = 1;
        while ($data = $ra->fetch_assoc()) {
            $answer = "'" . trim($data['текст_ответа']) . "'";
            $a_arr_q[] = $answer; 
            $a_arr_ids[] = $data['oid']; 
            if ($data['правильный']) {
                $a_arr_c[] = $i;
            }
            $i++;
        }
        $ans_arr = "[";
        $ans_arr .= implode(",", $a_arr_q);
        $ans_arr .= "]";
        $id_arr = "[";
        $id_arr .= implode(",", $a_arr_ids);
        $id_arr .= "]";
        $corr_ans_arr = "[";
        $corr_ans_arr .= implode(",", $a_arr_c);
        $corr_ans_arr .= "]";
        
        $res['answers'] = $ans_arr;
        $res['ids']     = $id_arr;
        $res['correct_ans'] = $corr_ans_arr;
        return $res;
    }
    
    private function append_script_tags()
    {
        $seconds = $this->duration * 60;
        $this->correct_answers ? $show_ca = 'true' : $show_ca = 'false';
        $css = CSS::getInstance();
        $css->add_style_link('quiz_styles.css');
        $css->add_style_link('timeTo.css');
        $js = Javascript::getInstance();
        $js->add_js_text($this->init_questions);
        $js->add_quiz();
        $jquizzy = "$('#quiz-container').quiz( { questions: init.questions, timeToTest: {$seconds}, showCorrectAnswers: {$show_ca} });";
        $js->add_jblock($jquizzy);
        
    }
    
    private function obfuscate($text)
    {
        $text = str_split($text);
        $o = '';
        foreach ($text as $l)
        {
            $o .= '%' . ord($l);
        }
        return $o;
    }
    
/*     
    private function obfuscate($text)
    {
        $text = str_split($text, 2);
        $o = '';
        foreach ($text as $l)
        {
            $o .= '%' . $l[0] . (ord($l[0])+ord($l[1]));
        }
        return $o;
    }


private function obfuscate($text)
    {
        $text = str_split($text, 2);
        $o = '';
        foreach ($text as $l)
        {
            $v1 = ord($l[0]);  
            $v2 = isset($l[1]) ? ord($l[1]) : 0;
            $o .= '%' . $l[0] . ( $v1 + $v2) ;
        }
        return $o;
    }
 */    
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