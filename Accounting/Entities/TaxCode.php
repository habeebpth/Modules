<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxCode extends Model
{
    protected $fillable = [
        'company_id', 'code', 'name', 'description', 'type', 'rate', 'tax_account_id', 'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function taxAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'tax_account_id');
    }
}
