<?php
/**
* @version		$Id: lpu_query.php,v 1.1 2009/12/05 00:50:30 shameev Exp $
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

class LPU_Query 
{
    public $oid;
    public $основное; // Логическое -  Идентификация основное учреждение (1), входящее в состав другого (0) 
    public $код_территории;
    public $почтовый_адрес;
    public $фактический_адрес;
    public $руководитель;
    public $наименование;
    public $налоговая_идентификация; // Идентификация налогоплательщика в органах налоговой инспекции
    public $дата_создания;
    public $дата_ликвидации;
    public $население; // Прикрепленное население
    public $номенклатура; 
    public $категория;
    public $возрастная_группа;
    public $крр; // Коэффициент районного регулирования тарифов
    public $дополнительно; // Дополнительные данные
    
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.код_территории, 
                        a.почтовый_адрес, 
                        a.фактический_адрес,
                        a.руководитель,
                        a.наименование,
                        a.налоговая_идентификация,
                        a.дата_создания,
                        a.дата_ликвидации,
                        a.население,
                        a.номенклатура,
                        a.категория,
                        a.возрастная_группа,
                        a.крр,
                        a.дополнительно
                    FROM `пасп_лпу` AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Показатель не существует");
        }
        $this->oid = $oid;
        $this->код_территории = $data['код_территории'];
        $this->почтовый_адрес = $data['почтовый_адрес'];
        $this->фактический_адрес = $data['фактический_адрес'];
        $this->руководитель = $data['руководитель'];
        $this->наименование = $data['наименование'];
        $this->налоговая_идентификация = $data['налоговая_идентификация'];
        $this->дата_создания = $data['дата_создания'];
        $this->дата_ликвидации = $data['дата_ликвидации'];
        $this->население = $data['население'];
        $this->номенклатура = $data['номенклатура'];
        $this->категория = $data['категория'];
        $this->возрастная_группа = $data['возрастная_группа'];
        $this->крр = $data['крр'];
        $this->дополнительно = $data['дополнительно'];
    }

    public static function findByName($name = null)
    {
        if (!name) {
            throw new Exception("Имя учреждения для поиска не определено");
        }
        $dbh = new DB_mzportal;
        $query = "SELECT oid FROM пасп_лпу WHERE наименование = :1";
        list($id) = $dbh->prepare($query)->execute($name)->fetch_row();
        if(!$id) {
            throw new Exception("Код учреждения не найден");
        }
        return new Uchr_Zdrav($id);
    }

	public function update() 
    {
	    if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код учреждения");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE 
                        `пасп_лпу` 
                    SET 
                        код_территории = :1, 
                        почтовый_адрес = :2, 
                        фактический_адрес = :3,
                        руководитель = :4,
                        наименование = :5,
                        налоговая_идентификация = :6,
                        дата_создания = :7,
                        дата_ликвидации = :8,
                        население = :9,
                        номенклатура = :10,
                        категория = :11,
                        возрастная_группа = :12,
                        крр = :13,
                        дополнительно = :14
                     WHERE 
                        oid = :15"; 
        $dbh->prepare($query)->execute( 
                                        $this->код_территории, 
                                        $this->почтовый_адрес,
                                        $this->фактический_адрес, 
                                        $this->руководитель,
                                        $this->наименование, 
                                        $this->налоговая_идентификация,
                                        $this->дата_создания, 
                                        $this->дата_ликвидации,
                                        $this->население,
                                        $this->номенклатура,
                                        $this->категория,
                                        $this->возрастная_группа,
                                        $this->крр,
                                        $this->дополнительно,
                                        $this->oid
                                        );
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
        $obj->name = $class_name . ' obj';
        $obj->description = $this->наименование;
        $obj->deleted = 0;
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO `пасп_лпу`
                    (oid, код_территории, почтовый_адрес, фактический_адрес, руководитель, наименование, 
                    налоговая_идентификация, дата_создания, дата_ликвидации)
                    VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $obj->obj_id,
                                        $this->код_территории, 
                                        $this->почтовый_адрес,
                                        $this->фактический_адрес, 
                                        $this->руководитель,
                                        $this->наименование, 
                                        $this->налоговая_идентификация,
                                        $this->дата_создания, 
                                        $this->дата_ликвидации,
                                        $this->население,
                                        $this->номенклатура,
                                        $this->категория,
                                        $this->возрастная_группа,
                                        $this->крр,
                                        $this->дополнительно
                                        );
        }
        catch (MysqlException $e) {
            $m =& Message::getInstance();
            $m->enque_message('error', $e->code);
        }
        
    }
 
    public function delete()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код не определен, удаление не возможно");
        }
        $query = "DELETE FROM `пасп_лпу` WHERE oid = :1";
        $dbh = new DB_mzportal;
        $dbh->prepare($query)->execute($this->oid);
    }
    
    public function get_as_array()
    {
        if(!$this->oid) 
        {
            throw new Exception("Код учреждения не определен");
        }
        $fields = array();
        foreach($this as $key => $value) {
            $fields[$key] = $value;
        }
        return $fields;
    }
   
}

?>