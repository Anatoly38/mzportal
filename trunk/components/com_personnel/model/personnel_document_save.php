<?php
/**
* @version		$Id$
* @package		MZPortal.Framework
* @subpackage	Passport_LPU
* @copyright	Copyright (C) 2009-2014 МИАЦ ИО

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
            Message::error('Ошибка: Ассоциация между объектами (PersDemographic, Document) не сохранена!');
            return false;
        }
    }
}
?>