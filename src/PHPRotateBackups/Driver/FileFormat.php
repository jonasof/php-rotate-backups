<?php

namespace PHPRotateBackups\Driver;

use DirectoryIterator;

class FileFormat implements DriverInterface
{
    
    public $folder;
    public $format;

    public function __construct()
    {
        
    }
    
    function getTimestamps()
    {
        $timestamps = collect();
        
        foreach (new DirectoryIterator($this->folder) as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $time = \Carbon\Carbon::createFromFormat($this->format, $fileinfo->getFilename());
                $timestamps->put($fileinfo->getFilename(), $time->getTimestamp());
            }
        }
        
        $timestamps = $timestamps->sort();
        
        return $timestamps;
    }
    
    public function delete($file)
    {
        unlink($this->folder . DIRECTORY_SEPARATOR . $file);
    }

}
