<?php
namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LRosterShift extends Model
{
    protected $table = "pmis.l_roster_shift";
    protected $primaryKey = "shift_id";
    protected $appends = ['text', 'value'];

    protected function getTextAttribute()
    {
        return $this->shift_code." ({$this->shift_start_time} - $this->shift_end_time)";
    }

    protected function getValueAttribute()
    {
        return $this->shift_id;
    }
}
