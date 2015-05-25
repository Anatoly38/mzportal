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

class QuizTicketQuery extends ClActiveRecord 
{
    protected $source = 'quiz_ticket';
    public $oid;
    public $тема;
    public $настройка;
    public $пин_код;
    public $запуск_теста;
    public $в_процессе;
    public $текущий_вопрос;
    public $начало_теста;
    public $окончание_теста;
    public $продолжительность;
    public $реализована;
    public $статус;
    public $оценка;
    public $балл;
    
    public function __construct($oid = false)
    {
        if ($oid === false) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.тема,
                        a.настройка,
                        a.пин_код,
                        a.запуск_теста,
                        a.в_процессе,
                        a.текущий_вопрос,
                        a.начало_теста,
                        a.окончание_теста,
                        a.продолжительность,
                        a.реализована,
                        a.статус,
                        a.оценка,
                        a.балл
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Попытка тестирования не существует");
        }
        $this->oid              = $oid;
        $this->тема             = $data['тема'];
        $this->настройка        = $data['настройка'];
        $this->пин_код          = $data['пин_код'];
        $this->запуск_теста     = $data['запуск_теста'];
        $this->в_процессе       = $data['в_процессе'];
        $this->текущий_вопрос   = $data['текущий_вопрос'];
        $this->начало_теста     = $data['начало_теста'];
        $this->окончание_теста  = $data['окончание_теста'];
        $this->продолжительность = $data['продолжительность'];
        $this->реализована      = $data['реализована'];
        $this->статус           = $data['статус'];
        $this->оценка           = $data['оценка'];
        $this->балл             = $data['балл'];
        
    }

    public function update() 
    {
        if($this->oid === null || $this->oid === false) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        тема            = :1,
                        настройка       = :2,
                        пин_код         = :3,
                        запуск_теста    = :4,
                        в_процессе      = :5,
                        текущий_вопрос  = :6,
                        начало_теста    = :7,
                        окончание_теста = :8,
                        продолжительность = :9,
                        реализована     = :10,
                        статус          = :11,
                        оценка          = :12,
                        балл            = :13
                    WHERE 
                        oid = :14";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->тема,
                                        $this->настройка,
                                        $this->пин_код,
                                        $this->запуск_теста,
                                        $this->в_процессе,
                                        $this->текущий_вопрос,
                                        $this->начало_теста,
                                        $this->окончание_теста,
                                        $this->продолжительность,
                                        $this->реализована,
                                        $this->статус,
                                        $this->оценка,
                                        $this->балл,
                                        $this->oid
                                        );
            if ($this->show_update_message) {
                Message::alert('Изменения при редактировании данных документа успешно сохранены');
            }
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактировании данных документа не сохранены!');
            return false;
        }
        if ($this->oid !== 0) {
            try {
                $obj = new MZObject($this->oid);
                $obj->name = 'Попытка тестирования';
                $obj->description = $this->тема;
                $obj->update();
            }
            catch (Exception $e) {
                Message::error('Ошибка: изменения <object> не сохранены!');
                return false;
            }
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
        $obj->name = 'Попытка тестирования';
        $obj->description = $this->тема;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    тема,
                    настройка,
                    пин_код,
                    запуск_теста,
                    в_процессе,
                    текущий_вопрос,
                    начало_теста,
                    окончание_теста,
                    продолжительность,
                    реализована,
                    статус,
                    оценка,
                    балл)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->тема,
                                        $this->настройка,
                                        $this->пин_код,
                                        $this->запуск_теста,
                                        $this->в_процессе,
                                        $this->текущий_вопрос,
                                        $this->начало_теста,
                                        $this->окончание_теста,
                                        $this->продолжительность,
                                        $this->реализована,
                                        $this->статус,
                                        $this->оценка,
                                        $this->балл
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
            return false;
        }
        return true;
    } 
}
?>