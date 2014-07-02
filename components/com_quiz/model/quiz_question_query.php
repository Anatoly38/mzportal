<?php
/**
* @version      $Id: quiz_question_query.php,v 1.0 2014/07/02 13:13:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class QuizQuestionQuery extends ClActiveRecord 
{
    protected $source = 'quiz_question';
    public $oid;
    public $текст_вопроса;
    public $тип_вопроса;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.текст_вопроса,
                        a.тип_вопроса
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->oid              = $oid;
        $this->текст_вопроса    = $data['текст_вопроса'];
        $this->тип_вопроса      = $data['тип_вопроса'];
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        текст_вопроса   = :1,
                        тип_вопроса  	= :2
                    WHERE 
                        oid = :3";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->текст_вопроса,
                                        $this->тип_вопроса,
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании вопроса успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактированиии вопроса не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = 'Вопрос теста';
            $obj->description = $this->текст_вопроса;
            $obj->update();
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = 'Вопрос теста';
        $obj->description = $this->текст_вопроса;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    текст_вопроса,
                    тип_вопроса
                    )
                    VALUES(:1, :2, :3)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->текст_вопроса,
                                        $this->тип_вопроса
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>