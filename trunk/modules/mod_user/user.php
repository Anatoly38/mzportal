<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО
  */
 
class UserException extends Exception {

    public function get_message()
    {
        return $this->message;
    }
}

class User 
{
    public $user_id;
    public $name;
    public $pwd;
    public $description;
    public $blocked;
    
    public function __construct($user_id = false)
    {
        if (!$user_id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT *
                    FROM `sys_users` 
                    WHERE uid = :1";
        $data = $dbh->prepare($query)->execute($user_id)->fetch_assoc();
        if(!$data) {
            throw new UserException("Учетная запись пользователя не найдена");
        }
        $this->user_id  = $user_id;
        $this->name     = $data['name'];
        $this->pwd      = $data['pwd'];
        $this->description = $data['description'];
        $this->blocked  = $data['blocked'];
    }

    public static function find_by_name($user_name = null)
    {
        if (!$user_name) {
            throw new Exception("Имя учетной записи показателя не определено");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT user_id 
                    FROM `sys_users` 
                    WHERE name = :1";
        list($id) = $dbh->prepare($query)->execute($index_name)->fetch_row();
        if(!$id) {
            throw new UserException("Код учетной записи пользователя не найден");
        }
        return new User($id);
    }

/*     public function update() 
    {
        if(!$this->user_id) 
        {
            throw new UserException("Для вызова update() необходим код учетной записи пользователя");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE `sys_users`
                    SET 
                        name = :1, 
                        pwd = :2, 
                        blocked = :3,
                        description = :4,
                        changed = :5
                    WHERE uid = :6"; 
        $dbh->prepare($query)->execute( $this->name, 
                                        md5($this->pwd),
                                        $this->blocked,
                                        $this->description,
                                        date("Y-m-d H:i:s"),
                                        $this->user_id);
    }

    public function insert()
    {
        if($this->user_id) 
        {
            throw new UserException("Уже определен код учетной записи, вставка невозможна");
        }
        $query =    "INSERT INTO `sys_users`
                        (name, pwd, blocked, created, changed)
                    VALUES
                        (:1, :2, :3, :4, :5)";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute( $this->name, 
                                        md5($this->pwd),
                                        $this->blocked, 
                                        date("Y-m-d H:i:s", time()+(8*60*60)),
                                        date("Y-m-d H:i:s", time()+(8*60*60)) );
        list($this->user_id) = $dbh->prepare("SELECT last_insert_id()")->execute()->fetch_row();
    }
 
    public function delete()
    {
        if(!$this->user_id) 
        {
            throw new UserException("Код учетной записи пользователя не определен");
        }
        $query = "DELETE FROM `sys_users` WHERE uid = :1";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute($this->user_id);
    }
    
    public function get_as_array()
    {
        if(!$this->user_id) 
        {
            throw new UserException("Код показателя не определен");
        }
        $fields = array();
        foreach($this as $key => $value) {
            $fields[$key] = $value;
        }
        return $fields;
    } */
}

?>
