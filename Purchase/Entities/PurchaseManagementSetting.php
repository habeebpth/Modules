<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class PurchaseManagementSetting extends BaseModel
{
    protected $table = 'purchase_management_settings';

    public const MODULE_NAME = 'purchase';

}
