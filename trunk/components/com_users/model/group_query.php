<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Users
* @copyright	Copyright (C) 2009-2010 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class GroupQuery extends ClActiveRecord implements ActiveRecord
{
    protected $source = 'sys_groups';
    protected $oid;
    public $gid;
    public $name;
    public $description;
    public $blocked;
    
    public function __construct($gid = false)
    {
        if (!$gid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.name, 
                        a.name,
                        a.description,
                        a.blocked
                    FROM `sys_groups` AS a 
                    WHERE gid = :1";
        $data = $dbh->prepare($query)->execute($gid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Пользователь не найден");
        }
        $this->oid = $gid;
        $this->gid =& $this->oid;
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->blocked = $data['blocked'];    
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя группа не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT gid FROM `sys_groups` WHERE name = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код группы не найден");
        }
        return new GroupQuery($id);
    }

    public function update() 
    {
        if(!$this->gid) 
        {
            throw new Exception("Для вызова update() необходим код группы");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        `sys_groups` 
                    SET 
                        name = :1, 
                        description = :2,
                        blocked = :3
                     WHERE 
                        gid = :4"; 
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute( 
                                        $this->name,
                                        $this->description,
                                        $this->blocked,
                                        $this->gid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании группы пользователей успешно сохранены');
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии группы пользователей не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->gid);
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения object при редактированиии пользователя не сохранены!');
            return false;
        }
        $obj->description = $this->name;
        $obj->update();
    }

    public function insert($obj_type = 'group')
    {
        if($this->gid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = $class_name . ' obj';
        $obj->description = $this->name;
        $obj->deleted = 0;
        $obj->create($obj_type);
        $dbh = new DB_mzportal;
        $this->gid = $obj->obj_id;
        $query =    "INSERT INTO `sys_groups` 
                    (gid, name, description, blocked)
                    VALUES(:1, :2, :3, :4)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->name, 
                                        $this->description,
                                        $this->blocked
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }
 
}

?>