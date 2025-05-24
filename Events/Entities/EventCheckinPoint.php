<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Events\Database\factories\EventCheckinPointFactory;

class EventCheckinPoint extends Model
{
    use HasFactory;

    const FILE_PATH = 'event-checkin-points';

    protected $fillable = [
        'event_id',
        'name',
        'code',
        'number',
        'description',
        'image',
        'external_link',
        'hashname',
    ];

    protected $appends = ['image_url', 'image_path'];

    protected static function newFactory(): EventCheckinPointFactory
    {
        // return EventCheckinPointFactory::new();
    }

    /**
     * Get the full URL for the image.
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
     * Get the image storage path.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return $this->external_link ?: (self::FILE_PATH . '/' . $this->id . '/' . $this->hashname);
    }
}
