<?php
/**
* @version      $Id $
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2010-2014 МИАЦ ИО
*/

// Верхний уровень 
define( '_MZEXEC', 1 );
define( 'MZPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( MZPATH_BASE .DS.'includes'.DS.'factory.php' );

$mainframe = new MZFactory();
$mainframe->check_auth();
$mainframe->get_content();

?>