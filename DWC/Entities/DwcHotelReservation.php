<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DwcHotelReservationsFactory;

class DwcHotelReservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['hotel_id'];

    protected static function newFactory(): DwcHotelReservationsFactory
    {
        //return DwcHotelReservationsFactory::new();
    }
    public function hotel()
    {
        return $this->belongsTo(DwcHotel::class, 'hotel_id', 'id');
    }
    public function billingcode()
    {
        return $this->belongsTo(DwcBillingCode::class, 'billing_code', 'id');
    }
}
