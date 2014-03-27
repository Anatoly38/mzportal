<?php
/**
* @version      $Id: application.php,v 1.7 2011/09/11 08:24:32 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2011 МИАЦ ИО
 
Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'includes'.DS.'loader.php' );

class AppException extends Exception 
{ 

    public function get_message()
    {
        return $this->message;
    }

}

class Application
{
    private $app_id;
    
    function __construct($app_id = null)
    {
        if ($app_id) {
            $app = $this->get_application($app_id);
            return $app;
        }
        else {
            throw new AppException("Приложение не определено");
        }
    }
    
    private function get_application($app_id) 
    {
        $dbh = new DB_mzportal();
        $query="SELECT cid FROM sys_classes WHERE cid = :1";
        list($cid) = $dbh->prepare($query)->execute($app_id)->fetch_row();
        if (!$cid) {
            throw new AppException("Приложение не найдено");
        }
        $app = Loader::get_object($cid);
        if (is_object($app)) {
            return $app;
        } 
        else {
            throw new AppException("Ошибка загрузки класса приложения");
        }
    }
    
    public static function get_user_applications($uid) 
    {
        $acl_restriction = "AND (a.uid = :1 OR a.uid IN (SELECT ug.uid FROM sys_users_groups AS ug  WHERE ug.gid = a.acl_id)) ";
        $dbh = new DB_mzportal();
        $query="SELECT s.component_id FROM tasks AS s
                    JOIN sys_objects AS o ON s.oid = o.oid  
                    JOIN sys_acl AS a ON o.acl_id = a.acl_id
                    WHERE s.component_id <> '0' $acl_restriction";
        $apps = $dbh->prepare($query)->execute($uid)->fetch();
        return $apps;
    }

}

?>