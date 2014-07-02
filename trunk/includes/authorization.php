<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Factory
* @copyright	Copyright (C) 2009 МИАЦ ИО

  */
 
class AuthException extends Exception {

    public function get_message()
    {
        return $this->message;
    }
}

class Authorization 
{
    private $created;
    public $user_id;
    private $version;
    private $td;
    // параметры криптографии
    static $cypher      = 'blowfish';
    static $mode        = 'cfb';
    static $key         = 'очень хороший ключ';
    static $cookiename  = 'MZ_USERAUTH';
    static $myversion   = '1';
    // время когда заканчивается действие куки
    static $expiration  = '3600';
    // через какое время переиздаем куки
    static $resettime   = '1800';
    static $glue        = '|';

    public function __construct($user_id = false) 
    {
        $this->td = mcrypt_module_open(self::$cypher, '', self::$mode, '');
        if($user_id) {
            $this->user_id = $user_id;
            return;
        }
        else {
            if(array_key_exists(self::$cookiename, $_COOKIE)) {
                $buffer = $this->_unpackage($_COOKIE[self::$cookiename]);
            }
            else {
                throw new AuthException("Cookie не определены");
            }
        }
    }
    
    public function set() 
    {
        $cookie = $this->_package();
        setcookie(self::$cookiename, $cookie);
    }
    
    public function validate() 
    {
        if(!$this->version || !$this->created || !$this->user_id) {
            throw new AuthException("Cookie не верны");
        }
        if ($this->version != self::$myversion) {
            throw new AuthException("Несоответствие версии");
        }
        if (time() - $this->created > self::$expiration) {
            throw new AuthException("Время действия Cookie закончилось");
        } 
        else if (time() - $this->created > self::$resettime) {
            $this->set();
        }
    }
  
    public function logout() 
    {
        setcookie(self::$cookiename, "", 0);
    }
  
    private function _package() 
    {
        $parts = array(self::$myversion, time(), $this->user_id);
        $cookie = implode(self::$glue, $parts);
        return $this->_encrypt($cookie);
    }
    
    private function _unpackage($cookie) 
    {
        $buffer = $this->_decrypt($cookie);
        list($this->version, $this->created, $this->user_id) =
        explode(self::$glue, $buffer);
        if($this->version != self::$myversion || !$this->created || !$this->user_id)
        {
            throw new AuthException();
        }
    }
    
    private function _encrypt($plaintext) 
    {
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->td), MCRYPT_RAND);
        mcrypt_generic_init($this->td, self::$key, $iv);
        $crypttext = mcrypt_generic ($this->td, $plaintext);
        mcrypt_generic_deinit ($this->td);
        return $iv.$crypttext;
    }
  
    private function _decrypt($crypttext) 
    {
        $ivsize = mcrypt_enc_get_iv_size($this->td);
        $iv = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        mcrypt_generic_init ($this->td, self::$key, $iv);
        $plaintext = mdecrypt_generic ($this->td, $crypttext);
        mcrypt_generic_deinit ($this->td);
        return $plaintext;
    }
  
    private function _reissue() 
    {
        $this->created = time();
    }
}
?>
