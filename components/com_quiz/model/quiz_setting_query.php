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

class QuizSettingsQuery extends ClActiveRecord 
{
    protected $source = 'quiz_settings';
    public $oid;
    public $наименование;
    public $основная_тема;
    public $доп_тема1_наименование;
    public $доп_тема1_доля;
    public $доп_тема2_наименование;
    public $доп_тема2_доля;
    public $доп_тема3_наименование;
    public $доп_тема3_доля;
    public $количество_вопросов;
    public $продолжительность_теста;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.наименование,
                        a.основная_тема,
                        a.доп_тема1_наименование,
                        a.доп_тема1_доля,
                        a.доп_тема2_наименование,
                        a.доп_тема2_доля,
                        a.доп_тема3_наименование,
                        a.доп_тема3_доля,
                        a.количество_вопросов,
                        a.продолжительность_теста
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->oid                      = $oid;
        $this->наименование             = $data['наименование'];
        $this->основная_тема            = $data['основная_тема'];
        $this->доп_тема1_наименование   = $data['доп_тема1_наименование'];
        $this->доп_тема1_доля           = $data['доп_тема1_доля'];
        $this->доп_тема2_наименование   = $data['доп_тема2_наименование'];
        $this->доп_тема2_доля           = $data['доп_тема2_доля'];
        $this->доп_тема3_наименование   = $data['доп_тема3_наименование'];
        $this->доп_тема3_доля           = $data['доп_тема3_доля'];
        $this->количество_вопросов      = $data['количество_вопросов'];
        $this->продолжительность_теста  = $data['продолжительность_теста'];
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
                        наименование            = :1,
                        основная_тема  	        = :2,
                        доп_тема1_наименование  = :3,
                        доп_тема1_доля          = :4,
                        доп_тема2_наименование  = :5,
                        доп_тема2_доля  	    = :6,
                        доп_тема3_наименование  = :7,
                        доп_тема3_доля          = :8,
                        количество_вопросов     = :9,
                        продолжительность_теста = :10
                    WHERE 
                        oid = :11";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->наименование,
                                        $this->основная_тема,
                                        $this->доп_тема1_наименование,
                                        $this->доп_тема1_доля,
                                        $this->доп_тема2_наименование,
                                        $this->доп_тема2_доля,
                                        $this->доп_тема3_наименование,
                                        $this->доп_тема3_доля,
                                        $this->количество_вопросов,
                                        $this->продолжительность_теста,
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
            $obj->name = 'Настройка тестирования';
            $obj->description = $this->наименование;
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
        $obj->name = 'Настройка тестирования';
        $obj->description = $this->наименование;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (
                    oid, 
                    наименование,
                    основная_тема,
                    доп_тема1_наименование,
                    доп_тема1_доля,
                    доп_тема2_наименование,
                    доп_тема2_доля,
                    доп_тема3_наименование,
                    доп_тема3_доля,
                    количество_вопросов,
                    продолжительность_теста
                    )
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->наименование,
                                        $this->основная_тема,
                                        $this->доп_тема1_наименование,
                                        $this->доп_тема1_доля,
                                        $this->доп_тема2_наименование,
                                        $this->доп_тема2_доля,
                                        $this->доп_тема3_наименование,
                                        $this->доп_тема3_доля,
                                        $this->количество_вопросов,
                                        $this->продолжительность_теста
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>