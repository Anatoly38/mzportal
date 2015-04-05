<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class AttestCabUserItem extends Item 
{
    protected $model    = 'AttestCabUserQuery';
    protected $form     = 'atest_cab_user_form_tmpl';
}

?>