<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Users
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'model' . DS . 'access_list_query.php' );

class AccessList extends ItemList
{
    protected $model = 'AccessListQuery';
    protected $source = 'sys_acl_user';
    protected $namespace = 'access_list';
    protected $task = 'current_acl';
    protected $obj  = 'oid';
    protected $id = 'uid';
    protected $default_cols = array( 'uid', 'user_name', 'description');
    
    public function __construct($acl_id = false)
    {
        if (!$acl_id) {
            throw new Exception("Не определен список доступа"); 
        }
        parent::__construct($this->model, $this->source, $this->namespace);
        $this->where = "AND s.acl_id = '$acl_id' ";
    }
    
    protected function set_constrains()
    {
        $constr = Constraint::getInstance();
        $constr->set_namespace($this->namespace);
        //$constr->add_filter('наименование');
        //$constr->get_filters();
    }
    
    protected function add($item)
    {
        parent::add($item);
    }
    
    protected function list_options()
    {
        $options = array();
        $options['uid'] = array('sort' => false, 'type' => 'checkbox' ); 
        return $options;
    }

}
?>