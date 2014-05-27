<?php
/**
* @version		$Id: doc_pattern.php,v 1.0 2014/05/23 15:13:51 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
define( 'PATTERNS', MZPATH_BASE .DS. 'templates' .DS. 'doc_patterns' );

require_once ( MZPATH_BASE .DS.'components'.DS.'component.php' );
require_once ( 'model' .DS. 'doc_pattern_query.php' );
require_once ( 'model' .DS. 'doc_pattern_save.php' );
require_once ( 'model' .DS. 'doc_dp_section_query.php' );
require_once ( 'model' .DS. 'doc_dp_section_save.php' );
require_once ( 'model' .DS. 'doc_dp_section_delete.php' );
require_once ( 'model' .DS. 'excel_upload_file_save.php' );
require_once ( 'model' .DS. 'excel_template_import.php' );
require_once ( 'views' .DS. 'doc_pattern_list.php' );
require_once ( 'views' .DS. 'doc_pattern_item.php' );
require_once ( 'views' .DS. 'doc_dp_section_list.php' );
require_once ( 'views' .DS. 'doc_dp_section_item.php' );
require_once ( 'views' .DS. 'doc_dp_section_template_text.php' );
require_once ( 'views' .DS. 'section_template_visual_edit.php' );
require_once ( 'views' .DS. 'excel_upload_form.php' );
require_once ( PATTERNS .DS. 'xml_sheet.php' );

class DocPattern extends Component
{

    protected function exec_new()
    {
        $this->view_new_item();
    }
    
    protected function exec_cancel()
    {
        $this->view_list();
    }

    protected function exec_edit()
    {
        $pattern = Request::getVar('pattern');
        if (!$pattern[0]) {
            Message::error('Описание документа не определено!');
            $this->view_list();
        }
        else {
            $this->view_edit_item($pattern[0]);
        }
    }

    protected function exec_save()
    {
        if (empty($this->oid[0])) {
            $s = new DocPatternSave();
            $s->insert_data();
        } 
        else {
            $s = new DocPatternSave($this->oid[0]);
            $s->update_data();
        }
        $this->view_list();
    }
    
    protected function exec_apply()
    {
        if (!$this->oid[0]) {
            Message::error('Описание документа не определено!');
            $this->view_list();
        } 
        $s = new DocPatternSave($this->oid[0]);
        $s->update_data();
        $this->view_edit_item();
    }
    
    protected function exec_section_list()
    {
        $pattern = Request::getVar('pattern');
        if (is_array($pattern)) {
            $pattern = $pattern[0];
        }
        if (!$pattern) {
            Message::error('Документ не определен!');
            $this->view_list();
        }
        else {
            Content::set_route('pattern', $pattern);
            $this->view_section_list($pattern);
        }
    }
    
    protected function exec_section_edit()
    {
        $section = Request::getVar('section');
        if (is_array($section)) {
            $section = $section[0];
        }
        $pattern = Request::getVar('pattern');
        if (!$section[0]) {
            Message::error('Раздел документа не определен');
            $this->view_list();
        }
        else {
            Content::set_route('pattern', $pattern);
            Content::set_route('section', $section);
            $this->view_section_edit_item($section);
        }
    }
    
    protected function exec_section_new()
    {
        $pattern = Request::getVar('pattern');
        if (!$pattern) {
            Message::error('Документ не определен!');
            $this->view_list();
        }
        Content::set_route('pattern', $pattern);
        $this->view_section_new_item($pattern);
    }

    protected function exec_section_save()
    {
        $pattern = Request::getVar('pattern');
        $section = Request::getVar('section');
        if (empty($section)) {
            $s = new DocDpSectionSave();
            $s->insert_data();
        } 
        else {
            $s = new DocDpSectionSave($section);
            $s->update_data();
        }
        if (!$pattern) {
            Message::error('Описание документа не определено!');
            $this->view_list();
            return true;
        }
        Content::set_route('pattern', $pattern);
        $this->view_section_list($pattern);
    }

    protected function exec_section_delete()
    {
        $section = Request::getVar('section');
        $pattern = Request::getVar('pattern');
        if (!$pattern) {
            Message::error('Раздел документа не определен');
        }
        else {
            $d = new DocDpSectionDelete($section);
        }
        Content::set_route('pattern', $pattern);
        $this->view_section_list($pattern);
    }

    protected function exec_template_text_edit()
    {
        $section = (array)Request::getVar('section');
        $pattern = Request::getVar('pattern');
        if (!$section[0]) {
            Message::error('Раздел документа не определен');
            $this->view_section_list($pattern);
        }
        else {
            Content::set_route('pattern', $pattern);
            Content::set_route('section', $section[0]);
            $this->template_text_edit($section[0]);
        }
    }
    
    protected function exec_template_text_save()
    {
        $section = Request::getVar('section');
        $pattern = Request::getVar('pattern');
        $pattern_source = Request::getVar('шаблон');
        $data = new DocDpSectionQuery($section);
        $data->шаблон = $pattern_source;
        $data->save_template();
        Content::set_route('pattern', $pattern);
        $this->view_section_list($pattern);
    }
    
    protected function exec_template_visual_edit()
    {
        $section = Request::getVar('section');
        $pattern = Request::getVar('pattern');
        if (!$section[0]) {
            Message::error('Раздел документа не определен');
            $this->view_section_list($pattern);
        }
        else {
            Content::set_route('section', $section[0]);
            Content::set_route('pattern', $pattern);
            Content::set_route('pattern_source', ''); 
            $this->template_visual_edit($section[0]);
        }
    }

    protected function exec_build_new_table()
    {
        $section = Request::getVar('section');
        $rows = (int)Request::getVar('row');
        $cols = (int)Request::getVar('col');
        $blank = XmlSheet::create_blank_sheet($rows, $cols);
        $data = new DocDpSectionQuery($section[0]);
        $data->шаблон = $blank;
        $data->save_template();
        $this->template_visual_edit($section);
    }

    protected function exec_load_excel()
    {
        $section = (array)Request::getVar('section');
        $pattern = Request::getVar('pattern');
        if (!$section[0]) {
            Message::error('Раздел документа не определен');
            $this->view_section_list($pattern);
        }
        else {
            Content::set_route('pattern', $pattern);
            Content::set_route('section', $section[0]);
            $this->upload_excel();
        }
    }    

    protected function exec_save_table()
    {
        $section = Request::getVar('section');
        $pattern = Request::getVar('pattern');
        $pattern_source = Request::getVar('pattern_source');
        $data = new DocDpSectionQuery($section);
        $data->шаблон = $pattern_source;
        $data->save_template();
        Content::set_route('pattern', $pattern);
        $this->view_section_list($pattern);
    }
    
    protected function exec_section_edit_cancel()
    {
        $pattern = Request::getVar('pattern');
        if (!$pattern) {
            Message::error('Описание документа не определено!');
            $this->view_list();
            return true;
        }
        Content::set_route('pattern', $pattern);
        $this->view_section_list($pattern);
    }
    
    protected function exec_section_list_cancel()
    {
        $this->view_list();
    }

    protected function exec_uploaded_excel_save()
    {
        $section = Request::getVar('section');
        $pattern = Request::getVar('pattern');
        if (!$section) {
            Message::error('Не определен шаблон раздела!');
            $this->view_section_list($pattern);
        }
        try {
            $uploaded = new ExcelUploadFileSave();
            $f = $uploaded->save_file();
            $i = new ExcelTemplateImport($f, $section);
            $ret = $i->excel_convert();
            Message::alert('Импортировано '. $ret['cells'] . ' ячеек');
            Content::set_route('pattern', $pattern);
            Content::set_route('section', $section);
            $this->template_text_edit($section);
        }
        catch (UploadException $e) {
            Message::error($e->message . 'Код ошибки ' . $e->code);
            $this->view_section_list($pattern);
        }
    }
    
    protected function exec_cancel_import()
    {
        $pattern = Request::getVar('pattern');
        $this->view_section_list($pattern);
    }
    
    protected function view_list()
    {
        $list = new DocPatternList();
        self::set_title('Список описаний отчетных документов');
        self::set_toolbar_button('new', 'new' , 'Создать');
        self::set_toolbar_button('edit', 'edit' , 'Редактировать метаданные');        
        self::set_toolbar_button('edit', 'section_list' , 'Разделы');        
        self::set_toolbar_button('delete', 'delete' , 'Удалить');
        $this->set_content($list->get_items_page());
    }

    protected function view_new_item() 
    {
        self::set_title('Создание нового описания');
        $i = new DocPatternItem();
        $i->new_item(); 
        $sb = self::set_toolbar_button('save', 'save' , 'Сохранить');
        $sb->validate(true);
        $cb = self::set_toolbar_button('cancel', 'cancel' , 'Закрыть');
        $cb->track_dirty(true);
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function view_edit_item($pattern)
    {
        $i = new DocPatternItem($pattern);
        self::set_title('Редактирование описания "' . $i->get_name() . '"');
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
    
    protected function view_section_list($pattern)
    {
        $list = new DocDpSectionList($pattern);
        self::set_title('Список разделов (форм) шаблона документа');
        self::set_toolbar_button('new', 'section_new' , 'Создать');
        $edit_b = self::set_toolbar_button('edit', 'section_edit' , 'Редактировать метаданные');
        $edit_b->set_option('obligate', true);        
        self::set_toolbar_button('edit', 'template_text_edit' , 'Редактировать шаблон (текст)');
        //self::set_toolbar_button('edit', 'template_visual_edit' , 'Редактировать шаблон (лист)');
        self::set_toolbar_button('delete', 'section_delete' , 'Удалить');
        self::set_toolbar_button('cancel', 'section_list_cancel' , 'Закрыть');
        $this->set_content($list->get_items_page());
    }

    protected function view_section_edit_item($section)
    {
        $i = new DocDpSectionItem($section);
        self::set_title('Редактирование раздела "' . $i->get_name() . '"');
        $i->edit_item();
        self::set_toolbar_button('save', 'section_save' , 'Сохранить');
        self::set_toolbar_button('delete', 'section_delete' , 'Удалить'); 
        self::set_toolbar_button('cancel', 'section_edit_cancel' , 'Закрыть');
        $form = $i->get_form();
        $this->set_content($form); 
    }
    
    protected function view_section_new_item($pattern)
    {
        self::set_title('Создание нового раздела');
        $i = new DocDpSectionItem();
        $i->new_item(); 
        self::set_toolbar_button('save', 'section_save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'section_edit_cancel' , 'Закрыть');        
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function template_text_edit($section)
    {
        $i = new DocDpSectionTemplateText($section);
        $i->edit_item();
        self::set_title('Редактирование шаблона раздела документа');
        self::set_toolbar_button('save', 'template_text_save' , 'Сохранить');
        self::set_toolbar_button('cancel', 'section_edit_cancel' , 'Закрыть');
        self::set_toolbar_button('upload', 'load_excel' , 'Загрузить из Excel');
        $form = $i->get_form();
        $this->set_content($form);
    }
    
    protected function template_visual_edit($section)
    {
        $data = new DocDpSectionQuery($section);
        $text = $data->get_template_text();
        $i = new SectionTemplateVisualEdit($text);
        self::set_title('Редактирование шаблона раздела документа');
        if ($i->new) {
            self::set_toolbar_button('save', 'build_new_table' , 'Сохранить');
        }
        else {
            $save_t = self::set_toolbar_button('save', 'save_table' , 'Сохранить');
            $js_code = '$("#pattern_source").val($.sheet.instance[0].HTMLtoCompactSource($($.sheet.instance[0].exportSheet.xml())[0]));$("#adminForm").submit();return true;';
            $save_t->set_option('dialog',  $js_code);
        }
        $cancel_b = self::set_toolbar_button('cancel', 'section_edit_cancel' , 'Закрыть');
        $track_dirty_code = "if ($.sheet.instance[0].isDirty) { 
                                if (confirm('Сделанные изменения будут потеряны')) {
                                    $('#adminForm').submit();
                                } else {
                                    return false;
                                } } else { $('#adminForm').submit(); } ";
        $cancel_b->set_option('dialog', $track_dirty_code);
        $c = $i->get_content();
        $this->set_content($c);
    }
    
    protected function upload_excel()
    {
        $c = Content::getInstance();
        $c->set_modal();
        self::set_title('Импорт данных для шаблона из формата Excel');
        self::set_toolbar_button('upload', 'uploaded_excel_save' , 'Загрузить');
        self::set_toolbar_button('cancel', 'cancel_import' , 'Закрыть');
        $u = new ExcelUploadForm();
        $this->set_content($u->get_form());

    
    }

}
?>