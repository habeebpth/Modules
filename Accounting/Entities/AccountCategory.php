<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;  // Import SoftDeletes
use Modules\Accounting\Database\factories\AccountCategoryFactory;

class AccountCategory extends Model
{
    use HasFactory;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): AccountCategoryFactory
    {
        //return AccountCategoryFactory::new();
    }
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id', 'id');
    }
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

}
