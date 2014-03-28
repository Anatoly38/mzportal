<?php
/**
* @version		$Id: components.php,v 1.1 2011/10/27 11:15:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Tasks
* @copyright	Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class Components extends ClActiveRecord
{
    protected $source = 'tasks_view';
    protected $upd_source = 'tasks';
    public $oid;
    public $наименование;
    public $описание;
    public $component_id;
    public $name;
    public $входит_в;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.наименование, 
                        a.описание,
                        a.component_id,
                        a.name,
                        a.parent_id,
                        a.входит_в
                    FROM `{$this->source}` AS a 
                    WHERE a.oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Задача не найдена");
        }
        $this->oid = $oid;
        $this->наименование = $data['наименование'];
        $this->описание     = $data['описание'];
        $this->component_id = $data['component_id'];
        $this->name         = $data['name'];
        $this->parent_id    = $data['parent_id'];
        $this->входит_в     = $data['входит_в'];
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя задачи не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM `tasks_view` WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код задачи не найден");
        }
        return new TaskQuery($id);
    }
    
    public static function findByComponent($c = null)
    {
        if (!$c) {
            throw new Exception("Компонент не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM `tasks_view` WHERE component_id = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код задачи не найден");
        }
        return new TaskQuery($id);
    }
    
    public static function get_rights($c = null, $u = null)
    {
        if (!$c || !$u) {
            throw new Exception("Компонент и/или пользователь не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT `right` FROM `tasks_view` WHERE `component_id` = :1 AND `uid` = :2";
        $rights = $dbh->prepare($query)->execute($c, $u)->fetch();
        if(!$rights) {
            return null;
        }
        return $rights;
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код задачи");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        `{$this->upd_source}` 
                    SET 
                        наименование = :1, 
                        описание = :2,
                        component_id = :3
                     WHERE 
                        oid = :4"; 
        try {
            $dbh->prepare($query)->execute( 
                                        $this->наименование,
                                        $this->описание,
                                        $this->component_id, 
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании задачи успешно сохранены');
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактировании задачи не сохранены (tasks)!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения object при редактированиии задачи не сохранены!');
            return false;
        }
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
        $obj->update();
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        $obj = MZObject::set_class_id($class_name);
        $obj->name = $this->наименование;
        $obj->description = $this->описание;
        $obj->deleted = 0;
        $obj->create();
        $dbh = new DB_mzportal;
        $this->uid = $obj->obj_id;
        $query =    "INSERT INTO `{$this->upd_source}` 
                    (oid, наименование, описание, component_id)
                    VALUES(:1, :2, :3, :4)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->наименование, 
                                        $this->описание,
                                        $this->component_id 
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }
}
?>