<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class PersonnelRecordQuery extends ClActiveRecord
{
    protected $source = 'pasp_personnel_record';
    public $oid;
    public $вид_должности;
    public $тип_должности;
    public $должность;
    public $ставка;
    public $дата_начала_труд_отношений;
    public $тип_записи_начало;
    public $номер_приказа_начало;
    public $дата_окончания_труд_отношений;
    public $тип_записи_окончание;
    public $номер_приказа_окончание;
    public $режим_работы;
    public $военная_служба;
    public $подразделение;
    public $название_подразделения;
    public $вид_мп;
    public $условия_мп;
    public $население;

    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT
                        a.oid,
                        a.вид_должности,
                        a.тип_должности,
                        a.должность,
                        a.ставка,
                        a.дата_начала_труд_отношений,
                        a.тип_записи_начало,
                        a.номер_приказа_начало,
                        a.дата_окончания_труд_отношений,
                        a.тип_записи_окончание,
                        a.номер_приказа_окончание,
                        a.режим_работы,
                        a.военная_служба,
                        a.подразделение,
                        a.название_подразделения,
                        a.вид_мп,
                        a.условия_мп,
                        a.население
                    FROM {$this->source} AS a
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Запись не существует");
        }
        $this->oid                              = $oid;
        $this->вид_должности                    = $data['вид_должности'];
        $this->тип_должности                    = $data['тип_должности'];
        $this->должность                        = $data['должность'];
        $this->ставка                           = $data['ставка'];
        $this->дата_начала_труд_отношений       = $data['дата_начала_труд_отношений'];
        $this->тип_записи_начало                = $data['тип_записи_начало'];
        $this->номер_приказа_начало             = $data['номер_приказа_начало'];
        $this->дата_окончания_труд_отношений    = $data['дата_окончания_труд_отношений'];
        $this->тип_записи_окончание             = $data['тип_записи_окончание'];
        $this->номер_приказа_окончание          = $data['номер_приказа_окончание'];
        $this->режим_работы                     = $data['режим_работы'];
        $this->военная_служба                   = $data['военная_служба'];
        $this->подразделение                    = $data['подразделение'];
        $this->название_подразделения           = $data['название_подразделения'];
        $this->вид_мп                           = $data['вид_мп'];
        $this->условия_мп                       = $data['условия_мп'];
        $this->население                        = $data['население'];
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
        $obj->name = $this->должность;
        $obj->description = 'Занимаемая сотрудником УЗ должность';
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source}
                    (
                     oid,
                     вид_должности,
                     тип_должности,
                     должность,
                     ставка,
                     дата_начала_труд_отношений,
                     тип_записи_начало,
                     номер_приказа_начало,
                     дата_окончания_труд_отношений,
                     тип_записи_окончание,
                     номер_приказа_окончание,
                     режим_работы,
                     военная_служба,
                     подразделение,
                     название_подразделения,
                     вид_мп,
                     условия_мп,
                     население
                    )
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16, :17, :18)";
        $dbh = new DB_mzportal;
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute  (
                                            $this->oid,
                                            $this->вид_должности,
                                            $this->тип_должности,
                                            $this->должность,
                                            $this->ставка,
                                            $this->дата_начала_труд_отношений,
                                            $this->тип_записи_начало,
                                            $this->номер_приказа_начало,
                                            $this->дата_окончания_труд_отношений,
                                            $this->тип_записи_окончание,
                                            $this->номер_приказа_окончание,
                                            $this->режим_работы,
                                            $this->военная_служба,
                                            $this->подразделение,
                                            $this->название_подразделения,
                                            $this->вид_мп,
                                            $this->условия_мп,
                                            $this->население
                                            );
            $m->enque_message('alert', 'Изменения при редактировании данных о должности сотрудника успешно сохранены');
        }
        catch (MysqlException $e) {
            $m->enque_message('error', $e->code);
        }
    }

    public function update()
    {
        if(!$this->oid)
        {
            throw new Exception("Для вызова update() необходим код документа сотрудника");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE
                        {$this->source}
                    SET
                        вид_должности                   = :1,
                        тип_должности                   = :2,
                        должность                       = :3,
                        ставка                          = :4,
                        дата_начала_труд_отношений      = :5,
                        тип_записи_начало               = :6,
                        номер_приказа_начало            = :7,
                        дата_окончания_труд_отношений   = :8,
                        тип_записи_окончание            = :9,
                        номер_приказа_окончание         = :10,
                        режим_работы                    = :11,
                        военная_служба                  = :12,
                        подразделение                   = :13,
                        название_подразделения          = :14,
                        вид_мп                          = :15,
                        условия_мп                      = :16,
                        население                       = :17
                     WHERE
                        oid = :18";
        $m = Message::getInstance();
        try {
            $dbh->prepare($query)->execute(
                                        $this->вид_должности,
                                        $this->тип_должности,
                                        $this->должность,
                                        $this->ставка,
                                        $this->дата_начала_труд_отношений,
                                        $this->тип_записи_начало,
                                        $this->номер_приказа_начало,
                                        $this->дата_окончания_труд_отношений,
                                        $this->тип_записи_окончание,
                                        $this->номер_приказа_окончание,
                                        $this->режим_работы,
                                        $this->военная_служба,
                                        $this->подразделение,
                                        $this->название_подразделения,
                                        $this->вид_мп,
                                        $this->условия_мп,
                                        $this->население,
                                        $this->oid
                                        );
            $m->enque_message('alert', 'Изменения при редактировании данных о должности сотрудника успешно сохранены');
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения при редактированиии данных о должности сотрудника не сохранены!' . $e->getMessage());
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = $this->должность;
            $obj->description = 'Занимаемая сотрудником УЗ должность';
            $obj->update();
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: изменения <object> не сохранены!');
            return false;
        }
    }

    public static function get_card($oid)
    {
        $link = Reference::get_id('должность', 'link_types');
        $data = LinkObjects::get_parents($oid, $link);
        if (is_array($data)) {
            return($data[0]);
        }
    }
}

?>