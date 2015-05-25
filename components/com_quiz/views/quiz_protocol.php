<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class QuizProtocol
{
    private $text;
    private $dt_obj;
    
    public function __construct($id = false)
    {
        if (!$id) {
            throw new Exception("Не определен идентификатор аттестационного дела");
        }
        $this->dt_obj = new AttestDossierTicketQuery($id);
    }
    
    public function show_title($title)
    {
        $t = '<h2>' . $title . '</h2>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_dossier()
    {
        $t = 'Номер дела: ' . $this->dt_obj->номер_дела . '<br/>';
        $t .= 'ФИО соискателя: ' . $this->dt_obj->фио . '<br/>';
        $t .= 'Email: ' . $this->dt_obj->email . '<br/>';
        $t .= 'Медицинская организация: ' . Reference::get_name($this->dt_obj->мо, 'subordination') . '<br/>';
        $t .= 'Экспертная группа: ' . Reference::get_name($this->dt_obj->экспертная_группа, 'expert_groups') . '<br/>';
        $t .= 'Вид должности: ' . Reference::get_name($this->dt_obj->вид_должности, 'position_short') . '<br/>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_ticket()
    {
        $t = 'Идентификатор попытки: ' . $this->dt_obj->oid . '<br/>';
        $t .= 'Основная тема: ' . Reference::get_name($this->dt_obj->тема, 'quiz_topics') . '<br/>';
        $t .= 'Настройка тестирования: ' . Reference::get_name($this->dt_obj->настройка, 'quiz_settings') . '<br/>';
        $t .= 'Дата и время: ' . date('d.m.Y H:s', $this->dt_obj->начало_теста)  . '<br/>';
        $d = $this->dt_obj->окончание_теста-$this->dt_obj->начало_теста;
        $t .= 'Продолжительность: ' . $d . ' сек. <br/>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_questions()
    {
        try {
            $res_obj = new QuizResult($this->dt_obj->oid);
            $quiestions = json_decode($res_obj->result);
            $t = print_r($quiestions);
        } 
        catch (Exception $e) {
            $t = 'Нет данных по пройденным вопросам теста';
        }

        $this->add_text($t);
        return $t;
    }
    
    public function add_text($t)
    {
        $this->text .= $t;
    }
    
    public function get_text()
    {
        return $this->text;
    }
  
}
?>