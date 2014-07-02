<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class MonDocumentItem extends Item 
{
    protected $model    = 'MonDocumentQuery';
    protected $form     = 'mon_document_form_tmpl';
    
    public function set_js() 
    {
        $js = Javascript::getInstance();
        $list1 = '<option value=""></option><option value="1">январь</option><option value="2">февраль</option><option value="3">март</option><option value="5">апрель</option><option value="6">май</option><option value="7">июнь</option><option value="10">июль</option><option value="11">август</option><option value="12">сентябрь</option><option value="14">октябрь</option><option value="15">ноябрь</option><option value="16">декабрь</option>';
        $list2 = '<option value=""></option><option value="20">первый месяц</option><option value="21">два месяца</option><option value="22">три месяца</option><option value="23">четыре месяца</option><option value="24">пять месяцев</option><option value="25">шесть месяцев</option><option value="26">семь месяцев</option><option value="27">восемь месяцев</option><option value="28">девять месяцев</option><option value="29">десять месяцев</option><option value="30">одиннадцать месяцев</option><option value="31">двенадцать месяцев</option>';
        $list3 = '<option value=""></option><option value="4">I квартал</option><option value="8">II квартал</option><option value="9">I полугодие</option><option value="13">III квартал</option><option value="17">IV квартал</option><option value="18">II полугодие</option><option value="19">годовой</option>';
        $code = 
<<<JS
$(function(){
    $("#period").focusin(function() {
        if ($("#period_id").hasClass('old')) {
            return;
        }
        v = $("#btext_dic_mon_patterns").text();
        if (!v || v=='Выберите значение') {
            $(this).html('<option value="">Выберите мониторинг и шаблон отчета</option>'); 
            return;
        }
        reg1 = 'за текущий период';
        reg2 = 'с накопительным итогом';
        if (v.indexOf(reg1) != -1) {
            $(this).html('{$list1}'); 
        }
        if (v.indexOf(reg2) != -1) {
            $(this).html('{$list2}');
        }
    }); 
});
JS;
        $js->add_jstree();
        $js->add_jblock($code);
    }
}

?>