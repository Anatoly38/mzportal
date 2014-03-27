<?php
/**
* @version		$Id: section_tree.php,v 1.0 2010/05/07 19:10:30 shameev Exp $
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item.php' );

class SectionTree
{
    public static function set_dp_section_view($item)
    {
        if (!$item) {
            return true;
        }
        $tp = TaskPaneBuilder::getInstance();
        $tp->load_node($item, 0, 'Разделы документа' , false);
        $dbh = new DB_mzportal();
        $obj_not_deleted = "s.oid = o.oid AND o.deleted ='0'";
        $query = "SELECT DISTINCT
                       s.oid, s.наименование 
                    FROM 
                        mon_dp_sections AS s, sys_acl AS a, sys_objects AS o 
                    WHERE
                        $obj_not_deleted
                        AND s.doc_pattern_id = :1";
        $stmt = $dbh->prepare($query)->execute($item);
        $data = new DB_Result($stmt);
        while ($data->next()) {
            $route = 'app=14&amp;oid[]=' . $data->oid . '&amp;task=section_edit'  ;
            $tp->load_node($data->oid, $item, $data->наименование , $route); 
        }
        return true;
    }
}

?>