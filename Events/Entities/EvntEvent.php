<?php

namespace Modules\Events\Entities;

use App\Models\EventAttendee;
use App\Models\EventFile;
use App\Models\MentionUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\ActiveScope;
use App\Traits\HasCompany;
use Modules\Events\Database\factories\EvntEventFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvntEvent extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasCompany;

    /**
     * The attributes that are mass assignable.
     */
    public const FILE_PATH = 'evnt_events';
    protected $table = 'evnt_events';
    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
    ];
    protected $fillable = [];
    public function attendee(): HasMany
    {
        return $this->hasMany(EventAttendee::class, 'event_id');
    }

    public function getUsers()
    {
        $userArray = [];

        foreach ($this->attendee as $attendee) {
            array_push($userArray, $attendee->user()->select('id', 'email', 'name', 'email_notifications')->first());
        }

        return collect($userArray);
    }

    public function files()
    {
        return $this->hasMany(EventFile::class, 'event_id')->orderByDesc('id');
    }

    public function mentionUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mention_users')->withoutGlobalScope(ActiveScope::class)->using(MentionUser::class);
    }

    public function mentionEvent(): HasMany
    {
        return $this->hasMany(MentionUser::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'host');
    }

    protected static function newFactory(): EvntEventFactory
    {
        //return EvntEventFactory::new();
    }
    protected $appends = ['brocher_url', 'brocher_path','icon_url', 'icon_path','banner_url', 'banner_path'];

    /**
     * Get the full URL for the airline's file.
     *
     * @return string
     */
    public function getBrocherUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->brocher);
    }

    /**
     * Get the file path for the stored file.
     *
     * @return string
     */
    public function getBrocherPathAttribute()
    {
        return $this->external_link ?: (self::FILE_PATH . '/' . $this->id . '/' . $this->brocher);
    }
    public function getIconUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->icon);
    }

    /**
     * Get the file path for the stored file.
     *
     * @return string
     */
    public function getIconPathAttribute()
    {
        return $this->external_link ?: (self::FILE_PATH . '/' . $this->id . '/' . $this->icon);
    }
    public function getBannerUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->banner);
    }

    /**
     * Get the file path for the stored file.
     *
     * @return string
     */
    public function getBannerPathAttribute()
    {
        return $this->external_link ?: (self::FILE_PATH . '/' . $this->id . '/' . $this->banner);
    }

}
