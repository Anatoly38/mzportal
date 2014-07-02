<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО
* @license      GNU/GPL, see LICENSE.php
 
Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MODULES .DS.'mod_user'.DS.'acl.php' );

class LinkObjects
{
    public $left; // id "родительского" объекта
    public $right; // id "подчиненного" объекта
    
    public function __construct()
    {
    }
    
    public static function set_link($left = null, $right = null, $link_type = '0', $set_rights = false)
    {
        if (!$left || !$right) {
            throw new Exception("Объекты не определены");
        }
        if ($left == $right) {
            throw new Exception("Объект не может быть подчинен самому себе");        
        }
        $dbh = new DB_mzportal;
        // Проверяем не является ли подчиняемый объект одновременно "хозяином"
        // Если да, то возвращаем ошибку.
        $query1 ="SELECT * FROM `sys_obj_links` WHERE `left` = :1 AND `right` = :2 AND `link_type` = :3";
        $data1 = $dbh->prepare($query1)->execute($right, $left, $link_type)->fetch_assoc();
        if ($data1) {
            throw new Exception("Объект не может быть одновременно и подчиненным и подчиняемым в данной иерархии");                
        }
        $i = "INSERT IGNORE INTO `sys_obj_links` (`link_type`, `left`, `right`) VALUES(:1, :2, :3)";
        $dbh->prepare($i)->execute($link_type, $left, $right); 
        if ($set_rights) {
            self::inherit_rights($left, $right);
        }
    }
    
    public static function unset_link($left = null, $right = null, $link_type = '0')
    {
        if (!$left || !$right ) {
            throw new Exception("Объекты не определены");
        }
        $dbh = new DB_mzportal;
        $i = "DELETE FROM `sys_obj_links` WHERE `link_type` = :1 AND `left` = :2 AND `right` = :3";
        $dbh->prepare($i)->execute($link_type, $left, $right);
    }

    public static function get_parents($oid, $link_type = null)
    {
        if (!$oid) {
            return null;
        }
        $dbh = new DB_mzportal;
        $l = null;
        if ($link_type) {
           $l = " AND link_type ='$link_type' ";
        }
        $query ="SELECT `left` FROM sys_obj_links WHERE `right` = :1 " . $l;
        $data = $dbh->prepare($query)->execute($oid)->fetch();
        if (!$data) {
            return null;
        }
        return $data;
    }
    
    public static function get_childs($oid, $link_type = null)
    {
        if (!$oid) {
            return null;
        }
        $dbh = new DB_mzportal;
        $l = null;
        if ($link_type) {
           $l = " AND link_type ='$link_type' ";
        }
        $query ="SELECT `right` FROM sys_obj_links WHERE `left` = :1 " . $l;
        $data = $dbh->prepare($query)->execute($oid)->fetch();
        if (!$data) {
            return null;
        }
        return $data;
    }
    
    private static function inherit_rights($left = null, $right = null) 
    {
        if (!$left || !$right) {
            throw new Exception("Объекты для наследования прав не определены");
        }
        $left_acl_id = ACL::get_obj_acl($left);
        $r_obj = new MZObject($right);
        $r_obj->acl_id = $left_acl_id;
        $r_obj->update();
        return true;
    }
    
    public static function clean()
    {
        $dbh = new DB_mzportal();
        $query = "DELETE FROM `sys_obj_links` WHERE `left` <> 0 AND (`left` NOT IN (SELECT oid FROM sys_objects) OR `right` NOT IN (SELECT oid FROM sys_objects));";
        $dbh->execute($query);
        return true;
    }
}

?>