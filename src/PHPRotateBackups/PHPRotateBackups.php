<?php

namespace PHPRotateBackups;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class PHPRotateBackups
{

    public $days = 7;
    public $months = 12;
    public $years = 5;
    
    public $driver;
    
    public function __construct(Driver\DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    function rotate()
    {
        $timestamps = $this->carbonize($this->driver->getTimestamps());
        
        $delete = $this->getFilesToDelete($timestamps);

        $timestamps_one_by_day = $timestamps->diff($delete);
        
        $keep = collect()
            ->merge(
                $this->getFilesToKeepDays($timestamps_one_by_day)
            )->merge(
                $this->getFilesToKeepMonths($timestamps_one_by_day)
            )->merge(
                $this->getFilesToKeepYears($timestamps_one_by_day)
            );
        
        $timestamps->diff($keep); //retorna os deletados!
         
        return $keep;
    }
    
    /**
     * @param Collection $timestamps
     * @return Collection
     */
    function carbonize($timestamps) 
    {   
        return $timestamps->map(function($timestamp) {
            return Carbon::createFromTimestamp($timestamp);
        });
    } 
    
    function getFilesToDelete($timestamps)
    {
        $deleted = $this->getFilesToKeepDays($timestamps);
        $deleted = $this->getFilesToKeepMonths($timestamps);
        $deleted = $this->getFilesToKeepYears($timestamps);
        
        return $deleted;
    }
    
    /**
     * @param Collection|Carbon $timestamps
     * */
    function getFilesToKeepDays($timestamps)
    {   
        return $timestamps->reverse()->take($this->days);
    }
    
    /**
     * @param Collection|Carbon $timestamps
     * */
    function getFilesToKeepMonths($timestamps)
    {
        $monthly_chunks = collect();
        
        $timestamps->reverse()->each(function($element) use ($monthly_chunks) {
            
            $key = $element->year . "-" . $element->month;
            
            if (! $monthly_chunks->has($key))
                $monthly_chunks->put($key, collect());
                
            $monthly_chunks->get($key)->push($element);
            
        });
        
        return $monthly_chunks->map(function($month) {
            return $month->sort()->first();
        })->sort()->reverse()->take($this->months);
    }
    
    /**
     * @param Collection|Carbon $timestamps
     * */
    function getFilesToKeepYears($timestamps)
    {   
        $yearly_chunks = collect();
        
        $timestamps->reverse()->each(function($element) use ($yearly_chunks) {
            
            $key = $element->year;
            
            if (! $yearly_chunks->has($key))
                $yearly_chunks->put($key, collect());
                
            $yearly_chunks->get($key)->push($element);
            
        });
        
        return $yearly_chunks->map(function($year) {
            return $year->sort()->first();
        })->sort()->reverse()->take($this->years);
    }
    
}
