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

class QuizTicketSave extends ItemSave
{
    protected $model = 'QuizTicketQuery';
    
    public function get_post_values()
    {
        $this->query->тема          = Request::getVar('тема');
        $this->query->настройка     = Request::getVar('настройка');
        $this->query->пин_код       = Request::getVar('пин_код');
        $this->query->в_процессе    = Request::getVar('в_процессе');
        $this->query->текущий_вопрос = Request::getVar('текущий_вопрос');
        $this->query->реализована   = Request::getVar('реализована');
    }
}
?>