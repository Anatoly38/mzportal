<?php
/**
* @version      $Id$
* @package      MZPortal.Framework
* @subpackage   AttAdmin
* @copyright    Copyright (C) 2009-2015 МИАЦ ИО

Прямой доступ запрещен
*/
defined( '_MZEXEC' ) or die( 'Restricted access' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'active_record.php' );
require_once ( MZPATH_BASE .DS.'includes'.DS.'link_objects.php' );

class AttestDossierTicketcountCabuserQuery extends ClActiveRecord 
{
    protected $source = 'attest_dossier_ticketcount_cabuser_view';
    public $oid; // идентификатор аттестационного дела
    public $номер_дела;
    public $фио;
    public $email;
    public $мо;
    public $экспертная_группа;
    public $вид_должности;
    public $Кол_во_попыток_тестирования;
    public $Доступ_в_личный_кабинет;
    
    public function __construct($oid = false)
    {
        if ($oid === false) {
            return;
        }
        $dbh = new DB_mzportal;
        $query =    "SELECT 
                        a.номер_дела,
                        a.фио,
                        a.email,
                        a.мо,
                        a.экспертная_группа,
                        a.вид_должности,
                        a.Кол_во_попыток_тестирования,
                        a.Доступ_в_личный_кабинет
                    FROM {$this->source} AS a 
                    WHERE oid = :1";
        $data = $dbh->prepare($query)->execute($oid)->fetch_assoc();
        if(!$data) {
            throw new Exception("Попытка тестирования не существует");
        }
        $this->oid              = $oid;
        $this->номер_дела       = $data['номер_дела'];
        $this->фио              = $data['фио'];
        $this->email            = $data['email'];
        $this->мо               = $data['мо'];
        $this->экспертная_группа = $data['экспертная_группа'];
        $this->вид_должности    = $data['вид_должности'];
        $this->Кол_во_попыток_тестирования    = $data['Кол_во_попыток_тестирования'];
        $this->Доступ_в_личный_кабинет    = $data['Доступ_в_личный_кабинет'];
    }

}
?>