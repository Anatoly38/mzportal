<?php
/**
* @version		$Id: personal_education_save.php,v 1.0 2011/02/14 12:50:30 shameev Exp $
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

class PersonnelEducationSave extends ItemSave
{
    protected $model = 'PersonnelEducationQuery';
    private $human;

    public function get_post_values()
    {
        $this->query->учебное_заведение = Request::getVar('учебное_заведение');
        $this->query->год_окончания     = Request::getVar('год_окончания');
        $this->query->серия_диплома     = Request::getVar('серия_диплома');
        $this->query->номер_диплома     = Request::getVar('номер_диплома');
        $this->query->специальность     = Request::getVar('специальность');
        $this->query->тип_образования   = Request::getVar('тип_образования');
        $this->human = Request::getVar('human');
    }

    public function set_assoc()
    {
        $education_link = Reference::get_id('образование', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $education_link); // Ассоциация между карточкой сотрудника и данными об образовании
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (Personnel, Education) не сохранена!');
            return false;
        }
    }
}
?>