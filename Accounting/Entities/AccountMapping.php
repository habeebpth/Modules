<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountMapping extends Model
{
    protected $fillable = [
        'company_id', 'module_name', 'mapping_type', 'account_id', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getAccountForMapping($moduleName, $mappingType)
    {
        $mapping = self::where('company_id', user()->company_id)
            ->where('module_name', $moduleName)
            ->where('mapping_type', $mappingType)
            ->where('is_active', true)
            ->first();

        return $mapping ? $mapping->account_id : null;
    }
}