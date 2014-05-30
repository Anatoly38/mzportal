<?php
/** 
* @version		$Id: excel_export.php,v 1.0 2014/05/28 14:50:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Excel module
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО
Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );

require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'excel'.DS.'PHPExcel'.DS.'IOFactory.php' );

class ExcelExport 
{
    private $title = false;
    private $pExcel;
    private $aSheet;
    private $returned_file;
    
    public function __construct($file_name = 'exel_doc.xlsx') 
    {
        $this->pExcel = new PHPExcel();
        $this->pExcel->setActiveSheetIndex(0);
        $this->aSheet = $this->pExcel->getActiveSheet();
        $this->returned_file = $file_name;
    }
    
    public function set_title($title = null)
    {
        $this->pExcel->getProperties()->setTitle($title);
    }
    
    public function set_creator($creator = null)
    {
        $this->pExcel->getProperties()->setCreator($creator);
    }
    
    public function set_properties($subject = null, $description = null, $creator = null)
    {
        $this->pExcel->getProperties()->setCreator()
                        ->setSubject($subject)
                        ->setDescription($description);
    }
    
    public function set_title_row($titles = null)
    {
        if (!$titles) {
            return;
        }
        $i = 0;
        foreach ($titles as $t) {
            $this->aSheet->setCellValueByColumnAndRow($i, 1, $t['title']);
            $i++;
        }
    }
    
    public function load_data($spreadshit_data = null)
    {
        if (!$spreadshit_data) {
            throw new Exception("Данные для экспорта в формат Excel не получены");
        }
        $i = 2;
        $titles = array_shift($spreadshit_data);
        foreach ($spreadshit_data as $row) {
            $j = 0;
            foreach ($row as $col => $value) {
                $this->aSheet->setCellValueByColumnAndRow($j, $i, $value);
                $j++;
            }
            $i++;
        }
        $this->set_title_row($titles);
    }
    
    public static function set_dialog($title)
    {
        $c = Content::getInstance();
        $df  = '<fieldset>';
        $df .= '<h3>Вывести записи</h3>';
        $df .= '<label for="records">Вcе</label>';
        $df .= '<input type="radio" name="records" id="records" class="text ui-widget-content ui-corner-all" value="all" checked="checked"/>';
        $df .= '<label for="records">Только текущие</label>';
        $df .= '<input type="radio" name="records" id="records" class="text ui-widget-content ui-corner-all" value="current"/><br/>';
        $df .= '<h3>Колонки</h3>';
        $df .= '<label for="columns">Вcе</label>';
        $df .= '<input type="radio" name="columns" id="columns" class="text ui-widget-content ui-corner-all" value="all" checked="checked"/>';
        $df .= '<label for="columns">Только текущие</label>';
        $df .= '<input type="radio" name="columns" id="columns" class="text ui-widget-content ui-corner-all" value="current"/><br/>';
        $df .= '<h3>Формат файла</h3>';        
        $df .= '<label for="format">Excel 2007</label>';
        $df .= '<input type="radio" name="format" id="format" value="excel2007" class="text ui-widget-content ui-corner-all" checked="checked" />';
        //$df .= '<label for="format">Excel 2003</label>';
        //$df .= '<input type="radio" name="format" id="format" value="excel2003" class="text ui-widget-content ui-corner-all" />';
        $df .= '</fieldset>';
        $dialog_id = 'excel-dialog';
        $button = 'excel_export';
        $tb = Toolbar_Content::getInstance();
        $excel_button = $tb->get_button($button);
        $c->set_dialog_form($df, $title, $dialog_id);
        $code = "var pos = Array ( $( \"#$button\" ).offset().left, $( \"#$button\" ).offset().top + $( \"#$button\" ).outerHeight() );";
        $code .= '$( "#' . $dialog_id . '" ).dialog( "option" , "position" , pos );';
        $code .= '$( "#' . $dialog_id . '" ).dialog( "open" );';
        $excel_button->set_option('dialog', $code);
        $jq_block =
<<<JS
    $( "#$dialog_id" ).dialog({
        resizable: false,
        autoOpen: false,
        height: 300,
        width: 270,
        modal: true,
        buttons: {
            "Вывести список": function() {
                $( this ).dialog( "close" );
                val1 = $("#records:checked").val();
                val2 = $("#columns:checked").val();
                $("#adminForm").append('<input type="hidden" name="records" value="'+val1+'" />');
                $("#adminForm").append('<input type="hidden" name="columns" value="'+val2+'" />');
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
JS;
        $js = Javascript::getInstance();
        $js->add_jblock($jq_block);
        return true;
    }

    public function render()
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->pExcel, 'Excel2007');
        ob_end_clean();
        ini_set('zlib.output_compression','Off'); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$this->returned_file.'"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit();
    }
}
?>
