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
    private $q_obj; // вопросы
    private $a_obj; // ответы
    private $topic;
    private $q_qount;
    private $duration;
    
    public function __construct($topic = false, $q_qount  = null, $duration) {
        if (!$topic) {
            throw new Exception("Не определен тема тестирования");
        }
        if (!$q_qount) {
            throw new Exception("Не определено количество вопросов теста");
        }
        if (!$duration) {
            throw new Exception("Не определена продолжительность теста");
        }
        $this->topic    = $topic;
        $this->q_qount  = $q_qount;
        $this->duration = $duration;
        $this->q_obj =  new QuizQTempQuery();
        $this->a_obj =  new QuizATempQuery();
    }
    
    private function insert_qestion_text($n, $q)
    {
        if (!$n || !$q) {
            return false;
        }
        $this->q_obj->номер_пп      = $n;
        $this->q_obj->текст_вопроса = $q;
        $this->q_obj->insert();
        return true;
    }
    
    private function insert_answer_text($n, $a, $c)
    {
        if (!$n || !$a) {
            return false;
        }
        $this->a_obj->id = $n;
        $this->a_obj->текст_ответа  = $a;
        $this->a_obj->правильный    = $c;
        $this->a_obj->insert();
        return true;
    }
}

?>