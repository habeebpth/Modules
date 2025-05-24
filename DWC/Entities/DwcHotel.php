<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DWCHotelFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DwcHotel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'dwc_hotels';
    protected $fillable = [];

    protected static function newFactory(): DWCHotelFactory
    {
        //return DWCHotelFactory::new();
    }
    public function roomtype(): HasMany
    {
        return $this->hasMany(DwcHotelRoomType::class, 'hotel_id');
    }
}
