<?php
/**
* @version      $Id: index.php,v 1.11 2014/05/11 08:24:33 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Factory
* @copyright    Copyright (C) 2010-2014 МИАЦ ИО
*/

// на время разработки отображаем все ошибки
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// Верхний уровень 
define( '_MZEXEC', 1 );
define( 'MZPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( MZPATH_BASE .DS.'includes'.DS.'factory.php' );

$mainframe = new MZFactory();
    // Проверяем авторизован ли пользователь
$mainframe->check_auth();
    // Загружаем основной шаблон
$mainframe->get_layout();
    // Отображаем текущего пользователя и завершение сессии
$mainframe->set_user_pane();
    // Загружаем приложение
$mainframe->set_content();
    // Устанавливаем текущее приложение
$mainframe->set_route();
    // Загружаем сообщения
$mainframe->set_message_queue();
    // Загружаем панель инструментов
$mainframe->set_toolbar();
    // Определяем заголовок страницы
$mainframe->set_title();
    // Загружаем панель задач (иерархия объектов)
//$mainframe->set_task_pane();
    // Загрузка javascript'ов
$mainframe->set_javascripts();
    // Загрузка стилей
$mainframe->set_css();
    // Сводим воедино
$mainframe->render();
    // Отправляем на вывод
echo $mainframe->site;
//$r = Registry::getInstance();
//print_r($r);
//$s = SessionStorage::getInstance();
//print_r($s);
//$c = Constraint::getInstance();
//print_r($c);
//echo '<br />Сессия: ';
print_r($_SESSION);
//print_r($_POST);
//print_r($_FILES);
?>