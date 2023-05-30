<?php


namespace App\Entities\Security;


use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'CPA_SECURITY.sec_reports';
    protected $primaryKey = 'report_id';
    protected $with = ['module', 'params'];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function params() {
        return $this->hasMany(ReportParam::class, 'report_id')->orderBy('order_no', 'asc');
    }
}
