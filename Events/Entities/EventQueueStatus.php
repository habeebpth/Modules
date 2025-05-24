<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Events\Database\factories\EventStudentFactory;

class EventQueueStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'gents',
        'ladies',
        'countdown_minutes'
    ];

    /**
     * Get the event associated with the queue status.
     */
    public function event()
    {
        return $this->belongsTo(EvntEvent::class, 'event_id');
    }

    protected static function newFactory()
    {
        //return EventStudentFactory::new();
    }
}