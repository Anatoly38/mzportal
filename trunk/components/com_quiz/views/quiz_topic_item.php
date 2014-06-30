<?php
/**
* @version      $Id: quiz_topic_item.php,v 1.0 2014/05/28 13:46:30 shameev Exp $
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