<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Events\Database\factories\DistrictFactory;

class District extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DistrictFactory
    {
        //return DistrictFactory::new();
    }
    public function panchayats()
    {
        return $this->hasMany(Panchayat::class);
    }
}
