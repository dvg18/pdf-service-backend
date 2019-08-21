<?php

namespace File;

class Exception extends \Exception
{
    /**
     * @var string
     */
    const FILE_NOT_FOUND = 'File not found';

    /**
     * @throws \Exception
     */
    public static function throwFileNotFound()
    {
        throw new \Exception(self::FILE_NOT_FOUND);
    }
}