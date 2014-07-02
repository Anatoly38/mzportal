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

class PersonnelAwardSave extends ItemSave
{
    protected $model = 'PersonnelAwardQuery';
    private $personnel_id;

    public function get_post_values()
    {
        $this->query->номер_награды = Request::getVar('номер_награды');
        $this->query->наименование = Request::getVar('наименование');
        $this->query->дата_получения = Request::getVar('дата_получения');
        $this->human = Request::getVar('human');
    }
    
    public function set_assoc()
    {
        $award_link = Reference::get_id('награда', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $award_link); // Ассоциация между карточкой сотрудника и сведениями о награде
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (PersDemographic, PersAward) не сохранена!');
            return false;
        }
    }
}
?>