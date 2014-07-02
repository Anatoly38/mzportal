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

class PersonnelRetrainmentSave extends ItemSave
{
    protected $model = 'PersonnelRetrainmentQuery';
    private $human;

    public function get_post_values()
    {
        $this->query->учебное_заведение = Request::getVar('учебное_заведение');
        $this->query->год_прохождения   = Request::getVar('год_прохождения');
        $this->query->количество_часов  = Request::getVar('количество_часов');
        $this->query->серия_документа   = Request::getVar('серия_документа');
        $this->query->номер_документа   = Request::getVar('номер_документа');
        $this->query->специальность     = Request::getVar('специальность');
        $this->human = Request::getVar('human');
    }

    public function set_assoc()
    {
        $retrainment_link = Reference::get_id('переподготовка', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $retrainment_link); // Ассоциация между сотрудником и данными о переподготовке
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (Personnel, Retrainment) не сохранена!');
            return false;
        }
    }
}
?>