<?php
/**
* @version		$Id: section_template_visual_edit.php,v 1.0 2011/05/04 15:59:30 shameev Exp $
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
require_once ( PATTERNS .DS. 'pattern2html.php' );
require_once ( PATTERNS .DS. 'pattern2sheet.php' );

class SectionTemplateVisualEdit
{
    public $new = false;
    private $template;
    private $content;
        
    public function __construct($template)
    {
        if (!$template) {
            $this->content = $this->create_new();
            $this->new = true;
        }
        else {
            $this->template = $template;
            $this->content = $this->table_edit();
        }
    }

    private function create_new()
    {
        $css = CSS::getInstance();
        $css->add_style_link('mzportal.form.css');
        $js = Javascript::getInstance();
        $js->add_jquery_validate();
        $js->add_datepicker();
        $js->add_dirtyforms();
        $df = '<fieldset class="adminform">';
        $df .= '<legend>Задайте размерность нового листа</legend>';  
        $df .= '<table cellspacing="1" class="admintable">';  
        $df .= '<tr>';
        $df .= '<td width="185" valign="top" class="key">Кол-во столбцов</td>';
        $df .= '<td><input name="col" style="width: 40px; height: 17px;" class="required number" value="5"/></td>';
        $df .= '</tr>';
        $df .= '<tr>';
        $df .= '<td width="185" valign="top" class="key">Кол-во строк</td>';
        $df .= '<td><input name="row" style="width: 40px; height: 17px;" class="required number" value="5"/></td>';
        $df .= '</tr>';
        $df .= '</table>';
        $df .= '</fieldset>';
        return $df;
    }
    
    private function table_edit()
    {
        $js = Javascript::getInstance();
        $js->add_jquery();
        $js->add_sheet($this->template);
        $sheet_div =  '<div id="Sheet" style="height: 450px;"></div>';
        return $sheet_div;
    }
    
    public function get_content()
    {
        return $this->content;
    }

}

?>