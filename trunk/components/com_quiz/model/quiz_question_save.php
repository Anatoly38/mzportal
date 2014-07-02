<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class QuizQuestionSave extends ItemSave
{
    protected $model = 'QuizQuestionQuery';
    
    public function get_post_values()
    {
        $this->query->название_темы             = Request::getVar('текст_вопроса');
        $this->query->описание_темы             = Request::getVar('тип_вопроса');
    }
}
?>