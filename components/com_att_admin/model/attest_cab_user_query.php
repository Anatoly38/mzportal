<?php
/**
* @version      $Id: $
* @package      MZPortal.Framework
* @subpackage   Users
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

class AttestCabUserQuery extends ClActiveRecord 
{
    protected $source = 'attest_cab_user';
    public $uid;
    public $name;
    public $pwd;
    public $description;
    public $blocked;
    
    
    public function __construct($uid = false)
    {
        if (!$uid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.name, 
                        a.pwd,
                        a.description,
                        a.blocked
                    FROM {$this->source} AS a 
                    WHERE uid = :1";
        $data = $dbh->prepare($query)->execute($uid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Пользователь не найден");
        }
        $this->uid = $uid;
        $this->name = $data['name'];
        $this->pwd = $data['pwd'];
        $this->description = $data['description'];
        $this->blocked = $data['blocked'];    
    }
    
    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя пользователя не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT uid FROM `attest_cab_user` WHERE name = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код пользователя не найден");
        }
        return new AttestCabUserQuery($id);
    }

    public function update() 
    {
        if(!$this->uid) 
        {
            throw new Exception("Для вызова update() необходим код пользователя");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        {$this->source} 
                    SET 
                        name = :1, 
                        description = :2,
                        pwd = :3,
                        blocked = :4
                     WHERE 
                        uid = :5"; 
        try {
            $dbh->prepare($query)->execute( 
                                        $this->name,
                                        $this->description,
                                        $this->encryption ? md5($this->pwd) : $this->pwd, 
                                        $this->blocked,
                                        $this->uid
                                        );
            Message::alert('Изменения при редактировании пользователя успешно сохранены');
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии пользователя не сохранены (sys_users)!');
            return false;
        }
        try {
            $obj = new MZObject($this->uid);
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения object при редактированиии пользователя не сохранены!');
            return false;
        }
        $obj->description = $this->name;
        $obj->update();
    }

    public function insert()
    {
        if($this->uid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = 'Пользователь личного кабинета по аттестации';
        $obj->description = $this->name;
        $obj->deleted = 0;
        $obj->create();
        $dbh = new DB_mzportal;
        $this->uid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source}  
                    (uid, name, description, pwd, blocked)
                    VALUES(:1, :2, :3, :4, :5)";
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->name, 
                                        $this->description,
                                        $this->pwd,
                                        $this->blocked
                                        );
            Message::alert('Изменения при вводе нового пользователя успешно сохранены');
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }
}

?>