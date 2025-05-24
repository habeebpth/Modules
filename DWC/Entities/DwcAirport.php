<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DWCAirportFactory;

class DwcAirport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_airports';
    protected $fillable = [];

    protected static function newFactory(): DWCAirportFactory
    {
        //return DWCAirportFactory::new();
    }
}
