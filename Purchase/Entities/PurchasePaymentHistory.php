<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePaymentHistory extends BaseModel
{
    use HasFactory;
    use HasCompany;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Purchase\Database\factories\PurchasePaymentHistoryFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendorPayment(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendorPayment::class, 'purchase_payment_id');
    }

}
