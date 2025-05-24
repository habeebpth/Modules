<?php

namespace Modules\HotelManagement\Entities;

use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HotelManagement\Database\factories\HmRoomFileFactory;

class HmRoomFile extends Model
{
    use HasFactory;
    use IconTrait;

    public const FILE_PATH = 'hm-room-files';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): HmRoomFileFactory
    {
        //return HmRoomFileFactory::new();
    }
    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3(HmRoomFile::FILE_PATH . '/' . $this->hm_room_id . '/' . $this->hashname);
    }

    public function hmroom(): BelongsTo
    {
        return $this->belongsTo(HmRoom::class);
    }
}
