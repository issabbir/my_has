<?php


namespace App\Entities\Admin;


use App\Entities\Pmis\Employee\Employee;
use Illuminate\Database\Eloquent\Model;

class PrProcessUsers extends Model
{
    protected $table = "pmis.pr_process_users";
    protected $primaryKey = "";
    protected $with = ['employee'];
    public function employee() {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
