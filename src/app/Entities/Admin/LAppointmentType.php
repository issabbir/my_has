<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LAppointmentType extends Model
{
    protected $table = "pmis.l_appoinment_type";
    protected $primaryKey = "appoint_type_id";
}