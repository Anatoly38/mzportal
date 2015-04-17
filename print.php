<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО
*/
define( '_MZEXEC', 1 );
define( 'MZPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( MZPATH_BASE .DS.'includes'.DS.'factory.php' );

$mainframe = new MZFactory();
    // Проверяем авторизован ли пользователь
$mainframe->check_auth();
$mainframe->get_layout(MZConfig::$print_layout);
$mainframe->set_content();
$mainframe->set_css(MZConfig::$print_style);
$mainframe->render();
echo $mainframe->site;

?>