<?php
/**
 * @version		$Id$
 * @package		MZPortal.Framework
 * @subpackage	Environment
 * @copyright	Copyright (C) 2009 - 2014 ÌÈÀÖ ÈÎ
 */

// Check to ensure this file is within the rest of the framework
defined('MZPATH_BASE') or die();

class Request
{

    public static function getMethod()
    {
        $method = strtoupper( $_SERVER['REQUEST_METHOD'] );
        return $method;
    }

    public static function getVar($name, $default = null, $hash = 'default', $type = 'none', $mask = 0)
    {
        $hash = strtoupper( $hash );
        if ($hash === 'METHOD') {
            $hash = strtoupper( $_SERVER['REQUEST_METHOD'] );
        }
        $type   = strtoupper( $type );
        $sig    = $hash.$type.$mask;
        switch ($hash)
        {
            case 'GET' :
                $input = &$_GET;
                break;
            case 'POST' :
                $input = &$_POST;
                break;
            case 'FILES' :
                $input = &$_FILES;
                break;
            case 'COOKIE' :
                $input = &$_COOKIE;
                break;
            case 'ENV'    :
                $input = &$_ENV;
                break;
            case 'SERVER'    :
                $input = &$_SERVER;
                break;
            default:
                $input = &$_REQUEST;
                $hash = 'REQUEST';
                break;
        }
        $var = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;
        //$var = MZRequest::_cleanVar($var, $mask, $type);
        return $var;
    }

    public static function getInt($name, $default = 0, $hash = 'default')
    {
        return Request::getVar($name, $default, $hash, 'int');
    }

    public static function getFloat($name, $default = 0.0, $hash = 'default')
    {
        return Request::getVar($name, $default, $hash, 'float');
    }

    public static function getBool($name, $default = false, $hash = 'default')
    {
        return Request::getVar($name, $default, $hash, 'bool');
    }
    
    public static function getWord($name, $default = '', $hash = 'default')
    {
        return Request::getVar($name, $default, $hash, 'word');
    }

    public static function getCmd($name, $default = '', $hash = 'default')
    {
        return Request::getVar($name, $default, $hash, 'cmd');
    }

    public static function getString($name, $default = '', $hash = 'default', $mask = 0)
    {
        // Cast to string, in case MZREQUEST_ALLOWRAW was specified for mask
        return (string) MZRequest::getVar($name, $default, $hash, 'string', $mask);
    }

    public static function _cleanArray( &$array, $globalise=false )
    {
        static $banned = array( '_files', '_env', '_get', '_post', '_cookie', '_server', '_session', 'globals' );
        foreach ($array as $key => $value)
        {
            // PHP GLOBALS injection bug
            $failed = in_array( strtolower( $key ), $banned );
            // PHP Zend_Hash_Del_Key_Or_Index bug
            $failed |= is_numeric( $key );
            if ($failed) {
                mzexit( 'Illegal variable <b>' . implode( '</b> or <b>', $banned ) . '</b> passed to script.' );
            }
            if ($globalise) {
                $GLOBALS[$key] = $value;
            }
        }
    }

    private static function _cleanVar($var, $mask = 0, $type=null)
    {
        // Static input filters for specific settings
        static $noHtmlFilter	= null;
        static $safeHtmlFilter	= null;

        // If the no trim flag is not set, trim the variable
        if (!($mask & 1) && is_string($var)) {
            $var = trim($var);
        }

        // Now we handle input filtering
        if ($mask & 2)
        {
            // If the allow raw flag is set, do not modify the variable
            $var = $var;
        }
        elseif ($mask & 4)
        {
            // If the allow html flag is set, apply a safe html filter to the variable
            if (is_null($safeHtmlFilter)) {
                $safeHtmlFilter = & FilterInput::getInstance(null, null, 1, 1);
            }
            $var = $safeHtmlFilter->clean($var, $type);
        }
        else
        {
            // Since no allow flags were set, we will apply the most strict filter to the variable
            if (is_null($noHtmlFilter)) {
                $noHtmlFilter = & FilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
            }
            $var = $noHtmlFilter->clean($var, $type);
        }
        return $var;
    }


    public static function _stripSlashesRecursive( $value )
    {
        $value = is_array( $value ) ? array_map( array( 'MZRequest', '_stripSlashesRecursive' ), $value ) : stripslashes( $value );
        return $value;
    }
    
    public static function set_value($key, $default = null, $namespace = 'default')
    {
        $value = Request::getVar($key);
        if (isset($value)) {
            if (is_numeric($value)) {
                MZSession::set($key, $value, $namespace);
                return $value;
            }
            else {
                if (!empty($value)) {
                    MZSession::set($key, $value, $namespace);
                    return $value;
                }
            }
        }
        if (MZSession::has($key, $namespace)) {
            $value = MZSession::get($key, $namespace);
            return $value;
        }
        MZSession::clear($key, $namespace);
        return $default;
    }    
    
}
