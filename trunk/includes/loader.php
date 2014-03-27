<?php 
/**
* @version		$Id: loader.php,v 1.6 2009/09/11 08:24:33 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Loader
{
    public static function get_object($class_id)
    {
        $dbh = new DB_mzportal();
        $query="SELECT name, type, path, file_name FROM sys_classes WHERE cid = :1";
        list($class_name, $type, $path, $file_name) = $dbh->prepare($query)->execute($class_id)->fetch_row();
        if(!$class_name) {
            throw new AppException("Соответствующий класс не найден");
        }
        $full_path = COMPONENTS .DS. $path .DS. $file_name . '.php';
        if (file_exists($full_path)) {
            require_once $full_path;
            $instance = new $class_name();
            return $instance;
        }
        else {
            throw new AppException("Файл класса приложения не найден(". $full_path .")" );
        }
    }
    
}

?>