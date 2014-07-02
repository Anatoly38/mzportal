<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	User
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class UserItem extends Item 
{
    public $query;
    protected $model = 'UserQuery';
    protected $form = 'user_form_tmpl';
 
    protected function set_values($add = null)
    {
        $values = $this->query->get_as_array();
        $values['oid'] = $values['uid'];
        $this->form_loader->load_values($values);
    }
    
}

?>