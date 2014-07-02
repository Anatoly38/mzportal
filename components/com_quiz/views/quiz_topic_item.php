<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class QuizTopicItem extends Item 
{
    protected $model    = 'QuizTopicQuery';
    protected $form     = 'quiz_topic_form_tmpl';
}

?>