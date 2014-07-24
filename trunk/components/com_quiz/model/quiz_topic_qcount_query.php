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

class QuizTopicViewQuery extends ClActiveRecord 
{
    protected $source = 'quiz_topic_countquestion';
    public $oid;
    public $название_темы;
    public $описание_темы;
    public $аттестуемая_специальность;
    public $экспертная_группа;
    public $количество_вопросов;
    
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
                        a.экспертная_группа,
                        a.количество_вопросов
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
        $this->количество_вопросов      = $data['количество_вопросов'];
    }

    public function update() 
    {
    }
    
    public function insert()
    {
    } 
}
?>