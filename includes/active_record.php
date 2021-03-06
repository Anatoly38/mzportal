<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'object.php' );

interface ActiveRecord
{
    public function __construct($oid = false);
    public function update();
    public function insert();
    public function delete();
    public function get_as_array();
}

class ClActiveRecord
{
    protected   $link_type;
    protected   $source;
    protected   $show_update_message = true;
    public      $acl_id = null;
    
    public function delete()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код не определен, удаление не возможно");
        }
        $query = "DELETE FROM {$this->source} WHERE oid = :1";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute($this->oid);
    }
    
    public function get_parent()
    {
        if(!$this->oid) {
            throw new Exception("Код объекта не определен");
        }
        $p = LinkObjects::get_parents($this->oid, $this->link_type);
        if (!$p) {
            return null;
        }
        return $p[0];
    }
    
    public function set_update_message($state = true) 
    {
        $this->show_update_message = $state;
        return true;
    }
    
    public function get_as_array()
    {
/*         if(!$this->oid) 
        {
            throw new Exception("Код объекта не определен");
        } */
        $fields = array();
        foreach($this as $key => $value) {
            $fields[$key] = $value;
        }
        return $fields;
    }
    
    public function get_fields_titles()
    {
        $dbh = new DB_mzportal;
        $fields = array();
        $titles = array();
        $fields = $dbh->get_fields($this->source);
        foreach ($fields as $fkey => $fvalue) {
            if (!$fvalue['comment']) {
                $titles[$fkey] = $fkey;
            } 
            else {
                $titles[$fkey] = $fvalue['comment'];
            }
        }
        return $titles;
    }
    
    public static function get_titles($table)
    {
        $dbh = new DB_mzportal;
        $fields = array();
        $titles = array();
        $fields = $dbh->get_fields($table);
        foreach ($fields as $fkey => $fvalue) {
            if (!$fvalue['comment']) {
                $titles[$fkey] = $fkey;
            } 
            else {
                $titles[$fkey] = $fvalue['comment'];
            }
        }
        return $titles;
    }

}
?>