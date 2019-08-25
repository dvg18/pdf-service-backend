<?php

use DB\FileItemManager;

$DP = new FileItemManager('FileItem');
$fileItems = $DP->getAll();
foreach ($fileItems as $file) {
    if (File::fileExist($file)) {
        continue;
    } else {
        $DP->remove($file->Id);
    }
}