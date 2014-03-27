<?php
/**
* @version		$Id: session.php 2010-01-19 21:03:02Z ian $
* @package		MZPortal.Framework
* @subpackage	Session
* @copyright	Copyright (C) 2010 МИАЦ ИО
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

//Register the session storage class with the loader
require_once('sessionstorage.php');

class MZSession 
{
    private static $instance;
    private static $state = 'active';
    public $store = null;

    public function __construct()
    {
        $this->store = SessionStorage::getInstance();
    }

    public function getState() {
        return $this->state;
    }

    public function getName()
    {
        if( $this->state === 'destroyed' ) {
            // error
            return null;
        }
        return session_name();
    }

    public function getId()
    {
        if( $this->state === 'destroyed' ) {
            return null;
        }
        return session_id();
    }

    public static function get($name, $namespace = 'default', $default = null)
    {
        $namespace = '__'.$namespace; //add prefix to namespace to avoid collisions
        if(self::$state !== 'active') {
            // error here
            $error = null;
            return $error;
        }

        if (isset($_SESSION[$namespace][$name])) {
            return $_SESSION[$namespace][$name];
        }
        return $default;
    }

    public static function set($name, $value, $namespace = 'default')
    {
        $namespace = '__'.$namespace; //add prefix to namespace to avoid collisions
        if(self::$state !== 'active') {
            return null;
        }
        $old = isset($_SESSION[$namespace][$name]) ?  $_SESSION[$namespace][$name] : null;
        if (null === $value) {
            unset($_SESSION[$namespace][$name]);
        } else {
            $_SESSION[$namespace][$name] = $value;
        }
        return $old;
    }

    public static function has($name, $namespace = 'default')
    {
        $namespace = '__'.$namespace; //add prefix to namespace to avoid collisions
        if( self::$state !== 'active' ) {
            // @TODO :: generated error here
            return null;
        }
        return isset( $_SESSION[$namespace][$name] );
    }
    
    public static function hasNS($namespace = null)
    {
        $namespace = '__'.$namespace; //add prefix to namespace to avoid collisions
        if( self::$state !== 'active' ) {
            return null;
        }
        return isset( $_SESSION[$namespace] );
    }

    public static function clear($name, $namespace = 'default')
    {
        $namespace = '__'.$namespace; //add prefix to namespace to avoid collisions
        if( self::$state !== 'active' ) {
            // @TODO :: generated error here
            return null;
        }
        if (isset($_SESSION[$namespace][$name])) {
            unset($_SESSION[$namespace][$name]);
            return true;
        }
        return false;
    }

    public function start($id)
    {
        session_id($id);
        session_cache_limiter('none');
        session_start();
        $this->state = 'active';
        return true;
    }

    public static function destroy()
    {
        if(self::$state === 'destroyed') {
            return true;
        }
        session_unset();
        session_destroy();
        self::$state = 'destroyed';
        return true;
    }

    public function close() {
        session_write_close();
    }

}
