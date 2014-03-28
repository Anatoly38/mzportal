<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details. 

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Toolbar_Button
{
    public $options = array();
    
    public function __construct($icon, $action, $title)
    {
        $this->options['icon']      = $icon;
        $this->options['action']    = $action;
        $this->options['title']     = $title;
    }
    
    public function set_option($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function validate($value = true)
    {
        if ($value) {
            $this->options['validate'] = true;
        }
        else {
            $this->options['validate'] = false;
        }
    }
    
    public function track_dirty($value = true)
    {
        if ($value) {
            $this->options['trackdirty'] = true;
        }
        else {
            $this->options['trackdirty'] = false;
        }
    }
}

?>