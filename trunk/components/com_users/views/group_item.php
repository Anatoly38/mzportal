<?php
/**
* @version      $Id: group_item.php,v 1.0 2014/05/23 11:50:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   User
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class GroupItem extends Item 
{
    public $query;
    protected $model = 'GroupQuery';
    protected $form = 'group_form_tmpl';
 
    protected function set_values($add = null)
    {
        $values = $this->query->get_as_array();
        $values['oid'] = $values['gid'];
        $this->form_loader->load_values($values);
    }

}

?>