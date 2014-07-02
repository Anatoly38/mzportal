<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Users
* @copyright	Copyright (C) 2010 МИАЦ ИО
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

class ObjectSave extends ItemSave
{
    protected $model = 'ObjectQuery';
    
    public function get_post_values()
    {
        $this->query->name = Request::getVar('name');
        $this->query->description = Request::getVar('description');
        $this->query->deleted = Request::getVar('deleted');
    }
}
?>