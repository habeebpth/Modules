<?php

namespace Modules\DWC\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DWC\Database\factories\DwcBillingCodeFactory;

class DwcBillingCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): DwcBillingCodeFactory
    {
        //return DwcBillingCodeFactory::new();
    }
}
