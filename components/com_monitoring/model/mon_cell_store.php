<?php
/**
* @version      $Id: mon_cell_store.php,v 1.0 2011/09/24 18:37:30 shameev Exp $
* @package      MZPortal.Framework
* @subpackage   Monitorings
* @copyright    Copyright (C) 2011 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( COMPONENTS.DS.'com_period'.DS.'model'.DS.'period_query.php' );

class MonCellStore
{
    protected $section;

    public function __construct($section)
    {
        if (!$section) {
            throw new Exception("Не определен раздел документа, данные невозможно сохранить");
        }
        $this->section = $section;
    }
    
    public function save($data)
    {
        if (!$data) {
            return;
        }
        $dbh = new DB_mzportal;
        $query = "INSERT INTO mon_cellstorage (section, r, c, value) VALUES (:1, :2, :3, :4) ON DUPLICATE KEY UPDATE value = :5";
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $data);
        $cells = $dom->getElementsByTagName('td');
        $i = 0 ; // Общее кол-во заполняемых ячеек
        $j = 0 ; // Общее кол-во заполненных ячеек
        $res = array();
        foreach ($cells as $cell) {
            $cell_loc = explode('_', $cell->getAttribute('id')); 
            $classes = explode(' ', $cell->getAttribute('class'));
            $protected = in_array('cellProtected', $classes) ? 1 : 0;
            $calculated = in_array('cellCalculated', $classes) ? 1 : 0;
            if (!$protected) {
                if (!$calculated) {
                    $i++;
                }
                $type = $cell->getAttribute('type');
                $v = trim($cell->nodeValue);
                if (is_numeric($v)) {
                    switch ($type) {
                        case null:
                        case 'int':
                            $v = (int)$v; 
                        break;
                        case 'float':
                            $v = (float)$v; 
                        break;
                    }
                } elseif ($v === '') {
                    $v = null; 
                }
                if ($v !== null && !$calculated) {
                    $j++;
                }
                $c = ltrim($cell_loc[0], 'c');
                $r = ltrim($cell_loc[1], 'r');
                $dbh->prepare($query)->execute($this->section, $r, $c, $v, $v);
            }
        }
        $res['all']     = $i;
        $res['filled']  = $j;
        return $res;
    }

}
?>