<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class FiscalYear extends Model
{
    protected $fillable = [
        'company_id', 'name', 'start_date', 'end_date', 'is_active', 'is_closed'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function closingEntries(): HasMany
    {
        return $this->hasMany(ClosingEntry::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->where('is_active', true);
    }

    public function isDateInRange($date)
    {
        $checkDate = Carbon::parse($date);
        return $checkDate->between($this->start_date, $this->end_date);
    }
}
