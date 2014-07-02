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

class UserGroupQuery
{
    protected $source = 'sys_users_groups';
    public $uid;
    public $gid;
    
    public static function find_groups($uid = null)
    {
        if (!$uid) {
            throw new Exception("Имя пользователя не определено");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        s.gid 
                    FROM `sys_users_groups` AS s 
                    WHERE s.uid = :1";
        $data = $dbh->prepare($query)->execute($uid)->fetch();
        if(!$data) {
            return false;
        }
        return $data;
    }

    public static function find_users($gid = null)
    {
        if (!$gid) {
            throw new Exception("Имя пользователя не определено");
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        s.uid 
                    FROM `sys_users_groups` AS s 
                    WHERE s.gid = :1";
        $data = $dbh->prepare($query)->execute($gid)->fetch();
        if(!$data) {
            return false;
        }
        return $data;
    }
    // Удаление пользователя из группы
    public static function delete_user($gid, $uid)
    {
        if (!$gid || !$uid) {
            throw new Exception("Имя пользователя/группы не определено");
        }
        $dbh = new DB_mzportal;
        $query =    "DELETE 
                    FROM sys_users_groups  
                    WHERE gid = :1 AND uid = :2
                    LIMIT 1";
        $result = $dbh->prepare($query)->execute($gid, $uid);
    }
    
    public static function set_user($gid, $uid)
    {
        if (!$gid || !$uid) {
            throw new Exception("Имя пользователя/группы не определено");
        }
        $dbh = new DB_mzportal;
        $query =    "INSERT INTO sys_users_groups (gid , uid) 
                        VALUES(:1, :2)";
        $result = $dbh->prepare($query)->execute($gid, $uid);
    }
}

?>