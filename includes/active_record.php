<?php
/**
* @version		$Id: active_record.php,v 1.1 2010/05/19 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
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
    
    public function get_as_array()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код объекта не определен");
        }
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
    

}
?>