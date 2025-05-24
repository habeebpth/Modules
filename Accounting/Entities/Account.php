<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Database\factories\AccountFactory;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): AccountFactory
    {
        //return AccountFactory::new();
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(Account::class, 'account_parent_id', 'id');
    }

}
