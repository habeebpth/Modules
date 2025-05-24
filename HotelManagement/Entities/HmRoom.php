<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HotelManagement\Database\factories\HmRoomFactory;

class HmRoom extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'hm_rooms';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): HmRoomFactory
    {
        //return HmRoomFactory::new();
    }
    public function facilities()
    {
        return $this->hasMany(RoomFacility::class, 'room_id');
    }
    public function files(): HasMany
    {
        return $this->hasMany(HmRoomFile::class, 'hm_room_id')->orderByDesc('id');
    }

}
