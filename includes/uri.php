<?php
/**
 * @version		$Id$
 * @package		MZPortal.Framework
 * @subpackage	Environment
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
class URI
{
    private static $instance = false;
	public static $base_path = null;
    public static $query = Array();
    public static $application = null;
    public static $task = null;
    public static $oid = null;
    public static $view = null;

	private function __construct()
	{
        self::set_base_path();
        self::set_path();
	}

    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new URI;
        }
        return self::$instance;
    }
    
    private static function set_base_path()
    {
        if (!self::$base_path) {
            self::$base_path = MZConfig::$index;
        }
    }
    
    private static function set_path()
    {
        if (array_key_exists('app', $_REQUEST)) {
            self::$application = $_REQUEST['app'];
        }
        if (array_key_exists('task', $_REQUEST)) {
            self::$task = $_REQUEST['task'];
        }
        if (array_key_exists('oid', $_REQUEST)) {
            if (is_array($_REQUEST['oid'])) {
                self::$oid = $_REQUEST['oid'];
            } 
            else {
                self::$oid = array();
                self::$oid[] = $_REQUEST['oid'];
            }
        }
        if (array_key_exists('view', $_REQUEST)) {
            self::$view = $_REQUEST['view'];
        } 
        else {
            self::$view = 'list';
        }
    }

}
