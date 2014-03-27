<?php
/**
* @version		$Id: personal_document_save.php,v 1.0 2011/02/08 12:50:30 shameev Exp $
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

class PersonnelDocumentSave extends ItemSave
{
    protected $model = 'PersonnelDocumentQuery';
    private $personnel_id;

    public function get_post_values()
    {
        $this->query->тип_документа = Request::getVar('тип_документа');
        $this->query->серия_документа = Request::getVar('серия_документа');
        $this->query->номер_документа = Request::getVar('номер_документа');
        $this->query->кем_выдан = Request::getVar('кем_выдан');
        $this->query->дата_выдачи = Request::getVar('дата_выдачи');
        $this->human = Request::getVar('human');
    }
    
    public function set_assoc()
    {
        $document_link = Reference::get_id('документ', 'link_types');
        try {
            LinkObjects::set_link($this->human, $this->query->oid, $document_link); // Ассоциация между карточкой сотрудника и документом
        }
        catch (Exception $e) {
            $m = Message::getInstance();
            $m->enque_message('error', 'Ошибка: Ассоциация между объектами (PersDemographic, Document) не сохранена!');
            return false;
        }
    }
}
?>