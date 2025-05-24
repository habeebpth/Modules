<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DWCRacesFactory;

class DwcRaces extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_races';
    protected $fillable = [];

    protected static function newFactory(): DWCRacesFactory
    {
        //return DWCRacesFactory::new();
    }
    public function horses()
    {
        return $this->belongsToMany(DwcHorse::class, 'dwc_race_horse');
    }
    public function racehorses()
    {
        return $this->belongsToMany(DwcHorse::class, 'dwc_race_horse', 'dwc_races_id', 'dwc_horse_id');
    }
}
