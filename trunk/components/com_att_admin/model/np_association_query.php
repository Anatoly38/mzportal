<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class NPAssociationQuery extends ClActiveRecord 
{
    protected $source = 'attest_np_association';
    public $oid;
    public $наименование;
    public $аббревиатура;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.наименование,
                        a.аббревиатура
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Вопрос не существует");
        }
        $this->oid              = $oid;
        $this->наименование     = $data['наименование'];
        $this->аббревиатура     = $data['аббревиатура'];
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
                        наименование    = :1,
                        аббревиатура    = :2
                    WHERE 
                        oid = :3";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->наименование,
                                        $this->аббревиатура,
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактированиии не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = 'Медицинская ассоциация';
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
        $obj->name = 'Медицинская ассоциация';
        $obj->description = $this->наименование;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    наименование,
                    аббревиатура
                    )
                    VALUES(:1, :2, :3)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->наименование,
                                        $this->аббревиатура
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>