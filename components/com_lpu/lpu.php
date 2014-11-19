<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'components'.DS.'component_acl.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'delete_items.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );
require_once ( 'model' . DS . 'lpu_query.php' );
require_once ( 'model' . DS . 'lpu_save.php' );
require_once ( 'model' . DS . 'tax_lpu_save.php' );
require_once ( 'model' . DS . 'tax_query.php' );
require_once ( MZPATH_BASE .DS.'components'.DS.'com_territory'.DS.'model' .DS. 'territory_query.php' );
require_once ( 'views' . DS . 'lpu_list.php' );
require_once ( 'views' . DS . 'lpu_item.php' );
require_once ( 'views' . DS . 'lpu_subordinate.php' );
require_once ( 'views' . DS . 'tax_lpu_item.php' );
require_once ( 'views' . DS . 'taxes_lpu_list.php' );
require_once ( MODULES . DS . 'mod_user'  . DS . 'acl.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'access_list.php' );
require_once ( COMPONENTS . DS . 'com_users' . DS . 'views' . DS . 'user_list.php' );

class LPU extends ComponentACL
{
    
// основные данные об учреждении здравоохранения
    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_edit()
    {
        $lpu = (array)Request::getVar('lpu');
        if (!$lpu[0]) {
            Message::error('Запись для редактирования не определена!');
            $this->view_list();
        }
        else {
            $this->view_edit_item($lpu[0]);
        }
    }
    
    protected function exec_cancel()
    {
        $this->view_list();
    }
    
    protected function exec_close_lists()
    {
        $this->view_list();
    }

    protected function exec_save()
    {
        if (!$this->oid[0]) {
            $s = new LpuSave();
            $s->insert_data();
        } 
        else {
            $s = new LpuSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            Message::error('Описание учреждения не определено!');
            $this->view_list();
        } 
        $s = new LpuSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item($this->oid[0]);
    }
    
    protected function exec_delete()
    {
        $lpu = (array)Request::getVar('lpu');
        if (!$lpu[0]) {
            Message::error('Учреждение(я) не определено(ы)!');
            $this->view_list();
        }
        else {
            $lpu = new DeleteItems($lpu);
        }
        $this->view_list();
    }

// Подчинение учреждения здравоохранения (федеральное, региональное, муниципальное)
    protected function exec_subordinate()
    {
        $lpu = Request::getVar('lpu');
        if (is_array($lpu)) {
            $lpu = $lpu[0];
        }
        if (!$lpu) {
            Message::error('Учреждение не определено!');
            $this->view_list();
            return false;
        }
        $terr_id = LpuQuery::get_territory($lpu);
        if (!$terr_id) {
            $alert = 'не определено';
        }
        else {
            $t = new TerritoryQuery($terr_id);
            $alert = $t->наименование;
        }
        Message::alert("Сейчас установлена территория: {$alert}");
        Content::set_route('lpu', $lpu);
        $this->view_subordinate($lpu);
    }
    
    protected function exec_subordinate_apply()
    {
        $lpu = Request::getVar('lpu');
        if (!$lpu) {
            Message::error('Учреждение не определено!');
            $this->view_list();
            return false;
        }
        $territory = (array)Request::getVar('territory');
        if (!$territory) {
            Message::error('Не выбрана Территория!');
        }
        else {
            $link_type = Reference::get_id('подчинение', 'link_types');
            LinkObjects::set_lto1_link($territory[0], $lpu, $link_type);
            $t = new TerritoryQuery($territory[0]);
            Message::alert("Установлено подчинение: {$t->наименование}");
        }
        Content::set_route('lpu', $lpu);
        $this->view_subordinate($lpu);
    }

// Налоговая идентификация учреждения здравоохранения
    protected function exec_taxes()
    {
        $lpu = Request::getVar('lpu');
        if (is_array($lpu)) {
            $lpu = $lpu[0];
        }
        if (!$lpu) {
            Message::error('Учреждение не определено!');
            $this->view_list();
        }
        else {
            
            Content::set_route('lpu', $lpu);
            $this->view_taxes($lpu);
        }
    }
    
    protected function exec_edit_tax()
    {
        $tax = Request::getVar('tax');
        if (!$tax[0]) {
            Message::error('Запись для редактирования не определена!');
            $this->view_list();
        }
        else {
            $lpu = Request::getVar('lpu');
            $i = new LpuItem($lpu);
            $lpu_name = $i->get_name();
            Content::set_route('lpu', $lpu);
            $this->view_edit_tax($tax[0], $lpu_name);
        }
    }
    
    protected function exec_new_tax()
    {
        $lpu = Request::getVar('lpu');
        Content::set_route('lpu', $lpu);
        $this->view_new_tax();
    }
    
    protected function exec_save_tax()
    {
        if (!$this->oid[0]) {
            $s = new TaxLpuSave();
            $s->insert_data();
            $s->set_assoc();
        } 
        else {
            $s = new TaxLpuSave($this->oid[0]);
            $s->update_data();
        }
        $lpu = Request::getVar('lpu');
        if ($lpu) {
            $this->view_taxes($lpu);
        } 
        else {
            $this->view_list();
        }
    }
    
    protected function exec_close_tax_edit() 
    {
        $lpu = Request::getVar('lpu');
        if ($lpu) {
            Content::set_route('lpu', $lpu);
            $this->view_taxes($lpu);
        }
        else {
            $this->view_list();
        }
    }

    protected function exec_excel_export()
    {
        $list = new LpuList();
        $scope = Request::getVar('records');
        $cols = Request::getVar('columns');
        if ($scope == 'all') {
            $list->set_limit(0);
            
        }
        if ($cols == 'all') {

            $list->set_columns(null);
        }
        $list->get_items();
        $exp = $list->export_to_excel();
        $exp->set_title('Список ЛПУ');
        $exp->set_creator($this->user_name);
        $exp->render();
    }
    
    protected function exec_current_acl()
    {
        $lpu = (array)Request::getVar('lpu');
        if (!$lpu[0]) {
            Message::error("Документ/ы не определен");
            $this->view_list();
            return;
        }
        if (!in_array(1, $this->rights) && !in_array(60, $this->rights)) {
            Message::error("Отсутствуют права на изменение списков доступа");
            $this->view_list();
            return;
        }
        $this->oid = $lpu;
        if (count($lpu) > 1) {
            parent::exec_user_list();
            return;
        } else {
            parent::exec_current_acl();
            return;
        }
    }

// Представления данных (view)
// Данные учреждений здравоохранения
    protected function view_list()
    {
        $title = 'Список лечебно-профилактических и санаторно-курортных учреждений';
        $confirm = 'Удаление выбранных учреждений';
        $list = new LpuList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new' , 'Создать');
        $edit_b = self::set_toolbar_button('edit', 'edit' , 'Редактировать');
        $edit_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        self::set_toolbar_button('subordinate', 'subordinate' , 'Территория');
        $tax_b = self::set_toolbar_button('tax', 'taxes' , 'Налоговая идентификация');
        $tax_b->set_option('obligate', true);
        $acl_b = self::set_toolbar_button('switch', 'current_acl' , 'Доступ');
        $acl_b->set_option('obligate', true);
        self::set_toolbar_button('excel', 'excel_export' , 'Сохранить в Excel');
        ExcelExport::set_dialog($title);
        $this->set_content($list->get_items_page());
    }

    protected function view_edit_item($lpu) 
    {
        $i = new LpuItem($lpu);
        self::set_title('Редактирование данных учреждения "' . $i->get_name() . '"');
        $i->edit_item();
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        $ab = self::set_toolbar_button('apply', 'apply' , 'Применить');
        $ab->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_item() 
    {
        self::set_title('Ввод нового учреждения');
        $i = new LpuItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form);
    }
    protected function view_subordinate($lpu)
    {
        $i = new LpuQuery($lpu);
        $list = new LpuSubordinate($lpu);
        $list->set_limit(0);
        self::set_title('Выбор Территории для ЛПУ"' . $i->наименование . '"');
        $sb = self::set_toolbar_button('default', 'subordinate_apply' , 'Установить');
        $sb->set_option('obligate', true);
        self::set_toolbar_button('cancel', 'close_lists', 'Закрыть');
        $this->set_content($list->get_items_page());  
        $c = Content::getInstance();
        $c->set_modal();        
    }

//Налоговая идентификация    
    protected function view_taxes($lpu)
    {
        $i = new LpuItem($lpu);
        $list = new TaxesLpuList($lpu);
        self::set_title('Налоговая индентификация ЛПУ"' . $i->get_name() . '" ');
        self::set_toolbar_button('edit', 'edit_tax' , 'Редактировать', 'Выберите запись для редактирования');
        self::set_toolbar_button('new', 'new_tax' , 'Новая запись');
        self::set_toolbar_button('cancel', 'close_lists', 'Закрыть');
        $this->set_content($list->get_items_page());  
        $c = Content::getInstance();
        $c->set_modal();
    }
    
    protected function view_edit_tax($tax, $lpu_name)
    {
        $i = new TaxLpuItem($tax);
        self::set_title('Редактирование данных налоговой идентификации ' . $lpu_name);
        $i->edit_item();
        self::set_toolbar_button('save', 'save_tax' , 'Сохранить');    
        $cb = self::set_toolbar_button('cancel', 'close_tax_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_new_tax()
    {
        $i = new TaxLpuItem();
        self::set_title('Ввод данных налоговой идентификации');
        $i->new_item();
        self::set_toolbar_button('save', 'save_tax' , 'Сохранить');    
        $cb = self::set_toolbar_button('cancel', 'close_tax_edit' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }

}

?>