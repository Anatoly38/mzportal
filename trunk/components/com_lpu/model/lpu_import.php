<?php
/** 
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   Passport
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/

defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( 'lpu_temp_query.php' );

class LpuImport 
{
    private $lpu_obj; 
    private $by_ogrn = true; // обновляем данные при совпадении ОГРН
    
    public function __construct() {
        set_time_limit(0);
    }
    
    public function update_all()
    {
        $dbh = new DB_mzportal;
        $query =    "SELECT `номер_пп` FROM pasp_lpu_temp"; 
        $items = $dbh->execute($query)->fetch();
        if (!$items) {
            throw new Exception("В БД нет данных ЛПУ для импорта");
        }
        $q = 0;
        foreach ($items as $item) {
            $q_temp = new LpuTempQuery($item);
            $q_oid = $this->update_lpu($q_temp);
            if ($q_oid) {
                $q++;
            }
        }
        return $q;
    }
    
    private function update_lpu($temp_obj)
    {
        if (!$temp_obj) {
            return false;
        }
        try {
            $lpu_obj = LpuQuery::findByOgrn($temp_obj->огрн);            
        }
        catch (Exception $e) {
            return false;
        }
        $lpu_obj->наименование = $temp_obj->наименование;
        $lpu_obj->сокращенное_наименование = $temp_obj->сокращенное_наименование;
        $lpu_obj->update();
        return $lpu_obj->oid;
    }

}
?>