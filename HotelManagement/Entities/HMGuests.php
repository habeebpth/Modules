<?php

namespace Modules\HotelManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HotelManagement\Database\factories\HMGuestsFactory;

class HMGuests extends Model
{
    use HasFactory;
    use SoftDeletes;
    public const FILE_PATH = 'hmguests';

    protected $table = 'hm_guests';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): HMGuestsFactory
    {
        //return HMGuestsFactory::new();
    }
    protected $appends = ['image_url', 'image_path'];

    /**
     * Get the full URL for the airline's file.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->hashname);
    }

    /**
     * Get the file path for the stored file.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return $this->external_link ?: (self::FILE_PATH . '/' . $this->id . '/' . $this->hashname);
    }
}
