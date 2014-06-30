<?php
/**
* @version      $Id: quiz_a_temp_query.php,v 1.0 2014/06/04 13:13:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );

class QuizATempQuery extends ClActiveRecord 
{
    protected $source = 'quiz_a_temp';
    public $id;
    public $текст_ответа;
    public $правильный;
    
    public function __construct($id = false)
    {
        if (!$id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.текст_ответа,
                        a.правильный
                    FROM {$this->source} AS a 
                    WHERE номер_пп = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->id              = $id;
        $this->текст_ответа    = $data['текст_ответа'];
        $this->правильный      = $data['правильный'];
    }

    public function update() 
    {
        if(!$this->id) 
        {
            throw new Exception("необходим номер вопроса");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        текст_ответа    = :1,
                        правильный      = :2
                    WHERE 
                        номер_пп = :3";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->текст_ответа,
                                        $this->правильный,
                                        $this->id
                                        );
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при импортировании ответов на вопросы теста не сохранены!');
            return false;
        }
    }
    
    public function insert()
    {
        if(!$this->id) 
        {
            throw new Exception("необходим номер ответа");
        }
        $query =    "INSERT INTO {$this->source} 
                    (номер_пп, 
                    текст_ответа,
                    правильный
                    )
                    VALUES(:1, :2, :3)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->id,
                                        $this->текст_ответа,
                                        $this->правильный
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }

    public function truncate_table()
    {
        $query = 'TRUNCATE quiz_a_temp';
        $dbh = new DB_mzportal;
        $dbh->execute($query);
        return true;
    }
    
}
?>