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

class PersonnelPostEducationSave extends ItemSave
{
    protected $model = 'PersonnelPostEducationQuery';
    private $personnel_id;

    public function get_post_values()
    {
        $this->query->базовая_организация  = Request::getVar('базовая_организация');
        $this->query->тип_образования      = Request::getVar('тип_образования');
        $this->query->начало_прохождения   = Request::getVar('начало_прохождения');
        $this->query->окончание_прохождения= Request::getVar('окончание_прохождения');
        $this->query->дата_получ_документа = Request::getVar('дата_получ_документа');
        $this->query->ученая_степень       = Request::getVar('ученая_степень');
        $this->query->серия_диплома        = Request::getVar('серия_диплома');
        $this->query->номер_диплома        = Request::getVar('номер_диплома');
        $this->query->специальность        = Request::getVar('специальность');
        $this->personnel_id                = Request::getVar('personnel_id');
    }

    public function set_assoc()
    {
        $card_education_link = Reference::get_id('образование', 'link_types');
        try {
            LinkObjects::set_link($this->personnel_id, $this->query->oid, $card_education_link); // Ассоциация между карточкой сотрудника и данными об образовании
        }
        catch (Exception $e) {
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (Personnel, Education) не сохранена!');
            return false;
        }
    }
}
?>