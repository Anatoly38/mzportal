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

class QuizTopicSave extends ItemSave
{
    protected $model = 'QuizTopicQuery';
    
    public function get_post_values()
    {
        $this->query->название_темы             = Request::getVar('название_темы');
        $this->query->описание_темы             = Request::getVar('описание_темы');
        $this->query->аттестуемая_специальность = Request::getVar('аттестуемая_специальность');
        $this->query->экспертная_группа         = Request::getVar('экспертная_группа');
    }
}
?>