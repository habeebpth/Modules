<?php
namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    protected $fillable = [
        'company_id', 'setting_key', 'setting_value'
    ];

    public static function getSetting($key, $default = null)
    {
        $setting = self::where('company_id', user()->company_id)
            ->where('setting_key', $key)
            ->first();

        return $setting ? $setting->setting_value : $default;
    }

    public static function setSetting($key, $value)
    {
        return self::updateOrCreate(
            ['company_id' => user()->company_id, 'setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
