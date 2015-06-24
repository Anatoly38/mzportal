<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class QuizAnswers  
{
    private $source = 'quiz_answer_question';
    private $collate;
    
    public function __construct($qid = false)
    {
        if (!$qid) {
            throw new Exception("Не определен идентификатор вопроса для работы с ответами");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        oid, текст_ответа, правильный
                    FROM {$this->source}  
                    WHERE question_id = :1";
        $this->collate = $dbh->prepare($query)->execute($qid)->fetchall_assoc();
        if(!$this->collate) {
            throw new Exception("Не найдены ответы соответствующие приведенному идентификатору вопроса");
        }
    }

    public function check_answer($order)
    {
        return $this->collate[$order-1]['правильный'];
    }
    
    public function get_answers()
    {
        return $this->collate;
    }
}
?>