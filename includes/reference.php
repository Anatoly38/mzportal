<?php 
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2011 МИАЦ ИО

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Reference
{
    public static function get_name($ref_id, $ref_table, $preffix = 'dic_')
    {
        $ref_table = $preffix . $ref_table;
        $dbh = new DB_mzportal();
        $query="SELECT наименование FROM $ref_table WHERE код = :1";
        list($name) = $dbh->prepare($query)->execute($ref_id)->fetch_row();
        return $name;
    }
    
    public static function get_parent($ref_id, $ref_table, $preffix = 'dic_')
    {
        $ref_table = $preffix . $ref_table;
        $dbh = new DB_mzportal();
        $query="SELECT родитель FROM $ref_table WHERE код = :1";
        list($code) = $dbh->prepare($query)->execute($ref_id)->fetch_row();
        $name = self::get_name($code, $ref_table, $preffix = '');
        return $name;
    }
    
    public static function get_id($ref_name, $ref_table, $preffix = 'dic_')
    {
        $ref_table = $preffix . $ref_table;
        $dbh = new DB_mzportal();
        $query="SELECT код FROM $ref_table WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($ref_name)->fetch_row();
        return $id;
    }

    public static function get_frmr($ref_frmr, $ref_table, $preffix = 'dic_')
    {
        $ref_table = $preffix . $ref_table;
        $dbh = new DB_mzportal();
        $query="SELECT федеральный_код FROM $ref_table WHERE код = :1";
        list($frmr) = $dbh->prepare($query)->execute($ref_frmr)->fetch_row();
        return $frmr;
    }
    
    public static function get_from_frmr($ref_frmr, $ref_table, $preffix = 'dic_')
    {
        $ref_table = $preffix . $ref_table;
        $dbh = new DB_mzportal();
        $query="SELECT код FROM $ref_table WHERE федеральный_код = :1";
        list($id) = $dbh->prepare($query)->execute($ref_frmr)->fetch_row();
        return $id;
    }
}

?>