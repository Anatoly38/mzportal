<?php
/**
* @version		$Id: registry.php,v 1.1 2014/06/26 00:10:27 shameev Exp $
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