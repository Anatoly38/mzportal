<?php
/**
* @version      $Id $
* @package      MZPortal.Framework
* @subpackage   Quiz
* @copyright    Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'quiz_answer_query.php' );

Request::getVar('answers');
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