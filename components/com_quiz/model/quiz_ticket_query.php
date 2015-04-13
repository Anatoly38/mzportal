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
    public $в_процессе;
    public $текущий_вопрос;
    public $реализована;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.тема,
                        a.настройка,
                        a.пин_код,
                        a.в_процессе,
                        a.текущий_вопрос,
                        a.реализована
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
        $this->в_процессе       = $data['в_процессе'];
        $this->текущий_вопрос   = $data['текущий_вопрос'];
        $this->реализована      = $data['реализована'];
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
                        тема            = :1,
                        настройка       = :2,
                        пин_код         = :3,
                        в_процессе      = :4,
                        текущий_вопрос  = :5,
                        реализована     = :6
                    WHERE 
                        oid = :7";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->тема,
                                        $this->настройка,
                                        $this->пин_код,
                                        $this->в_процессе,
                                        $this->текущий_вопрос,
                                        $this->реализована,
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании данных документа успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактированиии данных документа не сохранены!');
            return false;
        }
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
                    в_процессе,
                    текущий_вопрос,
                    реализована
                    )
                    VALUES(:1, :2, :3, :4, :5, :6)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->тема,
                                        $this->настройка,
                                        $this->в_процессе,
                                        $this->текущий_вопрос,
                                        $this->реализована
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