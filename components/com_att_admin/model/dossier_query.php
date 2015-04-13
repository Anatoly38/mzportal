<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class DossierQuery extends ClActiveRecord 
{
    protected $source = 'attest_dossier';
    public $oid;
    public $номер_дела;
    public $фио;
    public $email;
    public $мо;
    public $экспертная_группа;
    public $вид_должности;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.номер_дела,
                        a.фио,
                        a.email,
                        a.мо,
                        a.экспертная_группа,
                        a.вид_должности
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Группа не существует");
        }
        $this->oid          = $oid;
        $this->номер_дела   = $data['номер_дела'];
        $this->фио          = $data['фио'];
        $this->email        = $data['email'];
        $this->мо           = $data['мо'];
        $this->экспертная_группа = $data['экспертная_группа'];
        $this->вид_должности     = $data['вид_должности'];
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
                        email       = :3,
                        мо          = :4,
                        экспертная_группа = :5,
                        вид_должности     = :6
                    WHERE 
                        oid = :6";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->номер_дела,
                                        $this->фио,
                                        $this->email,
                                        $this->мо,
                                        $this->экспертная_группа,
                                        $this->вид_должности,
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
                    email,
                    мо,
                    экспертная_группа,
                    вид_должности
                    )
                    VALUES(:1, :2, :3, :4, :5, :6, :7 )";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->номер_дела,
                                        $this->фио,
                                        $this->email,
                                        $this->мо,
                                        $this->экспертная_группа,
                                        $this->вид_должности
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>