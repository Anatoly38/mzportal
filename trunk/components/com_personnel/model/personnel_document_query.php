<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class PersonnelDocumentQuery extends ClActiveRecord
{
    protected $source = 'pers_document';
    public $oid;
    public $тип_документа;
    public $серия_документа;
    public $номер_документа;
    public $кем_выдан;
    public $дата_выдачи;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.oid,
                        a.тип_документа,
                        a.серия_документа,
                        a.номер_документа,
                        a.кем_выдан,
                        a.дата_выдачи
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid = $oid;
        $this->тип_документа    = $data['тип_документа'];
        $this->серия_документа  = $data['серия_документа'];
        $this->номер_документа  = $data['номер_документа'];
        $this->кем_выдан        = $data['кем_выдан'];
        $this->дата_выдачи      = $data['дата_выдачи'];
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
        $obj->name = Reference::get_name($this->тип_документа, 'document_type'); 
        $obj->description = 'Документ сотрудника УЗ';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, тип_документа, серия_документа, номер_документа, кем_выдан, дата_выдачи)
                    VALUES(:1, :2, :3, :4, :5, :6)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,
                                            $this->тип_документа, 
                                            $this->серия_документа, 
                                            $this->номер_документа,
                                            $this->кем_выдан,
                                            $this->дата_выдачи
                                            );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код документа сотрудника");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        тип_документа = :1,
                        серия_документа = :2, 
                        номер_документа = :3,
                        кем_выдан = :4,
                        дата_выдачи = :5
                     WHERE 
                        oid = :6";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->тип_документа, 
                                        $this->серия_документа, 
                                        $this->номер_документа,
                                        $this->кем_выдан,
                                        $this->дата_выдачи,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных документа сотрудника успешно сохранены');
        } 
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных документа сотрудника не сохранены!' . $e->getMessage());
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            //$obj->name = $this->oid;
            $obj->description = 'Документ сотрудника'  . ' ' . $this->тип_документа . ' ' . $this->серия_документа . ' ' . $this->номер_документа;
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public static function get_card($oid)
    {
        $link = Reference::get_id('документ', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>