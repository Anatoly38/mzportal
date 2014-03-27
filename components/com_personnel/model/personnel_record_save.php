<?php
/**
* @version		$Id: personal_record_save.php,v 1.0 2011/02/15 12:50:30 shameev Exp $
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

class PersonnelRecordSave extends ItemSave
{
    protected $model = 'PersonnelRecordQuery';
    private $personnel_id;

    public function get_post_values()
    {
        $this->query->вид_должности = Request::getVar('вид_должности');
        $this->query->тип_должности = Request::getVar('тип_должности');
        $this->query->должность = Request::getVar('должность');
        $this->query->ставка = Request::getVar('ставка');
        $this->query->дата_начала_труд_отношений = Request::getVar('дата_начала_труд_отношений');
        $this->query->тип_записи_начало = Request::getVar('тип_записи_начало');
        $this->query->номер_приказа_начало = Request::getVar('номер_приказа_начало');
        $this->query->дата_окончания_труд_отношений = Request::getVar('дата_окончания_труд_отношений');
        $this->query->тип_записи_окончание = Request::getVar('тип_записи_окончание');
        $this->query->номер_приказа_окончание = Request::getVar('номер_приказа_окончание');
        $this->query->режим_работы = Request::getVar('режим_работы');
        $this->query->военная_служба = Request::getVar('военная_служба');
        $this->query->подразделение = Request::getVar('подразделение');
        $this->query->название_подразделения = Request::getVar('название_подразделения');
        $this->query->вид_мп = Request::getVar('вид_мп');
        $this->query->условия_мп = Request::getVar('условия_мп');
        $this->query->население = Request::getVar('население');
        $this->human = Request::getVar('human');
        $this->lpu = Request::getVar('lpu');
    }

    public function set_assoc()
    {
        
        $record_link = Reference::get_id('должность', 'link_types');
        $lpu_link = Reference::get_id('должность_лпу', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $record_link); // Ассоциация между сотрудником и данными о занятой должности
            LinkObjects::set_link($this->query->oid, $this->lpu, $lpu_link); // Ассоциация между данными о занятой должности и ЛПУ
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (PersDemographic, PersonnelRecord, PaspLpu) не сохранена!');
            return false;
        }
    }
}
?>