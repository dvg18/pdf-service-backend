<?php

require_once '../engine/core/includes/config.inc.php';
require_once '../engine/core/includes/config_common.inc.php';
$scriptName = ltrim($GLOBALS['Request']->get('SCRIPT_NAME'), '/');

// cron_task
if (preg_match('/^task\/.+php$/', $scriptName)) {
    require_once CFG_PATH_INC . 'functions.inc.php';
    include CFG_PATH_DOCUMENT_ROOT . 'tools/cron/' . $scriptName;
    die;
}

// sys_cron_task
if (preg_match('/^sys_cron_task\/.+php$/', $scriptName)) {
    require_once CFG_PATH_INC . 'functions.inc.php';
    include $scriptName;
    die;
}

$controller = new Controller();
try {
    $controller->process();
} catch (Exception $ex) {
    trigger_error($ex->getMessage());
    echo json_encode(array('error' => $ex->getMessage()));
}
