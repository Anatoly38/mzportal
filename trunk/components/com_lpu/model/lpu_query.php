<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport_LPU
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class LpuQuery extends ClActiveRecord 
{
    protected $source = 'pasp_lpu';
    public $oid;
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
    
    public function __construct($oid = false)
    {
        if (!$oid) {
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
                        a.вэб_сайт
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Учреждение не существует");
        }
        $this->oid = $oid;
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
    }

    public static function findByName($name = null)
    {
        if (!$name) {
            throw new Exception("Имя учреждения для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM pasp_lpu WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new LpuQuery($id);
    }
    
    public static function findByOgrn($ogrn = null)
    {
        if (!$ogrn) {
            throw new Exception("ОГРН учреждения для поиска не определен");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM pasp_lpu WHERE огрн = :1";
        list($id) = $dbh->prepare($query)->execute($ogrn)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new LpuQuery($id);
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код учреждения");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        обособленность          = :1,
                        код_территории          = :2, 
                        огрн                    = :3,
                        код_оуз                 = :4, 
                        почтовый_адрес          = :5,
                        фактический_адрес       = :6,
                        руководитель            = :7,
                        главный_бухгалтер       = :8,
                        наименование            = :9,
                        сокращенное_наименование =:10,
                        опф                     = :11,
                        состояние               = :12,
                        дата_создания           = :13,
                        дата_ликвидации         = :14,
                        население               = :15,
                        уровень                 = :16,
                        номенклатура            = :17,
                        категория               = :18,
                        уровень_мп              = :19,
                        возрастная_группа       = :20,
                        смп                     = :21,
                        село                    = :22,
                        дети                    = :23,
                        крр                     = :24,
                        омс                     = :25,
                        дополнительно           = :26,
                        егрюл                   = :27,
                        вэб_сайт                = :28
                     WHERE 
                        oid                     = :29";
        try {
            $dbh->prepare($query)->execute( 
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
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании данных учреждения успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактированиии данных учреждения не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = empty($this->наименование) ? '' :   $this->наименование;
            $obj->description = empty($this->дополнительно) ? '' :  $this->дополнительно;
            $obj->update();
        }
        catch (Exception $e) {
            Message::error('Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }

    public function insert()
    {
        if($this->oid) 
        {
            throw new Exception("В объекте уже определен код, вставка невозможна");
        }
        $class_name = get_class($this);
        // Регистрация нового объекта в таблице sys_objects
        $obj = MZObject::set_class_id($class_name); // Создаем объект класса MZObject с определенной переменной $class_id
        $obj->name = empty($this->наименование) ? '' :   $this->наименование;
        $obj->description = empty($this->дополнительно) ? '' :  $this->дополнительно;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, обособленность, код_территории, огрн, код_оуз, почтовый_адрес, фактический_адрес, 
                    руководитель, главный_бухгалтер, наименование, сокращенное_наименование, опф,
                    состояние, дата_создания, дата_ликвидации, население, уровень, номенклатура,
                    категория, уровень_мп, возрастная_группа, смп, село, дети, крр, омс, дополнительно, егрюл, вэб_сайт)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16, :17, :18, 
                    :19, :20, :21, :22, :23, :24, :25, :26, :27, :28, :29)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
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
                                        $this->вэб_сайт
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    }
    
    public static function get_territory($oid)
    {
        $link = Reference::get_id('территории', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>