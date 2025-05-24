<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Events\Database\factories\EventParticipantFactory;

class EventParticipant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): EventParticipantFactory
    {
        //return EventParticipantFactory::new();
    }
    // EventParticipant.php
public function checkinpoint()
{
    return $this->belongsTo(EventCheckinPoint::class, 'checkin_point_id');
}

}
