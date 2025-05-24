<?php

namespace Modules\Events\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Events\Entities\EvntEvent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Events\Database\factories\EventRegistrationFactory;

class EventRegistration extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): EventRegistrationFactory
    {
        //return EventRegistrationFactory::new();
    }
    public function event()
    {
        return $this->belongsTo(EvntEvent::class, 'event_id');
    }
    public function panchayath()
{
    return $this->belongsTo(Panchayat::class, 'panchayat_id');
}

public function district()
{
    return $this->belongsTo(District::class, 'district_id');
}
}

