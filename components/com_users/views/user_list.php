<?php
/**
* @version      $Id: user_list.php,v 1.1 2014/05/23 16:50:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Users
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'model' . DS . 'user_query.php' );

class UserList extends ItemList
{
    protected $model = 'UserQuery';
    protected $source = 'sys_users';
    protected $namespace = 'user_list';
    protected $obj = 'oid';
    protected $id = 'uid';
    protected $default_cols = array('uid', 'name', 'description');
    protected $acl_id = null;
    
    public function __construct()
    {
        parent::__construct($this->model, $this->source, $this->namespace );        
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        $constr->add_filter('name');
        $constr->add_filter('blocked' , 'dic_user_status');
        $constr->get_filters();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    public function exlude_users_by_acl($acl_id)
    {
        $subquery = " AND s.uid NOT IN (SELECT uid FROM sys_acl WHERE acl_id = '$acl_id')";
        $this->where .= $subquery ;
    }
    
    public function include_users_by_acl($acl_id)
    {
        $subquery = " AND s.uid IN (SELECT uid FROM sys_acl WHERE acl_id = '$acl_id')";
        $this->where .= $subquery ;
    }

    protected function list_options()
    {
        $options = array();
        $options['uid'] = array('sort' => false, 'type' => 'checkbox' ); 
        return $options;
    }    

}
?>