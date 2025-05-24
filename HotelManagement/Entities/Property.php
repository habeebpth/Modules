<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HotelManagement\Database\factories\PropertyFactory;

class Property extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PropertyFactory
    {
        //return PropertyFactory::new();
    }
    public function files(): HasMany
    {
        return $this->hasMany(PropertyFile::class, 'property_id')->orderByDesc('id');
    }
}
