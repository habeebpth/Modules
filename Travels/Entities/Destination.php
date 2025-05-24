<?php

namespace Modules\Travels\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Travels\Database\factories\DestinationFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory;
    use SoftDeletes;
    public const FILE_PATH = 'destinations';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DestinationFactory
    {
        //return DestinationFactory::new();
    }
    // Attributes to append to the model's array or JSON representation
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
