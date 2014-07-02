<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class MonDocumentQuery extends ClActiveRecord 
{
    protected $source = 'mon_documents';
    public $oid;
    public $тип_отчета;
    public $год;
    public $статус;
    public $комментарий;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.тип_отчета,
                        a.год,
                        a.статус,
                        a.комментарий
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Документ не существует");
        }
        $this->oid          = $oid;
        $this->тип_отчета   = $data['тип_отчета'];
        $this->год          = $data['год'];
        $this->статус       = $data['статус'];
        $this->комментарий  = $data['комментарий'];
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
                        тип_отчета  = :1,
                        год         = :2,
                        статус      = :3,
                        комментарий = :4
                    WHERE 
                        oid = :5";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->тип_отчета,
                                        $this->год,
                                        $this->статус,
                                        $this->комментарий,
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
            $obj->name = $this->тип_отчета;
            $obj->description = $this->статус;
            $obj->update();
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }
    
    public function set_status($new_status)
    {
        if(!$new_status) 
        {
            throw new Exception("Для вызова set_status() необходим код статуса");
        }
        $dbh = new DB_mzportal;
        $query = "UPDATE {$this->source} SET статус = :1 WHERE oid = :2";
        $dbh->prepare($query)->execute($new_status, $this->oid);
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
        $obj->name = Reference::get_name($this->тип_отчета, 'doc_report_kind');
        $obj->description = Reference::get_name($this->статус, 'doc_report_status');
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    тип_отчета,
                    год,
                    статус,     
                    комментарий)
                    VALUES(:1, :2, :3, :4, :5)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->тип_отчета,
                                        $this->год,
                                        $this->статус,
                                        $this->комментарий
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }
}
?>