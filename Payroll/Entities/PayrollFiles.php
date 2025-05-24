<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payroll\Database\factories\PayrollFilesFactory;

class PayrollFiles extends Model
{
    use HasFactory;
    use IconTrait;

    public const FILE_PATH = 'payroll-files';


    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): PayrollFilesFactory
    {
        //return PayrollFilesFactory::new();
    }
    protected $appends = ['file_url', 'icon','ImageUrl','files_url','ImagesUrl'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3(PayrollFiles::FILE_PATH . '/' . $this->advance_repayment_id . '/' . $this->hashname);
    }
    public function getImageUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->advance_repayment_id . '/' . $this->hashname);
    }
    public function getFilesUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3(PayrollFiles::FILE_PATH . '/' . $this->employee_expense_repayment_id . '/' . $this->hashname);
    }
    public function getImagesUrlAttribute()
    {
        if ($this->external_link) {
            return str($this->external_link)->contains('http')
                ? $this->external_link
                : asset_url_local_s3($this->external_link);
        }

        return asset_url_local_s3(self::FILE_PATH . '/' . $this->employee_expense_repayment_id . '/' . $this->hashname);
    }

}
