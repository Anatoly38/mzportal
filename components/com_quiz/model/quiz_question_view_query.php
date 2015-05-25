<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class QuizQuestionViewQuery extends ClActiveRecord 
{
    protected $source = 'quiz_question_topic';
    public $oid;
    public $topic_id;
    public $тема_теста;
    public $текст_вопроса;
    public $тип_вопроса;
    public $количество_ответов;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.topic_id,
                        a.тема_теста,
                        a.текст_вопроса,
                        a.тип_вопроса,
                        a.количество_ответов
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->oid              = $oid;
        $this->topic_id         = $data['topic_id'];
        $this->тема_теста       = $data['тема_теста'];
        $this->текст_вопроса    = $data['текст_вопроса'];
        $this->тип_вопроса      = $data['тип_вопроса'];
        $this->количество_ответов = $data['количество_ответов'];
    }

    public function update() 
    {
    }
    
    public function insert()
    {
    }
    
    public function delete()
    {
    }
}
?>