<?php
/**
* @version      $Id:$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class QuizAnswerSave extends ItemSave
{
    protected $model = 'QuizAnswerQuery';
    
    public function get_post_values()
    {
        $this->query->текст_ответа  = Request::getVar('текст_ответа');
        $this->query->правильный    = Request::getVar('правильный');
    }
}
?>