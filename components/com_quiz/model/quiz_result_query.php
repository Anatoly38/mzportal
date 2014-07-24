<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class QuizResultQuery extends ClActiveRecord 
{
    protected $source = 'quiz_result';
    public $oid;
    public $uid;
    public $topic_id;
    public $начало_теста;
    public $продолжительность_теста;
    public $оценка;
    public $балл;
    public $время_сохранения;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.uid,
                        a.topic_id,
                        a.начало_теста,
                        a.продолжительность_теста,
                        a.оценка,
                        a.балл,
                        a.время_сохранения
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->oid              = $oid;
        $this->uid              = $data['uid'];
        $this->topic_id         = $data['topic_id'];
        $this->начало_теста     = $data['начало_теста'];
        $this->продолжительность_теста      = $data['продолжительность_теста'];
        $this->оценка           = $data['оценка'];
        $this->балл             = $data['балл'];
        $this->время_сохранения = $data['время_сохранения'];
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
                        uid             = :1,
                        topic_id  	    = :2,
                        начало_теста  	= :3,
                        продолжительность_теста = :4,
                        оценка  	    = :5,
                        балл  	        = :6,
                        время_сохранения = :7
                    WHERE 
                        oid = :3";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->uid,
                                        $this->topic_id,
                                        $this->начало_теста,
                                        $this->продолжительность_теста,
                                        $this->оценка,
                                        $this->балл,
                                        $this->время_сохранения,
                                        $this->oid
                                        );
            if ($this->show_update_message) {
                Message::alert('Изменения при редактировании результата тестирования успешно сохранены');
            }
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
                    (
                    oid, 
                    uid,
                    topic_id,
                    начало_теста,
                    продолжительность_теста,
                    оценка,
                    балл,
                    время_сохранения
                    )
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->uid,
                                        $this->topic_id,
                                        $this->начало_теста,
                                        $this->продолжительность_теста,
                                        $this->оценка,
                                        $this->балл,
                                        $this->время_сохранения
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>