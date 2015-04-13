<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Attest
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class AttestTicketSave extends ItemSave
{
    protected $model = 'QuizTicketQuery';
}
?>