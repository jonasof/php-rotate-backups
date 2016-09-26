<?php

use PHPUnit\Framework\TestCase;
use PHPRotateBackups\PHPRotateBackups;
use PHPRotateBackups\Driver\Simulate;

use Carbon\Carbon;

class PHPRotateBackupsTest extends TestCase
{

    public function test()
    {
        $driver = new Simulate();
        $driver->array = $this->getFiles(20);
        $this->assertCount(20, $driver->getTimestamps());
        
        $driver->array = $this->getManyFiles();
        $rotator = new PHPRotateBackups\PHPRotateBackups($driver);
        $rotator->days = 7;
        $rotator->months = 12;
        $rotator->years = 3;
        
        $result = $rotator->rotate();
        
        $this->assertCount(7 + 12 + 3, $result);
        #$this->assertEquals($this->getExpectedResult(), $result->toJson());
    }
    
    private function getExpectedResult()
    {

    }
    
    private function getManyFiles()
    {
        $files = [];
        
        $base = Carbon::createFromTimestamp(1474857800);
        
        $date = Carbon::createFromTimestamp(1474857800)->addYears(-5);
        
        while ($date->diffInDays($base) != 0) {
            $files[
                $date->year . "-" . $date->month . "-" . $date->day
            ] = $date->getTimestamp();
            $date->addDay(1);
        }
        
        return $files;
    }
    
    private function getFiles($count)
    {
        $files = [];
        
        $data = new Carbon();
        $data->addYears(5);
        
        $x = 0;
        
        while ($x < $count) {
            $files[
                $data->year . "-" . $data->month . "-" . $data->day
            ] = $data->getTimestamp();
            $data->addDay(1);
            $x++;
        }
        
        return $files;
    }
    
}
