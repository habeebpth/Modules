<?php

namespace Modules\Reward\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Reward\Database\factories\RewardCustomerFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardCustomer extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): RewardCustomerFactory
    {
        //return RewardCustomerFactory::new();
    }
    // RewardCustomer.php
    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }
}
