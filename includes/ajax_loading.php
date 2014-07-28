<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2010-2014 МИАЦ ИО
*/

// на время разработки отображаем все ошибки
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// Верхний уровень 
define( '_MZEXEC', 1 );
define( 'MZPATH_BASE', '/home/wwwroot/default/mzportal' );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( 'factory.php' );

$mainframe = new MZFactory();
$mainframe->check_auth();
print_r($_POST);
?>