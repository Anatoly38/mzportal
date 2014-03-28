<?php
/**
* @version      $Id: acl.php,v 1.2 2011/09/11 09:17:00 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Core
* @copyright    Copyright (C) 2011 МИАЦ ИО

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class ACL
{
    private $obj_id = null;
    
    public function __construct($obj_id = null)
    {
        if (!$obj_id) {
            throw new Exception("Объект для изменения списка доступа не определен");
        }
        $this->obj_id = $obj_id;
    }
    
    public static function inherit($recepient, $donor)
    {
        if (!$recepient || !$donor) {
            throw new Exception('Не определены объекты для передачи прав');
        }
        $robj = new MZObject($recepient);
        $dobj = new MZObject($donor);
        $rusers = self::get_acl($robj->acl_id);
        $dusers = self::get_acl($dobj->acl_id);
        $all_users = array_merge($dusers, $rusers);
        $all_users = array_unique($all_users);
        $new_acl_id = self::get_new_acl_id($all_users);
        $robj->acl_id = $new_acl_id;
        $robj->update();
        return $new_acl_id;;
    }
    
    public static function add_user_acl($old_acl, $users, $rights)
    {
        if (!$old_acl) {
            throw new Exception("Список доступа не определен");
        }
        if (!$users) {
            throw new Exception("Пользователи для изменения списка доступа не определены");
        }
        if (!$rights) {
            throw new Exception("Не выбраны разрешения для добавляемых в список доступа пользователей");
        }
        $old_users = self::get_acl($old_acl);
        $new_users = array();
        foreach ($rights as $r) {
            foreach ($users as $u) {
                $new_users[] = $u . '.' . $r;
            }
            reset($users);
        }
        $all_users = array_merge($new_users, $old_users);
        $all_users = array_unique($all_users);
        if (!$new_acl_id = self::find_identical_acl($all_users)) {
            $first_user = array_shift($all_users);
            $new_acl_id = self::insert_acl_row($first_user);
            foreach ($all_users as $a) {
                self::update_acl_row($new_acl_id, $a);
            }
        }
        if (!$new_acl_id) {
            throw new Exception("Ошибка при создании нового списка доступа к объекту");
        }
        return $new_acl_id;
    }
    
    public static function remove_user_acl($old_acl, $users)
    {
        if (!$old_acl) {
            throw new Exception("Объект для изменения списка доступа не определен");
        }
        if (!$users[0]) {
            throw new Exception("Пользователи для изменения списка доступа не определены");
        }
        $old_users = self::get_acl($old_acl);
        $new_users = $old_users;
        $j = 0;
        for ($i = 0, $c = count($new_users); $i < $c; ++$i) {
            $u = explode('.' , $new_users[$i]);
            if (in_array($u[0], $users)) {
                unset($new_users[$i]);
                $j++;
            }
        }
        if (!$new_acl_id = self::find_identical_acl($new_users)) {
            $first_user = array_shift($new_users);
            $new_acl_id = self::insert_acl_row($first_user);
            foreach ($new_users as $u) {
                self::update_acl_row($new_acl_id, $u);
            }
        }
        if (!$new_acl_id) {
            throw new Exception("Ошибка при создании нового списка доступа к объекту");            
        }
        return $new_acl_id; 
    }
   
    private static function update_acl_row($acl_id, $user)
    {
        if (!$acl_id || !$user) {
            throw new Exception("Не определен список доступа и/или идентификатор пользователя");
        }
        $u_r = explode('.' , $user);
        $dbh = new DB_mzportal();
        $query = "INSERT IGNORE INTO `sys_acl` (`acl_id`, `uid`, `right`) VALUES (:1, :2, :3)";
        $dbh->prepare($query)->execute($acl_id, $u_r[0], $u_r[1]);
        return true;
    }
    
    private static function insert_acl_row($user)
    {
        if (!$user) {
            throw new Exception("Не определен идентификатор пользователя");
        }
        $u_r = explode('.' , $user);
        $dbh = new DB_mzportal();
        $query = "INSERT INTO `sys_acl` (`acl_id`, `uid`, `right`) VALUES (NULL, :1, :2)";
        $dbh->prepare($query)->execute($u_r[0], $u_r[1]);
        list($last_id) = $dbh->execute("SELECT LAST_INSERT_ID()")->fetch_row();
        return $last_id;
    }
    
    public static function get_new_acl_id($users)
    {
        if (!$new_acl_id = self::find_identical_acl($users)) {
            $first_user = array_shift($users);
            $new_acl_id = self::insert_acl_row($first_user);
            foreach ($users as $a) {
                self::update_acl_row($new_acl_id, $a);
            }
        }
        if (!$new_acl_id) {
            return false;
        }
        return $new_acl_id;
    }
    
    public static function get_acl($acl)
    {
        $dbh = new DB_mzportal();
        $query = "SELECT `uid`, `right` FROM `sys_acl` WHERE `acl_id` = '$acl'";
        $users = $dbh->execute($query)->fetchall_assoc();
        $ret_val = array();
        foreach ($users as $u) {
            $ret_val[] = $u['uid'] . '.' . $u['right'];
        }
        return $ret_val;
    }
    
    public static function get_obj_acl($oid)
    {
        $dbh = new DB_mzportal();
        $query = "SELECT acl_id FROM sys_objects WHERE oid = $oid";
        list($acl_id) = $dbh->execute($query)->fetch_row();
        return $acl_id;
    }
    
    public static function get_component_acl($component)
    {
        $dbh = new DB_mzportal();
        $query = "SELECT oid FROM tasks_view WHERE component_id = '$component'";
        list($id) = $dbh->execute($query)->fetch_row();
        $acl_id = self::get_obj_acl($id);
        return $acl_id;
    }

    public static function check_right($user, $obj)
    {
        if (!$user || !$obj) {
            throw new Exception("Пользователь и/или объект не определен");
        }
        $rights = array();
        if ($user == '500') {
            $rights[0]['right'] = 1;
            return $rights;
        }
        $dbh = new DB_mzportal();
        $query = "SELECT 
                        a.right
                    FROM
                        sys_acl AS a, sys_objects AS o
                    WHERE 
                        a.acl_id = o.acl_id AND a.uid = '$user' AND a.oid = '$obj'
                    ORDER BY a.right";
        $rights = $dbh->execute($query)->fetch();
        return $rights;
    }
    
    public static function clean()
    {
        $dbh = new DB_mzportal();
        $query = "DELETE FROM sys_acl WHERE acl_id NOT IN (SELECT DISTINCT acl_id FROM `sys_objects`)";
        $dbh->execute($query);
        return true;
    }
    
    public static function find_identical_acl($users = null)
    {
        $users = (array)$users; 
        if (!$users[0]) {
            return false;
        }
        $for_tmp_table = '';
        foreach ($users as $u) {
            $u_r = explode('.' , $u);
            $comma_separated = implode(",", $u_r);
            $for_tmp_table .= "($comma_separated),";
        }
        $for_tmp_table = substr($for_tmp_table, 0, -1);
        $dbh = new DB_mzportal();
        $sql = "CREATE TEMPORARY TABLE `t1` (`uid` int(11) NOT NULL, `right` smallint(6) NOT NULL );
                    INSERT INTO `t1` (`uid`, `right`) VALUES $for_tmp_table;
                    CREATE TEMPORARY TABLE `t2` LIKE `t1`;
                    INSERT INTO `t2` (`uid`, `right`) SELECT t1.`uid`, t1.`right` FROM `t1`;
                    CREATE TEMPORARY TABLE `t3` LIKE `t1`;
                    INSERT INTO `t3` (`uid`, `right`) SELECT t1.`uid`, t1.`right` FROM `t1`;
                    SELECT acl_id FROM sys_acl WHERE acl_id IN (SELECT acl_id FROM sys_acl WHERE (`uid`, `right`) IN (SELECT `uid`, `right` FROM `t1`)) 
                    AND acl_id NOT IN (SELECT acl_id FROM sys_acl WHERE (`uid`, `right`) NOT IN (SELECT `t2`.`uid`, `t2`.`right` FROM `t2`)) GROUP BY acl_id HAVING COUNT(`uid`) = (SELECT COUNT(*) FROM `t3`); "; 
        $mysqli = new mysqli("localhost", "root", MZConfig::$pw, "mzportal") or die ('Could not connect to the database server' . mysqli_connect_error());
        $found = false;
        if ($mysqli->multi_query($sql)) {
           do {
                if ($result = $mysqli->store_result()) {
                    list($found) = $result->fetch_row(); 
                    if ($found) {
                        break;
                    }
                    $result->free();
                }
            } while ($mysqli->next_result());
        }
        $mysqli->close();
        return $found;
    }
}