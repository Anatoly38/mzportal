<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
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
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class DicQuery  
{
    protected $source;
    public $код;
    public $родитель;
    public $наименование;
    public $комментарий;
    public $альтернативный_код;
    public $федеральный_код;
    
    public function __construct($source = false, $id = false)
    {
        if (!$source) {
            throw new Exception("Словарь не определен");
        }
        if (!$id) {
            return;
        }
        $this->source = $source;
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.код,
                        a.родитель,
                        a.наименование,
                        a.комментарий, 
                        a.федеральный_код,
                        а.альтернативный_код
                    FROM $source AS a 
                    WHERE код = :1";
        $data = $dbh->prepare($query)->execute($id)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не найдена");
        }
        $this->код                  = $data['код'];
        $this->родитель             = $data['родитель'];
        $this->наименование         = $data['наименование'];
        $this->комментарий          = $data['комментарий'];
        $this->федеральный_код      = $data['федеральный_код'];
        $this->альтернативный_код   = $data['альтернативный_код'];
    }

    public static function find_by_name($source = false, $name = null)
    {
        if (!$source) {
            throw new Exception("Словарь не определен");
        }
        if (!$name) {
            throw new Exception("Имя записи для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT код FROM $source WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код записи не найден");
        }
        return new DicQuery($source, $id);
    }

    public static function get_parent($source = false, $p_code = null)
    {
        if (!$source) {
            throw new Exception("Словарь не определен");
        }
        if (!$p_code) {
            throw new Exception("Имя родительского элемента не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT код FROM {$source} WHERE родитель = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код родительского элемента не найден");
        }
        return new DicQuery($source, $id);
    }

    public function update() 
    {
        if(!$this->код) 
        {
            throw new Exception("Для вызова update() необходим код записи");
        }
        if (!$this->source) {
            throw new Exception("Словарь не определен");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        родитель           = :2, 
                        наименование       = :3, 
                        комментарий        = :4, 
                        федеральный_код    = :5,
                        альтернативный_код = :6,
                     WHERE 
                        код = :1";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->код,
                                        $this->родитель,
                                        $this->наименование,
                                        $this->комментарий,
                                        $this->федеральный_код,
                                        $this->альтернативный_код
                                        );
        } 
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
            return false;
        }
    }

    public function insert()
    {
        if($this->код) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        if (!$this->source) {
            throw new Exception("Словарь не определен");
        }
        $query =    "INSERT INTO {$this->source} 
                    (
                    код,
                    родитель,
                    наименование,
                    комментарий, 
                    федеральный_код,
                    альтернативный_код
                    )
                    VALUES(:1, :2, :3, :4, :5, :6)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->код,
                                        $this->родитель,
                                        $this->наименование,
                                        $this->комментарий,
                                        $this->федеральный_код,
                                        $this->альтернативный_код
                                        );
        }
        catch (MysqlException $e) {
            $m = Message::getInstance();
            $m->enque_message('error', $e->code);
        }
    }

}

?>