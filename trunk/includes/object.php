<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2011 МИАЦ ИО
 
Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class ObjException extends Exception { }

class MZObject
{
    public $obj_id = null;
    public $class_id;
    public $name = null;
    public $description = null;
    public $deleted = 0;
    public $created;
    public $changed;
    public $crc32;
    public $acl_id;
    public $owner;
    public $obj_type = null;
    
    
    public function __construct($id = null)
    {
        if (!$id) {
            return;
        }
        $dbh = new DB_mzportal;
                $query ="SELECT * FROM sys_objects WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($id)->fetch_assoc();
        if(!$data) {
            throw new Exception("Объект не найден");
        }
        $this->obj_id = $id;
        $this->name = $data['name'];
        $this->description  = $data['description'];
        $this->deleted      = $data['deleted'];
        $this->created      = $data['created'];
        $this->changed      = $data['changed'];
        $this->updates      = $data['updates'];
        $this->crc32        = $data['crc32'];
        $this->acl_id       = $data['acl_id'];
        $this->owner        = $data['owner'];
    }
    
    public static function set_class_id($class_name)
    {
        $query = "SELECT cid FROM `sys_classes` WHERE name = :1";
        $dbh = new DB_mzportal;
        list($class_id) = $dbh->prepare($query)->execute($class_name)->fetch_row();
        if(!$class_id) {
            throw new Exception("Идентификатор класса не найден");
        }
        $obj = new MZObject();
        $obj->class_id = $class_id;
        return $obj;
    }
    
    public function create($type = null)
    {
        $this->obj_type = $type;
        $r = Registry::getInstance();
        $user_id = $r->user->user_id;
        $new_obj_id = $this->get_max_id();
        $query =    "INSERT INTO `sys_objects`
                    (oid, classId, name, description, deleted, created, changed, acl_id, owner)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9)";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute( $new_obj_id, 
                                        $this->class_id,
                                        empty($this->name) ? '' : $this->name, 
                                        empty($this->description) ? '' : $this->description, 
                                        $this->deleted, 
                                        date("Y-m-d H:i:s"),
                                        date("Y-m-d H:i:s"),
                                        isset($this->acl_id) ? $this->acl_id : '1',
                                        $user_id );
        if (!$new_obj_id) {
            list($this->obj_id) = $dbh->prepare("SELECT last_insert_id()")->execute()->fetch_row();
        } 
        else {
            $this->obj_id = $new_obj_id;
        }
    }
    
    public function delete()
    {
        if (!$this->obj_id) {
            throw new ObjException("Идентификатор объекта не определен");
        }
        $this->deleted = 1;
        $query ="   UPDATE 
                        `sys_objects`
                    SET 
                        deleted = :1,
                        changed = :2
                    WHERE 
                        oid = :3"; 
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute( $this->deleted, date("YmdHis"), $this->obj_id );
    }
    
    public function update()
    {
        if (!$this->obj_id) {
            throw new ObjException("Идентификатор объекта не определен");
        }
        $query ="UPDATE sys_objects SET 
                        name = :1, 
                        description = :2, 
                        changed     = :3, 
                        updates     = :4,
                        acl_id      = :5,
                        owner       = :6
                        WHERE oid   = :7";
        try {
            $dbh = new DB_mzportal;
            $dbh->prepare($query)->execute( $this->name, 
                                            $this->description,
                                            date("YmdHis"),
                                            $this->updates++,
                                            $this->acl_id,
                                            $this->owner,
                                            $this->obj_id );
        }
        catch (MysqlException $e) {
            Message::error('Ошибка: изменения object не сохранены! ' . $e->code);
            return false;
        }
    }

    public function is_deleted()
    {
        if (!$this->obj_id) {
            throw new ObjException("Идентификатор объекта не определен");
        }
        if ($this->deleted = 1) {
            return true;
        }
        else {
            return false;
        }
    }
    
    private function get_max_id() 
    {
        $dbh = new DB_mzportal();
        $query = "SELECT MAX(OID)+1 AS next_id FROM `sys_objects` ";
        
        switch ($this->obj_type) {
            case null:
                $query .="WHERE OID > '9999'";
            break;
            case 'class':
                $query .= "WHERE OID < '100'";
            break;
            case 'group':
                $query .= "WHERE OID > '99' AND OID < '500'";
            break;
            case 'user':
                $query .= "WHERE OID > '499' AND OID < '10000'";
            break;            
        }
        list($next_id) = $dbh->execute($query)->fetch_row();
        if (!$next_id) {
            throw new ObjException("Ошибка определения идентификатора объекта для вставки");
        }
        return $next_id;
    }
    
    public static function get_childs($parent, $link_type = null) 
    {
        if (!$parent) {
            throw new Exception("Не задан родительский объект");
        }
        if (!$link_type) {
            $link_type_add = null; 
        }
        else {
            $link_type_add = " AND link_type = $link_type"; 
        }
        $dbh = new DB_mzportal();
        $query = "SELECT `right` FROM sys_obj_links WHERE `left` = '$parent' $link_type_add";
        $objects = $dbh->execute($query)->fetch();
        return $objects;
    }
}
?>