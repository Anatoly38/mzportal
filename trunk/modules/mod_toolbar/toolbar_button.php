<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Toolbar
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Toolbar_Button
{
    public $options = array();
    
    public function __construct($icon, $task, $title)
    {
        $this->options['icon']      = $icon;
        $this->options['task']      = $task;
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