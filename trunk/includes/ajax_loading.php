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
if (is_array($answers) && count($answers) < 1) {
    echo 'Нет данных для сохранения';
    exit;
}
$json_decoded =json_decode($answers, 1);
$i = 0;
foreach($json_decoded as $answer) {
    $a = new QuizAnswerQuery((int)$answer['answerId']);
    (int)$answer['correctAnswer'] == 1 ? $a->правильный = 0 : $a->правильный = 1;
    $a->update();
    $i++;
}
echo 'Изменено вариантов ответа: ' . $i ;
?>