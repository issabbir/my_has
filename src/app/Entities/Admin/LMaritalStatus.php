<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LMaritalStatus extends Model
{
    protected $table = "pmis.l_maritial_status";
    protected $primaryKey = "maritial_status_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->maritial_status;
    }

    protected function getValueAttribute() {
        return $this->maritial_status_id;
    }
}
