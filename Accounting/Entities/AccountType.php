<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;  // Import SoftDeletes
use Modules\Accounting\Database\factories\AccountTypeFactory;

class AccountType extends Model
{
    use HasFactory;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): AccountTypeFactory
    {
        //return AccountTypeFactory::new();
    }
    public function categories()
    {
        return $this->hasMany(AccountCategory::class);
    }
}
