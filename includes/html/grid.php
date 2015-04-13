<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   HTML
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );

class HTMLGrid
{
    private $tbody;
    private $thead;
    private $tfoot;
    private $table;
    private $order;
    private $direction;
    private $limitstart;
    private $row_count;
    private $col_count;
    private $with_number = true;
    private $application;
    private $task = 'edit';
    private $object_name = 'oid';
    private $order_task = 'default';
    private $data;
    private $footer;

    public function __construct(    $data = null, 
                                    $footer = null, 
                                    $limitstart = 0,
                                    $order = null,
                                    $direction = 'asc'
                                )
    {
        if (!$data && !is_array($data)) {
            throw new Exception("Данные для таблицы не определены");
            return false;
        }
        $r = Registry::getInstance();
        $this->application = $r->application;
        $this->limitstart = $limitstart;
        $this->order = $order;
        $this->direction = $direction;
        $this->row_count = count($data);
        $this->col_count = count($data[0]);
        $this->data = $data;
        $this->footer = $footer;
    }
    
    private function set_head($data)
    {
        if (!$data) {
            return true;
        }
        $tags = '<thead>';
        $tags .= '<tr class="ui-widget-header">';
        if ($this->with_number) {
            $tags .= "<th>#</th>";
        }

        foreach ($data[0] as $column => $description) {
            if ($description['type'] == 'checkbox') {
                $tags .= '<th title="Выделить все элементы" class="select_all_rows"><span class="ui-icon ui-icon-check"></span></th>';
            } 
            else {
                if ($description['sort']) {
                    $tags .= '<th>' . $this->sort(  $description['title'], 
                                                    $description['name'], 
                                                    $this->direction, 
                                                    $this->order_task);
                    $tags .= '</th>';
                }
                else {
                    $tags .= '<th>' . $description['title'] . '</th>';
                }
            }
        }
        $tags .= '</tr>';
        $tags .= '</thead>';
        return $tags;
    }

    private function set_body($data)
    {
        $func_prefix = 'set_';
        if(!is_array($data)) {
            return;
        }
        $tags = '<tbody>';
        $n = $this->limitstart;
        $id = 0;
        for($i = 1, $c = count($data); $i < $c; ++$i) {
            $oid = $data[$i][0];
            $tags .= '<tr class="grid_row" id="' . $oid . '" name= "' . $this->object_name . '" >';
            if ($this->with_number) {
                $tags .= "<td>" . ++$n . "</td>";
                $this->col_count++;
            }
            $col_index = 0;
            foreach ($data[$i] as $key => $value) {
                $set_type_func = $func_prefix . $data[0][$col_index]['type'];
                $name = $data[0][$col_index]['name'];
                $tags .= $this->$set_type_func( $value, $name, $i, $oid );
                $col_index++;
            }
            $tags .= '</tr>';
        }
        $tags .= '</tbody>';
        return $tags;
    }

    private function sort( $title, $column, $direction = 'asc', $selected = 0, $task = null )
    {
        if (!$task) {
            $task = $this->order_task;
        }
        $direction = strtolower( $direction );
        $images = array( 'sort_asc.png', 'sort_desc.png' );
        $index = intval( $direction == 'desc' );
        $direction = ($direction == 'desc') ? 'asc' : 'desc';
        $html = '<a href="javascript:tableOrdering(\'' . $column . '\',\''.$direction.'\',\'';
        $html .= $task .'\');" title="Нажмите для сортировки по этому столбцу" >';
        $html .= $title;
        if ($column == $this->order) {
            $html .= ' <img src="' . MZConfig::$images . $images[$index] . '"/>';
        }
        $html .= '</a>';
        return $html;
    }

    private function set_plain($value, $name= null, $i = null, $oid = null) 
    {
        $tag = "<td>" . $value . "</td>";
        return $tag;
    }
    
    private function set_checkbox($value, $name= null, $i = null, $oid = null) 
    {
        $tag = '<td><span class="cb"></span></td>';  
        return $tag;        
    }
    
    private function set_radio($value, $name= null, $i = null, $oid = null) 
    {
        $tag = '<td><input id="cb' . $i; 
        $tag .= '" type="radio" onclick="isChecked(this.checked);" value="'; 
        $tag .= $value . '" name="' . $name . '"/></td>';  
        return $tag;        
    }
    
    private function set_link($value, $name= null, $i = null, $oid = null)
    {
        $tag = '<td><a href="index.php?app=' . $this->application;  
        $tag .= '&amp;task=' . $this->task . '&amp;'. $this->object_name . '[]=' . $oid .'">';
        $tag .= $value . '</a></td>';
        return $tag;
    }
    
    private function set_image($value, $name= null, $i = null, $oid = null)
    {
        $image_path = MZConfig::$images . $value . '.png';
        if($value && !file_exists(IMAGES . DS . $value . '.png')) {
            throw new Exception("Файл изображения не существует");
        }
        $tag = '<td>';  
        $tag .= '<img src="' . $image_path . '" />';
        $tag .= '</td>';
        return $tag;
    }

    private function set_footer($footer)
    {
        if (!$footer) {
            return null;
        }
        $cpan = $this->col_count + 1;
        $tags ='<tfoot>';
        $tags .='<tr>';
        $tags .='<td colspan="'. $cpan .'">';
        $tags .= $footer;
        $tags .= '</td>';
        $tags .= '</tr>';
        $tags .='</tfoot>';
        return $tags;
    }
    
    public function set_task($task = 'edit')
    {
        $this->task = $task;
        return true;
    }
    
    public function set_object_name($o_name = 'oid')
    {
        $this->object_name = $o_name;
        return true;
    }

    public function set_order_task($task)
    {
        if (!$task) {
            return;
        }
        $this->order_task = $task;
        return true;
    }
    
    private function set_grid_js()
    {
        $js = Javascript::getInstance();
        $code = 
<<<JS
$(function(){
    $(".grid_row").click( function () {
        id = $(this).attr("id");
        cb = $(this).find('.cb');
        inp_sel = 'input[value="' + id + '"]';
        if (cb.hasClass('ui-icon')) {
            $(this).removeClass('ui-state-highlight');
            $(cb).removeClass('ui-icon ui-icon-check');
            $(inp_sel).remove();
        } 
        else {
            $(this).addClass('ui-state-highlight');
            cb.addClass('ui-icon ui-icon-check');
            $("#adminForm").append('<input type="hidden" name="'+ $(this).attr("name") +'[]" value="'+ id +'" />');
        }
        l = $("td > span.ui-icon-check").length;
        if (l > 0) {
            $("#status").html('Выбрано ' + l + ' элемента(ов)');
        } else {
            $("#status").html('');
            $(".select_all_rows").removeClass('ui-state-highlight');
        }
    });
    $(".select_all_rows").click( function () {
        $(this).toggleClass('ui-state-highlight');
        if ($(this).hasClass('ui-state-highlight')) {
            $(".grid_row").each( function () {
                id = $(this).attr("id");
                cb = $(this).find('.cb');
                inp_sel = 'input[value="' + id + '"]';
                $(this).addClass('ui-state-highlight');
                cb.addClass('ui-icon ui-icon-check');
                $("#adminForm").append('<input type="hidden" name="'+ $(this).attr("name") +'[]" value="'+ id +'" />');
            });
            $("#status").html('Выбрано ' + $("td > span.ui-icon-check").length + ' элемента(ов)');
        } else {
            $(".grid_row").each( function () {
                id = $(this).attr("id");
                cb = $(this).find('.cb');
                inp_sel = 'input[value="' + id + '"]';
                $(this).removeClass('ui-state-highlight');
                $(cb).removeClass('ui-icon ui-icon-check');
                $(inp_sel).remove();
            });
            $("#status").html('');
        }
    });
});
JS;
        $code .= "$('#list_container').css('height', function() {
            t = $('#list_container').offset().top;
            f = $('#footer').height();
            h = $(window).height() - t - f - 80;
            return h;
        });
        $(window).resize(function() {
            $('#list_container').css('height', function() {
                t = $('#list_container').offset().top;
                f = $('#footer').height();
                h = $(window).height() - t - 60;
                return h;
            });
        });";
        $js->add_jblock($code);
    }

    public function render_table()
    {
        $this->set_grid_js();
        $this->tbody = $this->set_body($this->data);
        $this->thead = $this->set_head($this->data);
        $this->tfoot = $this->set_footer($this->footer);
        $table = '<input type="hidden" name="order" value="" />';
        $table .= '<input type="hidden" name="direction" value="" />';
        $table .= '<div id="list_container" class="list_container" >';
        $table .= '<table class="item_list" cellspacing="1">';
        $table .= $this->thead . $this->tbody . $this->tfoot; 
        $table .= '</table>';
        $table .= '</div>';
        return $table;
    }
}