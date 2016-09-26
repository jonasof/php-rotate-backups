<?php

namespace PHPRotateBackups\Driver;

use DirectoryIterator;


class FileTimestamp implements DriverInterface
{
    
    public $folder;

    public function __construct()
    {
        
    }
    
    function getTimestamps()
    {
        $timestamps = collect();

        foreach (new DirectoryIterator($this->folder) as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $timestamps->put($fileinfo->getFilename(), $fileinfo->getCTime());
            }
        }

        return $timestamps;
    }

}
