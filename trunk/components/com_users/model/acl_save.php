<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Users
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

class ACLSave
{
    private $tasks;
    private $uid;
    
    public function __construct($uid)
    {
        if (!$uid) {
            throw new Exception("Пользователь не определен");
        }
        $this->uid = $uid;
    }
    
    public function get_post_values()
    {
        $this->tasks = Request::getVar('task_id');
    }
}
?>