<?php
/**
* @version		$Id: registry.php,v 1.1 2010/01/10 00:10:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Factory
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