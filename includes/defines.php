<?php
// no direct access
defined( '_MZEXEC' ) or die( 'Restricted access' );

//Global definitions
$parts = explode( DS, MZPATH_BASE );

//Defines
define( 'MZPATH_ROOT',              implode( DS, $parts ) );
define( 'MZPATH_SITE',              MZPATH_ROOT );
define( 'MZPATH_CONFIGURATION',     MZPATH_ROOT );
define( 'TEMPLATES',                MZPATH_ROOT.DS.'templates');
define( 'TMPL',                     MZPATH_BASE.DS.'templates');
define( 'IMAGES',                   MZPATH_ROOT.DS.'includes'.DS.'style'.DS.'images');
define( 'MODULES',                  MZPATH_ROOT.DS.'modules');
define( 'COMPONENTS',               MZPATH_ROOT.DS.'components');
define( 'ROOT',                     MZConfig::$root );
if (MZConfig::$os == 'windows') {
    define( 'UPLOADS',                  'C:\uploaded_files');
    define( 'FRMR_DICTIONARY_UPLOADS',  'C:\uploaded_files\frmr_dic_uploads');
}
else if (MZConfig::$os == 'linux') {
    define( 'UPLOADS',                  '/home/wwwroot/tmp_upload/');
    define( 'FRMR_DICTIONARY_UPLOADS',  '/home/wwwroot/tmp_upload/');
}

?>