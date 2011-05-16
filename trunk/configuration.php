<?php
class MZConfig 
{
    public static $sitename             = 'МИАЦ ИО';
    public static $index                = 'index.php';
    public static $root                 = '/mzportal';
    public static $images               = '/mzportal/includes/style/images/';
    public static $list_limit           = 20; // кол-во объектов в списке по умолчанию
    public static $default_application  = '2';
    public static $default_layout       = 'main_layout.xml';
    public static $task_pane_tmpl       = 'task_view.tmpl.xml';
    public static $table_tmpl           = 'table_view.tmpl.xml';
    public static $tp_link_type         = '5' ; // тип иерархии для объектов панели задач
    public static $territory_lpu        = '4' ; // тип иерархии для территорий, МО, учреждений, обособленных подразделений
    
    // Пользователи
    public static $root_uid             = 500;
    public static $everyone_uid         = 501;
    
    // Шаблоны форм компонентов
    public static $assignment_form      = 'assignment_form.xml';
    public static $object_form_tmpl     = 'object_form.xml';
    public static $index_form_tmpl      = 'index_form.xml';
    public static $lpu_form_tmpl        = 'lpu_form.xml';
    public static $territory_form_tmpl  = 'territory_form.xml';
    public static $user_form_tmpl       = 'user_form.xml';
    public static $group_form_tmpl      = 'group_form.xml';    
    public static $task_form_tmpl       = 'task_form.xml';
    public static $frontpage_tmpl       = 'frontpage.xml';
    public static $doc_pattern_form_tmpl= 'doc_pattern_form.xml';
    public static $doc_dp_section_form_tmpl = 'doc_dp_section_form.xml';
    public static $patient_oks_form     = 'patient_oks_form.xml';
    public static $patient_onmk_form    = 'patient_onmk_form.xml';
    public static $tax_form_tmpl        = 'tax_form.xml';
    public static $template_text_edit   = 'template_text_edit.xml';
    public static $personnel_form_tmpl          = 'personnel_form.xml';
    public static $personnel_document_tmpl      = 'personnel_document_form.xml';
    public static $personnel_education_tmpl     = 'personnel_education_form.xml';
    public static $personnel_posteducation_tmpl = 'personnel_posteducation_form.xml';
    public static $personnel_qualcategory_tmpl  = 'personnel_qualcategory_form.xml';
    public static $personnel_retrainment_tmpl   = 'personnel_retrainment_form.xml';
    public static $personnel_record_tmpl        = 'personnel_record_form.xml';
}
?>