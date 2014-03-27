<?php
/**
* @version		$Id: mon_consolidated_reports.php.php,v 1.0 2012/01/10 23:01:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Monitorings
* @copyright	Copyright (C) 2012 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'monitoring.php' );
require_once ( 'model' . DS . 'mon_document_query.php' );
require_once ( 'model' . DS . 'mon_documentview_query.php' );
require_once ( 'model' . DS . 'mon_consolidatedview_query.php' );
require_once ( 'model' . DS . 'mon_consolidated_insert.php' );
require_once ( 'model' . DS . 'mon_section_query.php' );
require_once ( 'model' . DS . 'mon_section_view.php' );
require_once ( 'model' . DS . 'mon_cell_store.php' );
require_once ( 'views' . DS . 'mon_consolidated_list.php' );
require_once ( 'views' . DS . 'mon_consolidated_item.php' );
require_once ( 'views' . DS . 'doc_section_list.php' );
require_once ( 'views' . DS . 'section_data_edit.php' );

class MonСonsolidatedReports extends Monitoring
{
    protected $model = 'MonConsolidatedViewQuery';

    protected function exec_save()
    {
        $pattern_id  = Request::getVar('pattern_id');
        $period_oid  = Request::getVar('period_oid');
        if (!$pattern_id || !$period_oid) {
            Message::error("Не определен шаблон документа и/или отчетный период. Сводный отчет не создан.");
            $this->view_list();
            return;
        }
        $s = new MonConsolidatedInsert($pattern_id, $period_oid);
        $this->view_list();
    }
    
    protected function exec_recalculate()
    {
        $document = Request::getVar('document');
        if (!$document) {
            Message::error('Документ не определен(ы)!');
            $this->view_list();
            return;
        }
        foreach ((array)$document as $d) {
            $doc = new MonConsolidatedViewQuery($d);
            $sect_list = new DocSectionList($d);
            $sections = $sect_list->get_items();
            $affected = MonConsolidatedInsert::data_summarization($sections, $doc->pattern_id, $doc->period_id); 
            Message::alert('Обработано разделов ' . $affected[0] . ', Ячеек ' . $affected[1]);
        }
        $this->view_list();
    }
    
    protected function exec_data_entering_cancel()
    {
       $document   = Request::getVar('document');
        if (!$document) {
            Message::error('Отчетный документ не определен!');
            $this->view_list();
        }
        else {
            $list = new DocSectionList($document);
            $restrict = $list->set_condition();
            $count = $list->items_count($restrict);
            if ($count > 1) {
                Content::set_route('document', $document);
                $this->view_sections($document);
            } 
            elseif ($count == 1) {
                $this->view_list();
            }
        }
    }

// Представления данных (view)
    protected function view_list()
    {
        $title = 'Отчетные документы';
        $confirm = 'Удаление выбранных отчетов';
        $list = new MonConsolidatedList();
        self::set_title($title);
        self::set_toolbar_button('new', 'new' , 'Создать сводный отчет');
        $edit_b = self::set_toolbar_button('tableedit', 'sections' , 'Выбор раздела/Ввод данных');
        $edit_b->set_option('obligate', true);
        $edit_b = self::set_toolbar_button('refresh', 'recalculate' , 'Повторить свод');
        $edit_b->set_option('obligate', true);
        $check_b = self::set_toolbar_button('check', 'status_change' , 'Изменить статус');
        $check_b->set_option('obligate', true);
        $this->set_status_dialog();
        $excel_b = self::set_toolbar_button('excel', 'excel_export' , 'Экспорт в Excel');
        $excel_b->set_option('obligate', true);
        $del_b = self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $del_b->set_option('obligate', true);
        DeleteItems::set_confirm_dialog($confirm);
        $this->set_content($list->get_items_page());
    }

    private function set_status_dialog()
    {
        $c = Content::getInstance();
        $df  = '<div id="radio">';
        $df .= '<input type="radio" id="radio1" name="radio" class="status" value="1" /><label for="radio1">Редактирование</label>';
        $df .= '<input type="radio" id="radio2" name="radio" class="status" value="2" /><label for="radio2">Готов к проверке</label>';
        $df .= '<input type="radio" id="radio3" name="radio" class="status" value="3" /><label for="radio3">На доработке</label>';
        $df .= '<input type="radio" id="radio4" name="radio" class="status" value="4" /><label for="radio4">Проверен</label>';
        $df .= '<input type="radio" id="radio5" name="radio" class="status" value="5" /><label for="radio5">Утвержден</label>';
        $df .= '</div>';
        $dialog_id = 'status-dialog';
        $button = 'status_change';
        $tb = Toolbar_Content::getInstance();
        $status_button = $tb->get_button($button);
        $c->set_dialog_form($df, 'Изменение статуса отчета(ов)', $dialog_id);
        $code = '$( "#' . $dialog_id . '" ).dialog( "open" );';
        $status_button->set_option('dialog', $code);
        $jradio_block = '$( "#radio" ).buttonset();';
        //if ($roles = $this->check_role()) {
          //  foreach ($roles as $r) {
                
                
            //}
      //  }

        
        $jq_block =
<<<JS
    $( "#$dialog_id" ).dialog({
        resizable: false,
        autoOpen: false,
        height: 100,
        width: 520,
        modal: true,
        buttons: {
            "Изменить статус": function() {
                $( this ).dialog( "close" );
                val = $('input[name="radio"]:checked').val();
                if (!val) {
                    return false;
                }
                $("#adminForm").append('<input type="hidden" name="new_status" value="'+val+'" />');
                $("#adminForm").submit();
                return true;
            },
            "Отменить": function() {
                $( this ).dialog( "close" );
                $("#task").val(null);
                return false;
            }
        }
    });
    $( "#radio1" ).button({ disabled: true });
JS;
        $js = Javascript::getInstance();
        $js->add_jblock($jradio_block);
        $js->add_jblock($jq_block);
        return true;
    }
    
    protected function view_new_item() 
    {
        self::set_title('Создать документ');
        $i = new MonConsolidatedItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_report_data($doc_id, $section_id) 
    {
        $doc = new $this->model($doc_id);
        $s = new SectionDataEdit($doc_id, $section_id);
        self::set_title('Мониторинг: "' . $doc->мониторинг . ', </br>Год: ' . $doc->год . ', Период: ' . $doc->наименование_периода . ' (с ' . $doc->начало . ' по ' . $doc->окончание . ')') ;
        $save_t = self::set_toolbar_button('save', 'data_saving' , 'Сохранить');
        $js_code = '$("#source").val($.statgrid.instance.exportHTML()); $("#adminForm").submit(); return true;'; //
        $save_t->set_option('dialog',  $js_code);
        $cancel_b = self::set_toolbar_button('cancel', 'data_entering_cancel' , 'Закрыть');
        $track_dirty_code = "if ($.statgrid.instance.isDirty) { if (confirm('Сделанные изменения будут потеряны')) { $('#adminForm').submit(); } else { return false; } } else { $('#adminForm').submit(); } ";
        $cancel_b->set_option('dialog', $track_dirty_code);
        $c = $s->get_content();
        $this->set_content($c);
        $c = Content::getInstance();
        $c->set_modal();
    }
    
}

?>