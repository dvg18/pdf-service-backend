<?php

namespace DB;

use BaseClass;

class FileItem extends BaseClass //implements Item
{
    /** @var int */
    const FILE_TYPE_UNKNOWN = 0;
    /** @var int */
    const FILE_TYPE_HTML = 1;
    /** @var int */
    const FILE_TYPE_PDF = 2;

    /** @var int Unique ID */
    public $Id;

    /** @var string Path to file */
    public $Path;

    /** @var string Name of file */
    public $Name;

    /** @var string Type of file (PDF, HTML and so on) */
    public $Type;

    /** @var */
    public $State;

}

class FileItemManager extends AbstractManager
{

}