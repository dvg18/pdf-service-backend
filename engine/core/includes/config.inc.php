<?php

use Http\Origin\Checker;

ini_set('default_charset', 'ISO-8859-1');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* Initialize Core */
@define('CFG_PATH_DOCUMENT_ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
define('CFG_PATH_COMMON_ENGINE', CFG_PATH_DOCUMENT_ROOT . 'engine/core/');
define('CFG_PATH_DEFAULT_HOST', CFG_PATH_DOCUMENT_ROOT . 'public_html/default/');

define('CFG_PATH_ENGINE', CFG_PATH_DOCUMENT_ROOT . 'engine/core/');
define('CFG_PATH_HOST', CFG_PATH_DOCUMENT_ROOT . 'public_html/default/');

define('CFG_PATH_INC', 'includes/');
define('CFG_PATH_CLASS', CFG_PATH_INC . 'classes/');
define('CFG_PATH_LIB', CFG_PATH_INC . 'lib/');

set_include_path(CFG_PATH_DOCUMENT_ROOT . PATH_SEPARATOR . get_include_path());

set_include_path(CFG_PATH_COMMON_ENGINE . CFG_PATH_CLASS . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_COMMON_ENGINE . CFG_PATH_INC . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_COMMON_ENGINE . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_ENGINE . CFG_PATH_CLASS . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_ENGINE . CFG_PATH_INC . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_ENGINE . PATH_SEPARATOR . get_include_path());
set_include_path(CFG_PATH_ENGINE . CFG_PATH_LIB . PATH_SEPARATOR . get_include_path());

require_once 'vendor/autoload.php';

require_once 'ServerVars.class.php';
require_once 'Request.class.php';

$GLOBALS['Request']->upgradeByExtraFields();

/* Set headers */
require_once 'Http/Origin/Checker.php';
if (Http\Origin\Checker::exec()) {
    define('HTTP_ORIGIN', $_SERVER['HTTP_ORIGIN']);
    define('HTTP_ORIGIN_CASTED_PROTOCOL', Http\Origin\Checker::originWithCastedProtocol());
    /**
     * @see Controller
     * Headers moved into Controller.
     * Because different methods need different headers
     */
} else {
    error_log('Incorrect origin: ' . $_SERVER['HTTP_ORIGIN'] . ' => ' . $_SERVER['HTTP_REFERER']);
    die('Service temporarily unavailable.');
}

/* Set other settings */
define('CFG_FOLDER_UPLOADED_TMP', CFG_PATH_DOCUMENT_ROOT . 'tmp/uploaded/');
define('CFG_FOLDER_UPLOADED_PDF', CFG_PATH_DOCUMENT_ROOT . 'tmp/uploaded/pdf/');
define('CFG_OFFICIAL_SITE', str_replace('www.', 'pdf.', HTTP_ORIGIN_CASTED_PROTOCOL));