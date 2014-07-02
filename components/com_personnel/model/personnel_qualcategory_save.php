<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport
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
require_once ( MZPATH_BASE .DS.'components'.DS.'item_save.php' );

class PersonnelQualCategorySave extends ItemSave
{
    protected $model = 'PersonnelQualCategoryQuery';
    private $human;

    public function get_post_values()
    {
        $this->query->категория       = Request::getVar('категория');
        $this->query->год_присвоения  = Request::getVar('год_присвоения');
        $this->query->специальность   = Request::getVar('специальность');
        $this->human = Request::getVar('human');
    }

    public function set_assoc()
    {
        $category_link = Reference::get_id('категория', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $category_link); // Ассоциация между сотрудником и данными об квалификационных категориях
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (Personnel, QualCategory) не сохранена!');
            return false;
        }
    }
}
?>