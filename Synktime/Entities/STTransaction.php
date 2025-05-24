<?php

namespace Modules\Synktime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Synktime\Database\factories\STTransactionFactory;

class STTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'st_transactions';

    protected $fillable = [];

    protected static function newFactory(): STTransactionFactory
    {
        //return STTransactionFactory::new();
    }
}
