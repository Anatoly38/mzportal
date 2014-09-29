<?php
/**
* @version      $Id $
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class DossierQuery extends ClActiveRecord 
{
    protected $source = ' attest_dossier';
    public $oid;
    public $номер_дела;
    public $фио;
    public $мо;
    public $экспертная_группа;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.номер_дела,
                        a.фио,
                        a.мо,
                        a.экспертная_группа
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Группа не существует");
        }
        $this->oid          = $oid;
        $this->номер_дела   = $data['номер_дела'];
        $this->фио          = $data['фио'];
        $this->мо           = $data['мо'];
        $this->экспертная_группа = $data['экспертная_группа'];
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
                        номер_дела  = :1,
                        фио         = :2,
                        мо          = :3,
                        экспертная_группа = :4
                    WHERE 
                        oid = :5";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->номер_дела,
                                        $this->фио,
                                        $this->мо,
                                        $this->экспертная_группа,
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
            $obj->name = 'Аттестационное дело';
            $obj->description = $this->номер_дела;
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
        $obj->name = 'Аттестационное дело';
        $obj->description = $this->номер_дела;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    номер_дела, 
                    фио,
                    мо,
                    экспертная_группа
                    )
                    VALUES(:1, :2, :3, :4, :5)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->номер_дела,
                                        $this->фио,
                                        $this->мо,
                                        $this->экспертная_группа
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>