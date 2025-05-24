<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DWCHorseFactory;

class DwcHorse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_horses';
    protected $fillable = [];

    protected static function newFactory(): DWCHorseFactory
    {
        //return DWCHorseFactory::new();
    }
    public function races()
    {
        return $this->belongsToMany(DwcRaces::class, 'dwc_race_horse');
    }
    public function racehourse()
    {
        return $this->belongsToMany(DwcRaces::class, 'dwc_race_horse', 'dwc_horse_id', 'dwc_races_id');
    }
}
