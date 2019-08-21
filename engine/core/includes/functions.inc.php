<?php

/**
 * @param $objList
 * @param bool $options
 * @param string $fldKey
 * @param string $fldName
 * @param bool $selected
 * @return array|bool
 */
function list2options($objList, $options = false, $fldKey = 'Id',
    $fldName = 'Name', $selected = false)
{
    if (!is_array($options)) {
        $options = array();
    }
    $qty = sizeof($objList);
    for ($i = 0; $i < $qty; $i++) {
        $options[] = array('value' => $objList[$i]->Id, 'caption' => $objList[$i]->Name);
    }
    return $options;
}

/**
 * @param $dbClass
 * @param $keyList
 * @param bool $overload
 * @return array
 */
function getInstanceNames($dbClass, $keyList, $overload = false)
{
    $clsFile = !empty($overload['file'])
        ? $overload['file']
        : ($dbClass . '.class.php');
    $clsManagerName = !empty($overload['managerName'])
        ? $overload['managerName']
        : ($dbClass . 'Manager');
    $clsMethodName = !empty($overload['methodName'])
        ? $overload['methodName']
        : ('getAll' . $dbClass . 's');
    $clsFieldKey = !empty($overload['fieldKey'])
        ? $overload['fieldKey']
        : 'Id';
    $clsFieldName = !empty($overload['fieldName'])
        ? $overload['fieldName']
        : 'Name';

    require_once CFG_PATH_DB_CLASS . $clsFile;
    $keyList = is_array($keyList)
        ? implode(',', $keyList)
        : $keyList;
    $items = $GLOBALS[$clsManagerName]->{$clsMethodName}($clsFieldKey . ' IN (' . $keyList . ')');

    $out = array();
    foreach ($items as $item) {
        $out[] = $item->Name;
    }
    return $out;
}

/**
 * @param $flName
 * @param $msg
 * @return bool
 */
function logFileMsg($flName, $msg)
{
    $fileExists = file_exists($flName);
    $fp = @fopen($flName, 'a');
    if (!$fp) {
        return false;
    }
    $msg = sprintf("[%s] %s\n", date('d.m.Y H:i:s'), $msg);
    @fputs($fp, $msg);
    @fclose($fp);
    if (!$fileExists) {
        @chmod($flName, 0666);
    }
    return file_exists($flName);
}

/**
 * @param $msg
 * @return bool
 */
function logSysCronMsg($msg)
{
    $flName = CFG_PATH_LOG . 'sys_cron_log/' . date('Y-m-d') . '.log';
    return logFileMsg($flName, $msg);
}

/**
 * @param $dir
 * @param int $perm
 * @return bool
 */
function chkDir($dir, $perm = 0774)
{
    if (!is_dir($dir)) {
        @mkdir($dir);
    }
    if (is_dir($dir) && $perm > 0) {
        @chmod($dir, $perm);
    }
    return is_dir($dir);
}

/**
 * @return bool
 */
function testCookie()
{
    $value = $GLOBALS['Request']->getSessionValue('cookie_checked');
    if (!empty($value)) {
        return true;
    }
    if (!$GLOBALS['Request']->emptyParam('tstcookie')) {
        return false;
    } else {
        $GLOBALS['Request']->setSessionValue('cookie_checked', 1);
        $redirectUrl = $GLOBALS["SystemFuncs"]->getSelfUrl()
            . '?' . $GLOBALS["SystemFuncs"]->refineQueryString(array('tstcookie')) . '&tstcookie=1';
        $GLOBALS['SystemFuncs']->goLocation($redirectUrl);
        die;
    }
    return true;
}

// hpcos: function for clear dir and all subdirs for it
// also you can use exceptions for specify dir and/or files wich must be skipped
// exceptions would be skipped in dir and all subdirs
/**
 * @param $dirName
 * @param array $exceptions
 * @return bool
 */
function clearDir($dirName, $exceptions = array())
{
    if (empty($dirName) || !is_dir($dirName)) {
        return FALSE;
    }
    $it = new RecursiveDirectoryIterator($dirName,
        RecursiveDirectoryIterator::SKIP_DOTS
    );
    $files = new RecursiveIteratorIterator($it,
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $fullExceptions = array_merge($exceptions, array('.', '..'));
    foreach ($files as $file) {
        $realPath = $file->getRealPath();
        if (in_array($file->getFilename(), $fullExceptions) || empty($realPath)) {
            continue;
        }
        if ($file->isDir()) {
            rmdir($realPath);
        } else {
            unlink($realPath);
        }
    }
    return TRUE;
}

/**
 * @param $srcDir
 * @param $dstDir
 */
function copyDir($srcDir, $dstDir)
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir,
        RecursiveDirectoryIterator::SKIP_DOTS
        ), RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isDir()) {
            mkdir($dstDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        } else {
            copy($item,
                $dstDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName()
            );
        }
    }
}

/**
 * @param $file
 * @param $destPath
 * @return bool
 */
function unzip($file, $destPath)
{
    $zip = new ZipArchive;
    if ($zip->open($file) === TRUE) {
        $zip->extractTo($destPath);
        $zip->close();
        return TRUE;
    } else {
        return FALSE;
    }
}


// hpcos: find backUrl in $_GET
/**
 * @return bool
 */
function backUrlFind()
{
    return $GLOBALS['Request']->issetParam('backurlid')
        ? $GLOBALS['Request']->get('backurlid')
        : false;
}

/**
 * @param $backUrl
 * @return false|int|string
 */
function backUrlSet($backUrl)
{
    $value = $GLOBALS['Request']->getSessionValue('backUrl');
    if (empty($value['backUrl'])) {
        $value['backUrl'] = array();
    }
    $backUrlId = array_search($backUrl, $value);
    if ($backUrlId === false) {
        $value[] = $backUrl;
        $GLOBALS['Request']->setSessionValue('backUrl', $value);
        $backUrlId = count($value) - 1;
    }
    return $backUrlId;
}

/**
 * @param $backUrlId
 * @return string
 */
function backUrlGet($backUrlId)
{
    $value = $GLOBALS['Request']->getSessionValue('backUrl');
    return empty($value[$backUrlId])
        ? 'javascript:history.back(-1);'
        : $value[$backUrlId];
}
