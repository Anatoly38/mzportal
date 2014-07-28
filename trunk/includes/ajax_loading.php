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
require_once ( MZPATH_BASE . DS . 'components' . DS . 'com_quiz' . DS . 'model' . DS . 'quiz_answer_query.php' );

$mainframe = new MZFactory();
$mainframe->check_auth();
$answers = $_POST['answers'];
$json_decoded =json_decode($answers, 1);
//var_dump($json_decoded);
//$dbh = new DB_mzportal();
$i = 1;
foreach($json_decoded as $answer) {
    $a = new QuizAnswerQuery((int)$answer['answerId']);
    $a->правильный = (int)$answer['correctAnswer'];
    $a->update();
}
?>