<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2010 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
define( 'FRMR_UPLOADS', 'C:\uploaded_files\frmr_uploads');
require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'personnel_save.php' );
require_once ( 'model' . DS . 'adapter_personnel_query.php' );
require_once ( 'model' . DS . 'personnel_document_save.php' );
require_once ( 'model' . DS . 'personnel_document_query.php' );
require_once ( 'model' . DS . 'personnel_adress_save.php' );
require_once ( COMPONENTS . DS . 'com_adress' . DS . 'model' . DS . 'adress_query.php' );
require_once ( 'model' . DS . 'personnel_award_query.php' );
require_once ( 'model' . DS . 'personnel_award_save.php' );
require_once ( 'model' . DS . 'personnel_education_save.php' );
require_once ( 'model' . DS . 'personnel_education_query.php' );
require_once ( 'model' . DS . 'personnel_posteducation_query.php' );
require_once ( 'model' . DS . 'personnel_posteducation_save.php' );
require_once ( 'model' . DS . 'personnel_qualcategory_query.php' );
require_once ( 'model' . DS . 'personnel_qualcategory_save.php' );
require_once ( 'model' . DS . 'personnel_retrainment_query.php' );
require_once ( 'model' . DS . 'personnel_retrainment_save.php' );
require_once ( 'model' . DS . 'personnel_record_save.php' );
require_once ( 'model' . DS . 'personnel_record_query.php' );
require_once ( COMPONENTS . DS . 'com_lpu' . DS . 'model' . DS . 'lpu_query.php' );
require_once ( 'views' . DS . 'personnel_list.php' );
require_once ( 'views' . DS . 'personnel_item.php' );
require_once ( 'views' . DS . 'personnel_document_list.php' );
require_once ( 'views' . DS . 'personnel_document_item.php' );
require_once ( 'views' . DS . 'personnel_adress_list.php' );
require_once ( 'views' . DS . 'personnel_adress_item.php' );
require_once ( 'views' . DS . 'personnel_award_list.php' );
require_once ( 'views' . DS . 'personnel_award_item.php' );
require_once ( 'views' . DS . 'personnel_education_list.php' );
require_once ( 'views' . DS . 'personnel_education_item.php' );
require_once ( 'views' . DS . 'personnel_posteducation_list.php' );
require_once ( 'views' . DS . 'personnel_posteducation_item.php' );
require_once ( 'views' . DS . 'personnel_qualcategory_list.php' );
require_once ( 'views' . DS . 'personnel_qualcategory_item.php' );
require_once ( 'views' . DS . 'personnel_retrainment_list.php' );
require_once ( 'views' . DS . 'personnel_retrainment_item.php' );
require_once ( 'views' . DS . 'personnel_record_list.php' );
require_once ( 'views' . DS . 'personnel_record_item.php' );
require_once ( MODULES . DS . 'mod_staffconv' . DS . 'frmr_import.php' );
require_once ( MODULES . DS . 'mod_staffconv' . DS . 'frmr_upload_form.php' );
require_once ( MODULES . DS . 'mod_staffconv' . DS . 'frmr_upload_file_save.php' );

class Personnel extends Component
{
    protected $default_view = 'view_personnel_list';
    
    protected function exec_close_lists()
    {
        $this->view_personnel_list();
    }

// Основные данные о сотруднике
    protected function exec_personnel_list()
    {
        $this->view_personnel_list();
    }

    protected function exec_new_personnel()
    {
        $this->view_new_personnel();
    }
    
    protected function exec_edit_personnel()
    {
        $personnel = Request::getVar('personnel');
        if (!$personnel[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $this->view_edit_personnel($personnel[0]);
        }
    }
    
    protected function exec_apply_personnel()
    {
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        } 
        else {
            $s = new PersonnelSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_edit_personnel($this->oid[0]);
    }

    protected function exec_save_personnel()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelSave();
            $s->insert_data();
        } 
        else {
            $s = new PersonnelSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_personnel_list();
    }
    
    protected function exec_cancel_personnel_edit() 
    {
        $this->view_personnel_list();
    }

// Документы, удостоверяющие личность медработника
    protected function exec_personnel_document_list()
    {
        $personnel = Request::getVar('personnel');
        if (!$personnel[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel[0]);
            $this->view_personnel_document_list($personnel[0]);
        }
    }

    protected function exec_new_personnel_document()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_document($fio);
    }
    
    protected function exec_edit_personnel_document()
    {
        $document = Request::getVar('document');
        if (!$document[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_document($document[0], $fio);
        }
    }
    
    protected function exec_cancel_personnel_document_list() 
    {
        $this->view_personnel_list();
    }
    
    protected function exec_cancel_personnel_document_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_document_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_apply_personnel_document()
    {
        $personnel = Request::getVar('personnel');
        if (!$this->oid[0]) {
            Message::error('Запись для редактирования не определена!');
            $this->view_personnel_document_list($personnel);
        } 
        else {
            $s = new PersonnelDocumentSave($this->oid[0]);
            $s->update_data();
        }
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_edit_personnel_document($this->oid[0], $fio);
    }
    
    protected function exec_save_personnel_document()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelDocumentSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelDocumentSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            Content::set_route('personnel', $personnel);        
            $this->view_personnel_document_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    // Адреса проживания сотрудника ЛПУ
    protected function exec_personnel_adress_list()
    {
        $personnel = Request::getVar('personnel');
        if (!$personnel[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel[0]);
            $this->view_personnel_adress_list($personnel[0]);
        }
    }

    protected function exec_new_personnel_adress()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_adress($fio);
    }
    
    protected function exec_edit_personnel_adress()
    {
        $adress = Request::getVar('adress');
        if (!$adress[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_adress($adress[0], $fio);
        }
    }
    
    protected function exec_cancel_personnel_adress_list() 
    {
        $this->view_personnel_list();
    }
    
    protected function exec_cancel_personnel_adress_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_adress_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_apply_personnel_adress()
    {
        $personnel = Request::getVar('personnel');
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_adress_list($personnel);
        } 
        else {
            $s = new PersonnelAdressSave($this->oid[0]);
            $s->update_data();
        }
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_edit_personnel_adress($this->oid[0], $fio);
    }

    protected function exec_save_personnel_adress()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelAdressSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelAdressSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            Content::set_route('personnel', $personnel);        
            $this->view_personnel_adress_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
// Награды и поощрения сотрудника
    protected function exec_personnel_award_list()
    {
        $personnel = Request::getVar('personnel');
        if (!$personnel[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel[0]);
            $this->view_personnel_award_list($personnel[0]);
        }
    }
    
    protected function exec_cancel_personnel_award_edit()
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_award_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }

    protected function exec_new_personnel_award()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_award($fio);
    }
 
    protected function exec_edit_personnel_award()
    {
        $award = Request::getVar('award');
        if (!$award[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_award($award[0], $fio);
        }
    }
    
        protected function exec_apply_personnel_award()
    {
        $personnel = Request::getVar('personnel');
        if (!$this->oid[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_award_list($personnel);
        } 
        else {
            $s = new PersonnelAwardSave($this->oid[0]);
            $s->update_data();
        }
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_edit_personnel_award($this->oid[0], $fio);
    }
   
    protected function exec_save_personnel_award()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelAwardSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelAwardSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            Content::set_route('personnel', $personnel);
            $this->view_personnel_award_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }

// Высшее и среднеспециальное образование медработников
    protected function exec_personnel_education_list()
    {
        $personnel = Request::getVar('personnel');
        if (is_array($personnel)) {
            $personnel = $personnel[0];
        }
        if (!$personnel) {
            Message::error('Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel);
            $this->view_personnel_education_list($personnel);
        }
    }
    
    protected function exec_edit_personnel_education()
    {
        $education = Request::getVar('education');
        if (!$education[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            if (is_array($personnel)) {
                $personnel = $personnel[0];
            }
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_education($education[0], $fio);
        }
    }
    
    protected function exec_new_personnel_education()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_education($fio);
    }
    
    protected function exec_save_personnel_education()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelEducationSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelEducationSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_education_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_education_list() 
    {
        $this->view_personnel_list();
    }
    
    protected function exec_cancel_personnel_education_edit() 
    {
        $personnel = Request::getVar('personnel');
        if (is_array($personnel)) {
            $personnel = $personnel[0];
        }
        if ($personnel) {
            $this->view_personnel_education_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
// Последипломное образование
    protected function exec_personnel_posteducation_list()
    {
        $personnel = Request::getVar('personnel');
        if (is_array($personnel)) {
            $personnel = $personnel[0];
        }
        if (!$personnel) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel);
            $this->view_personnel_posteducation_list($personnel);
        }

    }
    
    protected function exec_new_personnel_posteducation()
    {
        $this->view_new_personnel_posteducation();
    }
    
    protected function exec_edit_personnel_posteducation()
    {
        $posteducation = Request::getVar('posteducation');
        if (!$posteducation[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel[0]);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel[0]);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_posteducation($posteducation[0], $fio);
        }
    }

    protected function exec_save_personnel_posteducation()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelPostEducationSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelPostEducationSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_posteducation_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_posteducation_list()
    {
        $lpu = Request::getVar('lpu');
        if ($lpu) {
            $this->view_personnel_list($lpu);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_posteducation_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_posteducation_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }

// Квалификационные категории медработника
    protected function exec_personnel_qualcategory_list()
    {
        $personnel = Request::getVar('personnel');
        if (is_array($personnel)) {
            $personnel = $personnel[0];
        }
        if (!$personnel) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel);
            $this->view_personnel_qualcategory_list($personnel);
        }
    }
    
    protected function exec_new_personnel_qualcategory()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_qualcategory($fio);
    }

    protected function exec_edit_personnel_qualcategory()
    {
        $category = Request::getVar('category');
        if (!$category[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel[0]);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel[0]);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_qualcategory($category[0], $fio);
        }
    }

    protected function exec_save_personnel_qualcategory()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelQualCategorySave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelQualCategorySave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_qualcategory_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_qualcategory_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_qualcategory_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }

// Пройденные медработником курсы переподготовки
    protected function exec_personnel_retrainment_list()
    {
        $personnel = Request::getVar('personnel');
        if (is_array($personnel)) {
            $personnel = $personnel[0];
        }
        if (!$personnel) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel);
            $this->view_personnel_retrainment_list($personnel);
        }
    }

    protected function exec_new_personnel_retrainment()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        $this->view_new_personnel_retrainment($fio);
    }
    
    protected function exec_edit_personnel_retrainment()
    {
        $retrainment = Request::getVar('retrainment');
        if (!$retrainment[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_retrainment($retrainment[0], $fio);
        }
    }

    protected function exec_save_personnel_retrainment()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelRetrainmentSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelRetrainmentSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_retrainment_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_retrainment_list()
    {
        $lpu = Request::getVar('lpu');
        if ($lpu) {
            $this->view_personnel_list($lpu);
        } 
        else {
            $this->view_personnel_list();
        }
    }

    protected function exec_cancel_personnel_retrainment_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_retrainment_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }

// Запись в личном деле медработника (занимаемая должность и т.д.)
    protected function exec_personnel_record_list()
    {
        $personnel = Request::getVar('personnel');
        if (!$personnel[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            Content::set_route('personnel', $personnel[0]);
            $this->view_personnel_record_list($personnel[0]);
        }
    }
    
    protected function exec_edit_personnel_record()
    {
        $record = Request::getVar('record');
        if (!$record[0]) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Запись для редактирования не определена!');
            $this->view_personnel_list();
        }
        else {
            $personnel = Request::getVar('personnel');
            $i = new PersonnelItem($personnel);
            $fio = $i->get_name();
            $pers_id = $i->get_pers_id();
            Content::set_route('personnel', $personnel);
            Content::set_route('human', $pers_id);
            $this->view_edit_personnel_record($record[0], $fio);
        }
    }
    
    protected function exec_new_personnel_record()
    {
        $personnel = Request::getVar('personnel');
        $i = new PersonnelItem($personnel);
        $fio = $i->get_name();
        $pers_id = $i->get_pers_id();
        $lpu_id = $i->get_lpu_id();
        Content::set_route('personnel', $personnel);
        Content::set_route('human', $pers_id);
        Content::set_route('lpu', $lpu_id);
        $this->view_new_personnel_record($fio);
    }
    
    protected function exec_save_personnel_record()
    {
        if (!$this->oid[0]) {
            $s = new PersonnelRecordSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new PersonnelRecordSave($this->oid[0]);
            $s->update_data();
        }
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_record_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_record_list() 
    {
        $lpu = Request::getVar('lpu');
        if ($lpu) {
            $this->view_personnel_list($lpu);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
    protected function exec_cancel_personnel_record_edit() 
    {
        $personnel = Request::getVar('personnel');
        if ($personnel) {
            $this->view_personnel_record_list($personnel);
        } 
        else {
            $this->view_personnel_list();
        }
    }
    
// импорт данных из формата ФРМР
    protected function exec_upload_xml_form()
    {
        $this->view_upload_form($this->oid[0]);
    }

    protected function exec_upload_frmr_save()
    {
        $lpu = Request::getVar('lpu_id');
        if (!$lpu) {
            Message::error('Учреждение не определено!');
            $this->view_personnel_list();
        }
        try {
            $uploaded = new FrmrUploadFileSave();
            $uploaded->save_file();
        }
        catch (UploadException $e) {
            Message::error($e->message . 'Код ошибки ' . $e->code);
            $this->view_personnel_list($lpu);
        }
        $this->view_check_uploaded($lpu, $uploaded->get_uploaded_name());
    }
    
    protected function exec_save_imported_records()
    {
        $lpu = Request::getVar('lpu_id');
        if (!$lpu) {
            Message::error('Учреждение не определено!');
            $this->view_personnel_list();
        }
        $uid = Request::getVar('uid');
        if (!$uid) {
            Message::error('Не выбрано ни одного медработника для импорта данных!');
        }
        $file = Request::getVar('file');
        if (!$file) {
            Message::error('Не определен файл для импорта данных!');
        }
        try {
            $f = new FrmrImport($file, $lpu);
            $cnt = $f->import_employee_reestr($uid);
        }
        catch (UploadException $e) {
            Message::error($e->message . 'Код ошибки ' . $e->code);
        }
        Message::alert("Импортировано $cnt записей" );
        $this->view_personnel_list($lpu);
    }
    
    protected function exec_cancel_import() 
    {
        $this->view_personnel_list();
    }

// Представления данных (view)
// Список сотрудников УЗ
    protected function view_personnel_list()
    {
        $list = new PersonnelList();
        self::set_title('Сотрудники учреждений здравоохранения Иркутской области ');
        self::set_toolbar_button('new', 'new_personnel' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        $doc_b = self::set_toolbar_button('document', 'personnel_document_list' , 'Документы', 'Выберите сотрудника');
        $doc_b->set_option('obligate', true);
        $adr_b = self::set_toolbar_button('send', 'personnel_adress_list' , 'Адреса', 'Выберите сотрудника');
        $adr_b->set_option('obligate', true);
        $award_b = self::set_toolbar_button('award', 'personnel_award_list' , 'Награды', 'Выберите сотрудника');
        $award_b->set_option('obligate', true);
        $rec_b = self::set_toolbar_button('records', 'personnel_record_list' , 'Личное дело', 'Выберите сотрудника');
        $rec_b->set_option('obligate', true);
        $edu_b = self::set_toolbar_button('education', 'personnel_education_list' , 'Образование', 'Выберите сотрудника');
        $edu_b->set_option('obligate', true);
        self::set_toolbar_button('upload', 'upload_xml_form' , 'Загрузка ФРМР');
        $this->set_content($list->get_items_page());
    }
    protected function view_new_personnel()
    {
        $i = new PersonnelItem();
        self::set_title('Ввод данных сотрудника');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel($personnel)
    {
        $i = new PersonnelItem($personnel);
        self::set_title('Изменение данных сотрудника');
        $i->edit_item();
        $ab = self::set_toolbar_button('apply', 'apply_personnel' , 'Применить');
        $ab->validate(true);
        $sb = self::set_toolbar_button('save', 'save_personnel' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Список документов сотрудника
    protected function view_personnel_document_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelDocumentList($personnel);
        self::set_title('Документы сотрудника "' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_document' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_document' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'cancel_personnel_document_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }

    protected function view_new_personnel_document($name)
    {
        $i = new PersonnelDocumentItem();
        self::set_title('Ввод данных документа сотрудника "' . $name .'"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_document' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_document_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_document($document, $name)
    {
        $i = new PersonnelDocumentItem($document);
        self::set_title('Редактирование данных документа сотрудника "' . $name .'"');
        $i->edit_item();
        $ab = self::set_toolbar_button('apply', 'apply_personnel_document', 'Применить');
        $ab->validate(true);
        $sb = self::set_toolbar_button('save', 'save_personnel_document', 'Сохранить');
        $sb->validate(true);        
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_document_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
// Адреса проживания сотрудников
    protected function view_personnel_adress_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelAdressList($personnel);
        self::set_title('Адреса проживания сотрудника "' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_adress' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_adress' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'cancel_personnel_adress_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_new_personnel_adress($name)
    {
        $i = new PersonnelAdressItem();
        self::set_title('Ввод данных документа сотрудника "' . $name .'"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_adress' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_adress_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_adress($document, $name)
    {
        $i = new PersonnelAdressItem($document);
        self::set_title('Редактирование данных документа сотрудника "' . $name .'"');
        $i->edit_item();
        $ab = self::set_toolbar_button('apply', 'apply_personnel_adress', 'Применить');
        $ab->validate(true);
        $sb = self::set_toolbar_button('save', 'save_personnel_adress', 'Сохранить');
        $sb->validate(true);        
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_adress_edit', 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
// Награды и поощрения сотрудников
    protected function view_personnel_award_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelAwardList($personnel);
        self::set_title('Награды сотрудника "' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_award' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_award' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'close_lists' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_new_personnel_award($name)
    {
        $i = new PersonnelAwardItem();
        self::set_title('Ввод данных о наградах и поощрениях сотрудника "' . $name .'"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_award' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_award_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_award($award, $name)
    {
        $i = new PersonnelAwardItem($award);
        self::set_title('Редактирование данных о наградах и поощрениях сотрудника "' . $name .'"');
        $i->edit_item();
        $ab = self::set_toolbar_button('apply', 'apply_personnel_award', 'Применить');
        $ab->validate(true);
        $sb = self::set_toolbar_button('save', 'save_personnel_award' , 'Сохранить');
        $sb->validate(true);        
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_award_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Список оконченных уч. заведений + специальности по диплому сотрудника
    protected function view_personnel_education_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelEducationList($personnel);
        self::set_title('Специальности по диплому сотрудника"' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_education' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_education' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('education', 'personnel_posteducation_list' , 'Послевузовское Образование');
        self::set_toolbar_button('education', 'personnel_qualcategory_list' , 'Квалификационные категории');
        self::set_toolbar_button('education', 'personnel_retrainment_list' , 'Переподготовка');
        self::set_toolbar_button('cancel', 'cancel_personnel_education_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_edit_personnel_education($education, $name)
    {
        $i = new PersonnelEducationItem($education);
        self::set_title('Редактирование данных об образовании сотрудника "' . $name . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_education' , 'Сохранить');   
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_education_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_personnel_education($name)
    {
        $i = new PersonnelEducationItem();
        self::set_title('Ввод данных об образовании сотрудника "' . $name . '"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_education' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_education_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Послевузовское образование
    protected function view_personnel_posteducation_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelPostEducationList($personnel);
        self::set_toolbar_button('new', 'new_personnel_posteducation' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_posteducation' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_title('Послевузовское образование сотрудника"' . $i->get_name() . '" ');
        self::set_toolbar_button('education', 'personnel_education_list' , 'Образование');
        self::set_toolbar_button('education', 'personnel_qualcategory_list' , 'Квалификационные категории'); 
        self::set_toolbar_button('education', 'personnel_retrainment_list' , 'Переподготовка');
        self::set_toolbar_button('cancel', 'cancel_personnel_posteducation_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }    

    protected function view_new_personnel_posteducation()
    {
        $i = new PersonnelPostEducationItem();
        self::set_title('Ввод данных о послевузовском образовании сотрудника');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_posteducation' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_posteducation_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_posteducation($posteducation, $name)
    {
        $i = new PersonnelPostEducationItem($posteducation);
        self::set_title('Редактирование данных о последипломном образовании сотрудника "' . $name . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_posteducation' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_posteducation_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Квалификационные категории медработника
    protected function view_personnel_qualcategory_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelQualCategoryList($personnel);
        self::set_title('Квалификационные категории сотрудника"' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_qualcategory' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_qualcategory' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('education', 'personnel_education_list' , 'Образование');
        self::set_toolbar_button('education', 'personnel_posteducation_list' , 'Послевузовское Образование');
        self::set_toolbar_button('education', 'personnel_retrainment_list' , 'Переподготовка');
        self::set_toolbar_button('cancel', 'cancel_personnel_posteducation_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }    

    protected function view_new_personnel_qualcategory()
    {
        $i = new PersonnelQualCategoryItem();
        self::set_title('Ввод данных о квалификационных категориях, присвоенных сотруднику');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_qualcategory' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_qualcategory_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_qualcategory($category, $name)
    {
        $i = new PersonnelQualCategoryItem($category);
        self::set_title('Редактирование данных о квалификационной категории сотрудника"' . $name . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_qualcategory' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_qualcategory_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Пройденные медработником курсы переподготовки

    protected function view_personnel_retrainment_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelRetrainmentList($personnel);
        self::set_title('Курсы по переподготовке, пройденные сотрудником"' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_retrainment' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_retrainment' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('education', 'personnel_education_list' , 'Образование');
        self::set_toolbar_button('education', 'personnel_posteducation_list' , 'Послевузовское Образование');
        self::set_toolbar_button('education', 'personnel_qualcategory_list' , 'Квалификационные категории'); 
        self::set_toolbar_button('cancel', 'cancel_personnel_retrainment_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }

    protected function view_new_personnel_retrainment($name)
    {
        $i = new PersonnelRetrainmentItem();
        self::set_title('Ввод данных по переподготовке, пройденной сотрудником "' . $name . '"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_retrainment' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_retrainment_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_personnel_retrainment($retrainment, $name)
    {
        $i = new PersonnelRetrainmentItem($retrainment);
        self::set_title('Редактирование данных по переподготовке, пройденной сотрудником "' . $name . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_retrainment' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_retrainment_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Список должностей занимаемых сотрудником УЗ
    protected function view_personnel_record_list($personnel)
    {
        $i = new PersonnelItem($personnel);
        $list = new PersonnelRecordList($personnel);
        self::set_title('Должности, занимаемые сотрудником "' . $i->get_name() . '" ');
        self::set_toolbar_button('new', 'new_personnel_record' , 'Новая запись');
        $edit_b = self::set_toolbar_button('edit', 'edit_personnel_record' , 'Редактировать', 'Выберите запись для редактирования');
        $edit_b->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'cancel_personnel_record_list' , 'Закрыть');
        $this->set_content($list->get_items_page());
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_edit_personnel_record($record, $name)
    {
        $i = new PersonnelRecordItem($record);
        self::set_title('Редактирование данных должностях, занимаемых сотрудником "' . $name . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_record' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_record_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_personnel_record($name)
    {
        $i = new PersonnelRecordItem();
        self::set_title('Ввод данных о должностях, занимаемых сотрудником "' . $name . '"');
        $i->new_item();
        $sb = self::set_toolbar_button('save', 'save_personnel_record' , 'Сохранить');    
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel_personnel_record_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

// Загрузка XML файлов в формате ФРМР в регистр медработников
    protected function view_upload_form($lpu = null)
    {
        $c = Content::getInstance();
        $c->set_modal();
        self::set_title('Импорт данных из формата ФРМР');
        self::set_toolbar_button('upload', 'upload_frmr_save' , 'Загрузить');
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $u = new FrmrUploadForm();
        $this->set_content($u->get_form());
    }
    
    protected function view_check_uploaded($lpu, $uploaded)
    {
        $c = Content::getInstance();
        $c->set_modal();
        self::set_title('Импорт данных из формата ФРМР');
        self::set_toolbar_button('save', 'save_imported_records' , 'Импортировать выбранные записи');
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $f = new FrmrImport($uploaded, $lpu);
        $file_info = $f->check_employee_reestr();
        $this->set_content($file_info);
    }
}

?>