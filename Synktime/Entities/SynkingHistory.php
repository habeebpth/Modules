<?php

namespace Modules\Synktime\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SynkingHistory extends Model
{
    protected $table = 'synking_history';

    protected $fillable = [
        'company_id',
        'created_by',
        'updated_by',
        'project_id',
        'employee_id',
        'from_date',
        'to_date',
        'sync_type',
        'total_synced',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
