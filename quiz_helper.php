<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2010-2014 МИАЦ ИО
*/

// Верхний уровень 
/* 
1. Нужно принять номер билета.
2. Нужно принять номер дела.
3. Прописать в попытке - использована 
4. Убрать - в процессе
5. Рассчитать балл и оценку
6. Ассоциировать попытку и результат тестирования 

*/
define( '_MZEXEC', 1 );
define( 'MZPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( MZPATH_BASE .DS.'includes'.DS.'factory.php' );

$mainframe = MZFactory::getInstance();
$mainframe->set_application(54);
$mainframe->check_auth();
$mainframe->get_content();

?>