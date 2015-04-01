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

class QuizQTempQuery extends ClActiveRecord 
{
    protected $source = 'quiz_q_temp';
    public $номер_пп;
    public $текст_вопроса;
    public $тип_вопроса;
    
    public function __construct($id = false)
    {
        if (!$id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.текст_вопроса,
                        a.тип_вопроса
                    FROM {$this->source} AS a 
                    WHERE номер_пп = :1";
        $data = $dbh->prepare($query)->execute($id)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->номер_пп         = $id;
        $this->текст_вопроса    = $data['текст_вопроса'];
        $this->тип_вопроса      = $data['тип_вопроса'];
    }

    public function update() 
    {
        if(!$this->номер_пп) 
        {
            throw new Exception("необходим номер вопроса");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        текст_вопроса   = :1,
                        тип_вопроса     = :2
                    WHERE 
                        номер_пп = :3";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->текст_вопроса,
                                        $this->тип_вопроса,
                                        $this->номер_пп
                                        );
            Message::alert('Изменения при редактировании данных документа успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при импортировании вопросов теста не сохранены!');
            return false;
        }
    }
    
    public function insert()
    {
        if(!$this->номер_пп) 
        {
            throw new Exception("необходим номер вопроса");
        }
        $query =    "INSERT INTO {$this->source} 
                    (номер_пп, 
                    текст_вопроса,
                    тип_вопроса
                    )
                    VALUES(:1, :2, :3)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->номер_пп,
                                        $this->текст_вопроса,
                                        $this->тип_вопроса
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }

    public function truncate_table()
    {
        $query = 'TRUNCATE quiz_q_temp';
        $dbh = new DB_mzportal;
        $dbh->execute($query);
        return true;
    }
}
?>