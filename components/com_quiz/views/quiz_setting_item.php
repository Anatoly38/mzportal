<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class QuizSettingItem extends Item 
{
    protected $model    = 'QuizSettingQuery';
    protected $form     = 'quiz_setting_form_tmpl';
}



?>