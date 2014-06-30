<?php
/**
* @version      $Id: quiz_question_item.php,v 1.0 2014/06/11 13:46:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class QuizQuestionItem extends Item 
{
    protected $model    = 'QuizQuestionQuery';
    protected $form     = 'quiz_question_form_tmpl';
}

?>