<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО


Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Registry 
{
    private static $instance = false;
    private $vars = array();
    
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new Registry;
        }
        return self::$instance;    
    }

    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    public function __get($index)
    {
        return $this->vars[$index];
    }

}

?>