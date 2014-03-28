<?php
/** 
* @version		$Id: frmr_dictionary_import.php,v 1.0 2011/03/22 15:51:30 shameev Exp $
* @package		MZPortal.Framework
* @subpackage	FRMR Import Module
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
// Импорт данных из формата (XML) федерального регистра медработников 
class FrmrDictionaryImport 
{
    private $doc = false; // DOM Document
    private $replace = true; 
    private $file;
    private $personnel = array();
    private $dictionary; // Наименование таблицы словаря системы
    
    public function __construct($xml_file = false, $dictionary = null) {
        if (!$xml_file) {
            throw new Exception("Не определен файл для импорта");
        }
        if (!$dictionary) {
            throw new Exception("Не определен словарь для импорта данных");
        }
        $this->dictionary = $dictionary;
        $this->doc = new DOMdocument();
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true; 
        $path = FRMR_DICTIONARY_UPLOADS . DS . $xml_file;
        $this->file = $xml_file;
        If (!$this->doc->load($path, LIBXML_NOWARNING)) {
            throw new Exception("Ошибка загрузки XML файла");
        }
    }

    public function check_dictionary() // Проверка состава данных
    {
        $dic_section_name = $this->doc->firstChild->firstChild->nodeName;
        $dic_sect_el = $this->doc->getElementsByTagName($dic_section_name);
        $tags = "";
        $i = 0;
        foreach ($dic_sect_el as $dic_el) {
            $id     = $dic_el->getElementsByTagName('ID')->item(0)->nodeValue;
            $name   = $dic_el->getElementsByTagName('Name')->item(0)->nodeValue;
            $parent = $dic_el->getElementsByTagName('Parent')->item(0);
            $t = '';
            if ($parent instanceof DOMElement) {
                if ($p_code = $parent->nodeValue) {
                    $t = "(входит в $p_code)";
                } 
                else {
                    $t = "(верхний уровень)";
                }
            }
            $i++;
            $tags .= "<p>$i. <input type=\"checkbox\" name=\"uid[]\" value=\"$id\" checked=\"checked\" />$id $name $t</p>";
        }
        $title = "<p>Число записей в загруженном словаре - $i ($dic_section_name)</p>";
        $title .= "<p>В том числе:</p>";
        $title .= "<input type=\"hidden\" name=\"file\" value=\"{$this->file}\"/>";
        $title .= "<input type=\"hidden\" name=\"dic_name\" value=\"{$this->dictionary}\"/>";
        $tags = $title . $tags; 
        return $tags;
    }

    public function import_dictionary($uid = false) // Импорт данных о сотрудниках
    { 
        if (!$uid) {
            return false;
        }
        $dic_section_name = $this->doc->firstChild->firstChild->nodeName;
        $dic_sect_el = $this->doc->getElementsByTagName($dic_section_name);
        $count_el = $dic_sect_el->length;
        $dbh = new DB_mzportal;
        $query = "INSERT INTO {$this->dictionary} (код, родитель, наименование, федеральный_код) VALUES (:1, :2, :3, :4) 
                    ON DUPLICATE KEY UPDATE родитель = :5, наименование = :6 ;";
        $stmt = $dbh->prepare($query);
        $i = 0;
        foreach ($dic_sect_el as $el) {
            $id = $el->getElementsByTagName('ID')->item(0)->nodeValue;
            if (in_array($id, $uid)) {
                $name = $el->getElementsByTagName('Name')->item(0)->nodeValue;
                $parent_el = $el->getElementsByTagName('Parent')->item(0);
                if ($parent_el instanceof DOMElement) {
                    $parent = $parent_el->nodeValue;
                    if (empty($parent)) {
                        $parent = null;
                    }
                }
                else {
                    $parent = null;
                }
                $result = $stmt->execute($id, $parent, $name, $id, $parent, $name);
                $i++;
            }
        }
        return $i++;
    }
    
    public static function create_upload_form($dic_name = null)
    {
        $tags = '<div>';
        $tags .= '<input type="hidden" id="dic_name" name="dic_name" value="'.$dic_name.'" />';
        $tags .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />';
        $tags .= 'Выберите файл для загрузки: <input name="dic_file" type="file" />';
        $tags .= '</div>';
        return $tags;
    }
}

?>