<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php

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