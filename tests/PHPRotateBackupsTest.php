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
        $rotator = new PHPRotateBackups($driver);
        $rotator->days = 7;
        $rotator->months = 12;
        $rotator->years = 3;
        
        $result = $rotator->rotate();
        
        $this->assertCount(7 + 12 + 3, $result);
        #$this->assertEquals($this->getExpectedResult(), $result->toJson());
    }
    
    public function testExpected()
    {
        $driver = new Simulate();
        $driver->array = $this->get15Days();
        
        $this->assertCount(14, $driver->array);
        
        $rotator = new PHPRotateBackups($driver);
        $rotator->days = 7;
        $rotator->months = 12;
        $rotator->years = 3;
        
        $this->assertCount(9, $rotator->rotate());
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
    
    private function get15Days()
    {
        $timestamps = [];
        foreach (range(2,15) as $range) {
            $timestamps["$range-09-2016"] = 
                Carbon::createFromFormat("d-m-Y", "$range-09-2016")->getTimestamp();
        }
        
        return $timestamps;
    }
    
}
