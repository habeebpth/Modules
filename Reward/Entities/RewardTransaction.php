<?php

namespace Modules\Reward\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Reward\Database\factories\RewardTransactionFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): RewardTransactionFactory
    {
        //return RewardTransactionFactory::new();
    }
     public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }
}
