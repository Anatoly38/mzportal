<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport_LPU
* @copyright    Copyright (C) 2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class LpuTempQuery extends ClActiveRecord
{
    protected $source = 'pasp_lpu_temp';
    public $номер_пп;
    public $обособленность;
    public $код_территории;
    public $огрн;
    public $код_оуз;
    public $почтовый_адрес;
    public $фактический_адрес;
    public $руководитель;
    public $главный_бухгалтер;
    public $наименование;
    public $сокращенное_наименование;
    public $опф; // Организационно-правовая форма
    public $состояние;
    public $дата_создания;
    public $дата_ликвидации;
    public $население; // Прикрепленное население
    public $уровень; 
    public $номенклатура; 
    public $категория;
    public $уровень_мп; 
    public $возрастная_группа;
    public $смп;
    public $село;
    public $дети;
    public $крр; // Коэффициент районного регулирования тарифов
    public $омс;
    public $дополнительно; // Дополнительные данные
    public $егрюл; // Дополнительные данные
    public $вэб_сайт;
    public $date1c;
    
    public function __construct($id = false)
    {
        if (!$id) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.обособленность,
                        a.код_территории,
                        a.огрн,
                        a.код_оуз,
                        a.почтовый_адрес, 
                        a.фактический_адрес,
                        a.руководитель,
                        a.главный_бухгалтер,
                        a.наименование,
                        a.сокращенное_наименование,
                        a.опф,
                        a.состояние,
                        a.дата_создания,
                        a.дата_ликвидации,
                        a.население,
                        a.уровень,
                        a.номенклатура,
                        a.категория,
                        a.уровень_мп,
                        a.возрастная_группа,
                        a.смп,
                        a.село,
                        a.дети,
                        a.крр,
                        a.омс,
                        a.дополнительно,
                        a.егрюл,
                        a.вэб_сайт,
                        a.date1c
                    FROM {$this->source} AS a 
                    WHERE номер_пп = :1";
        $data = $dbh->prepare($query)->execute($id)->fetch_assoc();
        if(!$data) {
            throw new Exception("Учреждение не существует");
        }
        $this->номер_пп = $id;
        $this->обособленность       = $data['обособленность'];
        $this->код_территории       = $data['код_территории'];
        $this->огрн                 = $data['огрн'];
        $this->код_оуз              = $data['код_оуз'];
        $this->почтовый_адрес       = $data['почтовый_адрес'];
        $this->фактический_адрес    = $data['фактический_адрес'];
        $this->руководитель         = $data['руководитель'];
        $this->главный_бухгалтер    = $data['главный_бухгалтер'];
        $this->наименование         = $data['наименование'];
        $this->сокращенное_наименование = $data['сокращенное_наименование'];
        $this->опф                  = $data['опф'];
        $this->состояние            = $data['состояние'];
        $this->дата_создания        = $data['дата_создания'];
        $this->дата_ликвидации      = $data['дата_ликвидации'];
        $this->население            = $data['население'];
        $this->уровень              = $data['уровень'];
        $this->номенклатура         = $data['номенклатура'];
        $this->категория            = $data['категория'];
        $this->уровень_мп           = $data['уровень_мп'];
        $this->возрастная_группа    = $data['возрастная_группа'];
        $this->смп                  = $data['смп'];
        $this->село                 = $data['село'];
        $this->дети                 = $data['дети'];
        $this->крр                  = $data['крр'];
        $this->омс                  = $data['омс'];
        $this->дополнительно        = $data['дополнительно'];
        $this->егрюл                = $data['егрюл'];
        $this->вэб_сайт             = $data['вэб_сайт'];
        $this->date1c               = $data['date1c'];
        
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя учреждения для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT номер_пп FROM pasp_lpu_temp WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new LpuTempQuery($id);
    }
    
    public static function findByOgrn($ogrn = null)
    {
        if (!ogrn) {
            throw new Exception("ОГРН учреждения для поиска не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT номер_пп FROM pasp_lpu_temp WHERE огрн = :1";
        list($id) = $dbh->prepare($query)->execute($ogrn)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new LpuQuery($id);
    }

    public function insert()
    {
        $query =    "INSERT INTO {$this->source} 
                    (номер_пп, обособленность, код_территории, огрн, код_оуз, почтовый_адрес, фактический_адрес, 
                    руководитель, главный_бухгалтер, наименование, сокращенное_наименование, опф,
                    состояние, дата_создания, дата_ликвидации, население, уровень, номенклатура,
                    категория, уровень_мп, возрастная_группа, смп, село, дети, крр, омс, дополнительно, егрюл, вэб_сайт, date1c)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16, :17, :18, 
                    :19, :20, :21, :22, :23, :24, :25, :26, :27, :28, :29, :30)";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute( 
                                        $this->номер_пп,
                                        $this->обособленность,
                                        $this->код_территории,
                                        $this->огрн,
                                        $this->код_оуз,
                                        $this->почтовый_адрес,
                                        $this->фактический_адрес,
                                        $this->руководитель,
                                        $this->главный_бухгалтер,
                                        $this->наименование,
                                        $this->сокращенное_наименование,
                                        $this->опф,
                                        $this->состояние,
                                        $this->дата_создания,
                                        $this->дата_ликвидации,
                                        $this->население,
                                        $this->уровень,
                                        $this->номенклатура,
                                        $this->категория,
                                        $this->уровень_мп,
                                        $this->возрастная_группа,
                                        $this->смп,
                                        $this->село,
                                        $this->дети,
                                        $this->крр,
                                        $this->омс,
                                        $this->дополнительно,
                                        $this->егрюл,
                                        $this->вэб_сайт,
                                        $this->date1c
                                        );

    }
    
    public static function truncate_table()
    {
        $query = "TRUNCATE pasp_lpu_temp";
        $dbh = new DB_mzportal;
        $dbh->execute($query);
        return true;
    }
 
}

?>