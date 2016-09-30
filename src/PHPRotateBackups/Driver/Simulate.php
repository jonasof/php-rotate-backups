<?php

namespace PHPRotateBackups\Driver;

class Simulate implements DriverInterface
{
    
    public $array; //format ["filename.test" => 199928319]
    
    public function getTimestamps() 
    {
        $timestamps = collect($this->array);
        return $timestamps->sort();
    }
    
    public function delete($file)
    {
        //faça nada
    }

}
