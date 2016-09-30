<?php

namespace PHPRotateBackups\Driver;

interface DriverInterface
{
    
    public function getTimestamps();
    public function delete($file);   
}