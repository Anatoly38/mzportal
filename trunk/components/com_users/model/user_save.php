<?php
/**
* @version		$Id: user_save.php,v 1.1 2009/12/03 00:50:30 shameev Exp $
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class UserSave extends ItemSave
{
    protected $model = 'UserQuery';
    
    public function get_post_values()
    {
        $this->query->name = Request::getVar('name');
        $this->query->description = Request::getVar('description');
        $this->query->blocked = Request::getVar('blocked');
        $this->query->pwd = Request::getVar('pwd');
        $encryption = Request::getVar('crypt');
        $this->query->set_encryption($encryption);
    }
}
?>