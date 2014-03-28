<?php 
/**
* @version		$Id: xml_sheet.php,v 1.0 2011/05/05 12:03:27 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	Document Patterns
* @copyright	Copyright (C) 2011 МИАЦ ИО
* @license		GNU/GPL, see LICENSE.php
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.

 Прямой доступ запрещен
 */
defined( '_MZEXEC' ) or die( 'Restricted access' );

class XmlSheet 
{
    private $sheet_DOMDoc;

    public function __construct()  
    {

    }

    public static function create_blank_sheet($rows, $cols, $title = null)
    {
        $meta =  '<metadata>';
        $meta .= '<columns>'. $cols .'</columns>';
        $meta .= '<rows>'   . $rows . '</rows>';
        $meta .= '<title>'  . $title . '</title>';
        $meta .= '</metadata>';
        $row_cells  = ''; 
        for($i = 0; $i < $rows; $i++)
        {
            $row = self::insert_row($i, $cols);
            $row_cells .= $row;
        }
        $data = '<data>' . $row_cells . '</data>';
        $doc  = '<documents><document>';
        $doc .= $meta . $data;
        $doc .= '</document></documents>';
        return $doc;
    }

    private static function insert_row($index, $cols)
    {
        $cells = '';
        for($i = 0; $i < $cols; $i++)
        {
            $cell = self::insert_cell($i);
            $cells  .= $cell;
        }
        $row = '<r'.$index.'>'.$cells.'</r'.$index.'>';
        return $row;
    }
    
    private static function insert_cell($index, $content = null)
    {
        if (!$content) {
            $cell = '<c'.$index.'/>';
        }
        else {
            $cell = '<c'.$index.'>'.$content.'</c'.$index.'>';
        }
        return $cell;
    }
    
}

?>