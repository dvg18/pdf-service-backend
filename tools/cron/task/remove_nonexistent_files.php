<?php

$DP = new \DataProvider();
$fileItems = $DP->getAll();
foreach ($fileItems as $file) {
    if (File::fileExist($file)) {
        continue;
    } else {
        $DP->remove($file->Id);
    }
}