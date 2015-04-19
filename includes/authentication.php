<?php
/** 
 * @version 
 */
require_once ('common/database.php' );
 
class Authentication 
{
    public static function check_credentials($name = null, $password = null)  
    {
        if (!$password || !$name) {
            throw new AuthException("Не введено имя пользователя и/или пароль!");        
        }
        $dbh = new DB_mzportal();
        $query="SELECT uid FROM sys_users WHERE name = :1 AND pwd = :2 AND (blocked = '0' OR blocked IS NULL)";
        $cur = $dbh->prepare($query)->execute($name, md5($password));
        $row = $cur->fetch_assoc();
        if($row) {
            $userid = $row['uid'];
        }
        else {
            throw new AuthException("Пользователь не авторизован!");
        }
        return $userid;
    }
}

?>