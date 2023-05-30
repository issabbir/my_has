<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LHoliday extends Model
{
    protected $table = "pmis.l_holiday";
    protected $primaryKey = "holiday_id";
    protected $fillable = ['holiday_id','holiday', 'holiday_bng', 'date_from', 'date_to','description'];


}
