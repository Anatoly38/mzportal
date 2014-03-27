<?php
/**
* @version		$Id:sessionstorage.php 6961 2010-01-17 16:06:53Z tcp $
* @package		MZPortal.Framework
* @subpackage	Session
* @copyright	Copyright (C) 2010 МИАЦ ИО
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

class SessionStorage
{
    private static $instance = false;
    private static $dbh;
    private $max_lifetime;

    private function __construct()
    {
        $this->register();
        $this->max_lifetime = 2880; 
        //$this->max_lifetime = get_cfg_var("session.gc_maxlifetime"); 
    }

    public static function getInstance()
    {
        if(self::$instance === false) {
            self::$instance = new SessionStorage;
        }
        return self::$instance;
    }

    private function register()
    {
        $s = session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
        );
        if (!$s) {
            throw new Exception("Обработчик сессии не зарегистрирован"); 
        }
    }

    public function open($save_path, $session_name)
    {
        self::$dbh = new DB_mzportal();
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $ts = time() - $this->max_lifetime;
        $query  = "SELECT session_data
                    FROM sys_sessions
                    WHERE session_id = :1 AND mod_time > FROM_UNIXTIME($ts)";
        list($session_data) = self::$dbh->prepare($query )->execute($id)->fetch_row();
        return $session_data;
    }

    public function write($id, $session_data)
    {
        $query  = "REPLACE INTO
                    sys_sessions (session_id, session_data, mod_time)
                    VALUES(:1, :2, NOW())";
        self::$dbh->prepare($query)->execute($id, $session_data);
        return true;
    }

    public function destroy($id)
    {
        self::$dbh->execute("DELETE FROM sys_sessions WHERE session_id = '$id'");
        $_SESSION = array();
        return true;
    }

    public function gc($maxlifetime)
    {
        $ts = time() - $this->max_lifetime;
        self::$dbh->execute("DELETE FROM sys_sessions WHERE mod_time < FROM_UNIXTIME($ts)");
        return true;
    }
}