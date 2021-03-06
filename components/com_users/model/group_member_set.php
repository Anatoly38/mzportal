<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Indexes
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

class GroupMemberSet
{
    protected $error_message = "Добавляемые в группу пользователи не определены";
    protected $alert_message = "Добавлены пользователи";
    protected $items = array();
    protected $gid = null;

    public function __construct($items = false)
    {
        if (!$items) {
            throw new Exception($this->error_message);
        } 
        $this->registry = Registry::getInstance();
        $this->gid = $this->registry->oid[0];
        $this->items = $items;
        $this->set_group();
    }

    private function set_group()
    {
        if (is_array($this->items)) {
            for ($i = 0, $cnt = count($this->items); $i < $cnt; $i++) {
                $this->set_member($this->items[$i]);
            }
            $m = Message::getInstance();
            $m->enque_message('alert', $this->alert_message . ' ('. $cnt .')');
        }
    }

    private function set_member($uid)
    {
        UserGroupQuery::set_user($this->gid, $uid);
    }

}

?>