<?php 
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2010 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class Pattern2Html 
{
    private $pattern_DOMDoc;
    private $html_DOMDoc;
    private $show_titles;
    private $show_numbers;


    public function __construct($pattern, $titles = true, $numbers = true)  
    {
        if (!$pattern) {
            throw new Exception("Шаблон описания не определен");
        }
        $this->pattern_DOMDoc = new DOMDocument();
        $this->pattern_DOMDoc->preserveWhiteSpace = false;
        $this->pattern_DOMDoc->formatOutput = true;
        $this->pattern_DOMDoc->loadXML($pattern);
        $this->html_DOMDoc = new DOMDocument();
        $table_el = $this->html_DOMDoc->createElement('table');
        $table_el->setAttribute('border','1');
        $this->html_DOMDoc->appendChild($table_el);

        if ($titles ) {
            $this->show_titles = true;
            $this->set_titles('заголовкиСтолбцов', 'thead');
            $this->set_titles('заголовкиСтрок', 'tbody');
        }
        if ($numbers) {
            $this->show_numbers = true;
            $this->show_col_numbers();
            $this->show_row_numbers();
        }
        $this->left_upper_corner();
        $this->set_data_area();
    }

    private function set_titles($title_tag, $table_tag)
    {
        $thead_el = $this->html_DOMDoc->createElement($table_tag);
        $this->html_DOMDoc->firstChild->appendChild($thead_el);
        $row_titles = $this->pattern_DOMDoc->getElementsByTagName($title_tag)->item(0); 
        if ($row_titles) {
            $rows = $row_titles->getElementsByTagName('строка');
            if ($rows->length > 0) {
                $i = 0;
                foreach ($rows as $row) {
                    $cells = $row->getElementsByTagName('ячейка');
                    if ($cells->length > 0) {
                        $tr = $thead_el->appendChild($this->html_DOMDoc->createElement('tr'));
                        $tr->setAttribute('id' , $row->getAttribute('id'));
                        foreach ($cells as $cell) {
                            if ($cell->textContent) {
                                $td = $tr->appendChild($this->html_DOMDoc->createElement('td'));
                                $td->setAttribute('id', $cell->getAttribute('id'));
                                $td->appendChild($this->html_DOMDoc->createTextNode($cell->textContent));
                            }
                        }
                    }
                    $i++;
                }
            }
            // Объединение ячеек в заголовках столбцов
            $spans = $row_titles->getElementsByTagName('объединенныеЯчейки')->item(0);
            if ($spans) {
                $spaned_cells = $spans->getElementsByTagName('объединеннаяЯчейка');
                if ($spaned_cells->length > 0) {
                    foreach ($spaned_cells as $sc) {
                        $ref = $sc->getAttribute('ref');
                        $ref = explode(':', $ref );
                        $left = $ref[0]; // Левый верхний угол диапазона
                        $right = $ref[1]; // Правый нижний угол диапазона
                        $xpq ="//" . $table_tag . "/tr[@id=". substr($left, 1, 1) ."]/td[@id=". substr($left, 3, 1)."]";
                        $xpath = new DOMXpath($this->html_DOMDoc);
                        $span_node = $xpath->query($xpq)->item(0);
                        $rowspan = substr($right, 1, 1) - substr($left, 1, 1) + 1;
                        if ($rowspan) {
                            $span_node->setAttribute('rowspan', $rowspan);
                        }
                        $colspan = substr($right, 3, 1) - substr($left, 3, 1) + 1;
                        if ($colspan) {
                            $span_node->setAttribute('colspan', $colspan);
                        }
                        
                    }
                }
            }
        }
    }

    private function left_upper_corner($text = null)
    {
        $rs = 1; $cs = 1;
        $rs = $this->pattern_DOMDoc->getElementsByTagName('заголовкиСтолбцов')->item(0)->getAttribute('строки');
        $cs = $this->pattern_DOMDoc->getElementsByTagName('заголовкиСтрок')->item(0)->getAttribute('столбцы');
        if ($this->show_numbers) {
           $rs++; 
           $cs++;
        }
        $fragmentXML = '<td rowspan="' . $rs . '" colspan="' . $cs . '" >' . $text . '</td>';
        $fragment = $this->html_DOMDoc->createDocumentFragment();
        $fragment->appendXML($fragmentXML);
        $xpq ="//thead/tr[@id=1]/td[@id=1]";
        $xpath = new DOMXpath($this->html_DOMDoc);
        $upper_row = $xpath->query($xpq)->item(0);
        $upper_row->parentNode->insertBefore($fragment, $upper_row);
    }

    private function show_col_numbers()
    {
        $num = $this->pattern_DOMDoc->getElementsByTagName('областьДанных')->item(0)->getAttribute('столбцы');
        $th = $this->html_DOMDoc->getElementsByTagName('thead')->item(0);
        $tr = $th->appendChild($this->html_DOMDoc->createElement('tr'));
        for ($i = 1; $i <= $num; $i++) {
            $td = $tr->appendChild($this->html_DOMDoc->createElement('td'));
            $td->appendChild($this->html_DOMDoc->createTextNode($i));
        }
    }

    private function show_row_numbers()
    {
        $num = $this->pattern_DOMDoc->getElementsByTagName('областьДанных')->item(0)->getAttribute('строки');
        for ($i = 1; $i <= $num; $i++) {
            $xpq ="//tbody/tr[@id=". $i ."]";
            $xpath = new DOMXpath($this->html_DOMDoc);
            $row_node = $xpath->query($xpq)->item(0);
            $td = $row_node->appendChild($this->html_DOMDoc->createElement('td'));
            $td->appendChild($this->html_DOMDoc->createTextNode($i));
        }
    }

    private function set_data_area()
    {
        $data = $this->pattern_DOMDoc->getElementsByTagName('областьДанных')->item(0);
        $rows = $data->getElementsByTagName('строка');
        if ($rows->length == 0) {
            throw new Exception("Область данных не определена");
        }
        foreach ($rows as $row) {
            $row_id = $row->getAttribute('id');
            $xpq ="//tbody/tr[@id=". $row->getAttribute('id') ."]";
            $xpath = new DOMXpath($this->html_DOMDoc);
            $row_node = $xpath->query($xpq)->item(0);
            $cells = $row->getElementsByTagName('ячейка');
            foreach ($cells as $cell) {
                    $td = $row_node->appendChild($this->html_DOMDoc->createElement('td'));
                    $td->setAttribute('id', 'r' . $row_id . 'c' . $cell->getAttribute('id') . '_' . $cell->getAttribute('тип'));
            }
        }
    }

    public function render_table()
    {
        $table = $this->html_DOMDoc->getElementsByTagName('table')->item(0);
        return $table;
    }
}

?>