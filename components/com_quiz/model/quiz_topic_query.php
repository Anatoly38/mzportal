<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class QuizTopicQuery extends ClActiveRecord 
{
    protected $source = 'quiz_topic';
    public $oid;
    public $название_темы;
    public $описание_темы;
    
    public function __construct($oid = false)
    {
        if (!$oid) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.название_темы,
                        a.описание_темы,
                        a.аттестуемая_специальность,
                        a.экспертная_группа
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Тема не существует");
        }
        $this->oid                      = $oid;
        $this->название_темы            = $data['название_темы'];
        $this->описание_темы            = $data['описание_темы'];
        $this->аттестуемая_специальность = $data['аттестуемая_специальность'];
        $this->экспертная_группа        = $data['экспертная_группа'];
    }

    public function update() 
    {
        if(!$this->oid) 
        {
            throw new Exception("Для вызова update() необходим код объекта");
        }
        $dbh = new DB_mzportal;
        $query =    "UPDATE  
                        {$this->source} 
                    SET
                        название_темы               = :1,
                        описание_темы               = :2,
                        аттестуемая_специальность   = :3,
                        экспертная_группа           = :4
                    WHERE 
                        oid = :5";
        try {
            $dbh->prepare($query)->execute( 
                                        $this->название_темы,
                                        $this->описание_темы,
                                        $this->аттестуемая_специальность,
                                        $this->экспертная_группа,
                                        $this->oid
                                        );
            Message::alert('Изменения при редактировании данных документа успешно сохранены');
        } 
        catch (Exception $e) {
            Message::error('Ошибка: изменения при редактированиии данных документа не сохранены!');
            return false;
        }
        try {
            $obj = new MZObject($this->oid);
            $obj->name = 'Тема теста';
            $obj->description = $this->название_темы;
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
        $obj->name = 'Тема тестирования';
        $obj->description = $this->название_темы;
        $obj->deleted = 0;
        if (isset($this->acl_id)) {
            $obj->acl_id = $this->acl_id;
        }
        $obj->create();
        $this->oid = $obj->obj_id;
        $query =    "INSERT INTO {$this->source} 
                    (oid, 
                    название_темы,
                    описание_темы,
                    аттестуемая_специальность,
                    экспертная_группа
                    )
                    VALUES(:1, :2, :3, :4, :5)";
        $dbh = new DB_mzportal;
        try {
            $dbh->prepare($query)->execute( 
                                        $this->oid,
                                        $this->название_темы,
                                        $this->описание_темы,
                                        $this->аттестуемая_специальность,
                                        $this->экспертная_группа
                                        );
        }
        catch (MysqlException $e) {
            Message::error($e->code);
        }
    } 
}
?>