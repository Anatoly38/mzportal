<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_list.php' );

class DossierProfile
{
    private $dossier_id;
    private $text;
    private $page_breake = false;
    
    public function __construct($id = false)
    {
        if (!$id) {
            throw new Exception("Не определен идентификатор аттестационного дела");
        }
        $this->dossier_id = $id;
    }
    
    public function show_title($title)
    {
        $t = '<h2>' . $title . '</h2>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_dossier()
    {
        $d_obj = new DossierQuery($this->dossier_id);
        $t = 'Номер дела: ' . $d_obj->номер_дела . '<br/>';
        $t .= 'ФИО соискателя: ' . $d_obj->фио . '<br/>';
        $t .= 'Email: ' . $d_obj->email . '<br/>';
        $t .= 'Медицинская организация: ' . Reference::get_name($d_obj->мо, 'subordination') . '<br/>';
        $t .= 'Экспертная группа: ' . Reference::get_name($d_obj->экспертная_группа, 'expert_groups') . '<br/>';
        $t .= 'Вид должности: ' . Reference::get_name($d_obj->вид_должности, 'position_short') . '<br/>';
        $this->add_text($t);
        return $t;
    }
    
    public function show_cab_user()
    {
        try {
            $d_obj = new DossierCabQuery($this->dossier_id);
            $t = 'Логин: ' . $d_obj->name . '<br/>';
            $t .= 'Пароль: ' . $d_obj->pwd . '<br/>';
        } 
        catch (Exception $e) {
            $t = 'Логин и пароль не установлены<br/>';
        }

        $this->add_text($t);
        return $t;
    }
    
    public function show_quiz_attempts()
    {
        $list = new DossierTicketList($this->dossier_id);
        $tickets = $list->get_items();
        if (count($tickets) > 0) {
            $t = '<table>';
                $t .= '<tr>';
                $t .= '<th>№</th>';
                $t .= '<th>Тема</th>';
                $t .= '<th>Параметры</th>';
                $t .= '<th>Попытка реализована</th>';
                $t .= '</tr>';        
            $i = 1;
            foreach ($tickets as $ticket) {
                $t .= '<tr>';
                $t .= '<td>' . $i++ . '</td>';
                $t .= '<td>' . Reference::get_name($ticket->тема, 'quiz_topics') . '</td>';
                $t .= '<td>' . Reference::get_name($ticket->настройка, 'quiz_settings') . '</td>';
                $t .= '<td>' . Reference::get_name($ticket->реализована, 'bool') . '</td>';
                $t .= '</tr>';
            }
            $t .= '</table>';            
        }
        else {
            $t = 'Попытки тестирования не предоставлены';
        }
        $this->add_text($t);
        return $t;
    }
    
    public function show_hints()
    {
        $t = '1. Адрес сайта - ЛК аттестуемого: http://quiz.miac-io.ru <br/>';            
        $t .= '2. По вопросам работы с тестами обращаться по т. (3952)280-325 <br/>';
        $t .= '   или по электронной почте attest@miac-io.ru<br/>';
        $t .= '3. В аттестационное дело будет включен ПЕРВЫЙ сданный на положительную оценку тест. Оставшиеся попытки аннулируются.<br/>';
        $this->add_text($t);
        return $t;
    }
    
    public function set_pagebreake($p = true)
    {
        $this->page_breake = $p;
    }
    
    public function add_text($t)
    {
        $this->text .= $t;
    }
    
    public function get_text()
    {
        $all =  '';
        $this->page_breake ? $all .= '<div style="page-break-after:always;">' :  $all .= '';
        $all .= $this->text;
        $this->page_breake ? $all .= '</div>' :  $all .= '';
        return $all;
    }
  
}
?>