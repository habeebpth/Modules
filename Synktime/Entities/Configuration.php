<?php

namespace Modules\Synktime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Synktime\Database\factories\ConfigurationFactory;

class Configuration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['url', 'api_key', 'username', 'password', 'attendance_type'];

    protected static function newFactory(): ConfigurationFactory
    {
        //return ConfigurationFactory::new();
    }
}
