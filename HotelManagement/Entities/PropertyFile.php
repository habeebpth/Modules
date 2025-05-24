<?php

namespace Modules\HotelManagement\Entities;

use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HotelManagement\Database\factories\PropertyFileFactory;

class PropertyFile extends Model
{
    use HasFactory;
    use IconTrait;

    public const FILE_PATH = 'property-files';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PropertyFileFactory
    {
        //return PropertyFileFactory::new();
    }

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3(PropertyFile::FILE_PATH . '/' . $this->property_id . '/' . $this->hashname);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

}
