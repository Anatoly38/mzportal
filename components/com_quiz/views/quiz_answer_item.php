<?php
/**
* @version      $Id:$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class QuizAnswerItem extends Item 
{
    protected $model    = 'QuizAnswerQuery';
    protected $form     = 'quiz_answer_form_tmpl';

}

?>