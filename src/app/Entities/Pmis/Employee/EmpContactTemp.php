<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Admin\LContactType;
class EmpContactTemp extends Model
{
    protected $table = 'pmis.emp_contacts_temp';
    protected $primaryKey = 'emp_contacts_id';
    public $incrementing = false;

    protected $with = ['contact_type'];

    public function contact_type() {
        return $this->belongsTo(LContactType::class, 'emp_contact_type_id');
    }

}
