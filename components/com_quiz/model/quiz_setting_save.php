<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class QuizSettingSave extends ItemSave
{
    protected $model = 'QuizSettingQuery';
    
    public function get_post_values()
    {
        $this->query->наименование           = Request::getVar('наименование');
        $this->query->основная_тема          = Request::getVar('основная_тема');
        $this->query->доп_тема1_наименование = Request::getVar('доп_тема1_наименование');
        $this->query->доп_тема1_доля         = Request::getVar('доп_тема1_доля');
        $this->query->доп_тема2_наименование = Request::getVar('доп_тема2_наименование');
        $this->query->доп_тема2_доля         = Request::getVar('доп_тема2_доля');
        $this->query->доп_тема3_наименование = Request::getVar('доп_тема3_наименование');
        $this->query->доп_тема3_доля         = Request::getVar('доп_тема3_доля');
        $this->query->доп_тема4_наименование = Request::getVar('доп_тема4_наименование');
        $this->query->доп_тема4_доля         = Request::getVar('доп_тема4_доля');
        $this->query->количество_вопросов    = Request::getVar('количество_вопросов');
        $this->query->продолжительность_теста= Request::getVar('продолжительность_теста');
        $this->query->сортировка             = Request::getVar('сортировка');
        $show = Request::getVar('показ_ответов');
        if (isset($show)) {
            $this->query->показ_ответов      = Request::getVar('показ_ответов');
        }
        else {
            $this->query->показ_ответов = 0;
        }
        
    }
}
?>