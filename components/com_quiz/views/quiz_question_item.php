<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Quize
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class QuizQuestionItem extends Item 
{
    protected $model    = 'QuizQuestionQuery';
    protected $form     = 'quiz_question_form_tmpl';

    public function get_answers() {
        $a = new QuizAnswerList($this->item);
        $a->set_show_constraint(false);
        $a->set_show_pagination(false);
        $answers_table = $a->get_items_page();
        $this->form_loader->append_section($answers_table);
    }
}



?>