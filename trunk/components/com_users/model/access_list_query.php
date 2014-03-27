<?php
/**
* @version		$Id: access_list_query.php,v 1.0 2011/04/13 00:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Users
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class AccessListQuery extends ClActiveRecord
{
    protected $source = 'sys_acl_user';
    public $acl_id;
    public $uid;
    public $user_name;
    public $description;
    public $right;
    
    public function __construct($id = false)
    {
        if (!$id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.acl_id,
                        a.uid,
                        a.user_name,
                        a.description,
                        a.`right`
                    FROM {$this->source} AS a 
                    WHERE uid = :1";
        $data = $dbh->prepare($query)->execute($id)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->uid          = $id;
        $this->acl_id       = $data['acl_id'];
        $this->user_name    = $data['user_name'];
        $this->description  = $data['description'];
        $this->right        = $data['right'];
    }
}

?>