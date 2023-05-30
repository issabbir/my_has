<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LFamilyMemberStatus extends Model
{
    protected $table = "pmis.l_family_member_status";
    protected $primaryKey = "family_member_status_id";

    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->family_member_status;
    }

    protected function getValueAttribute() {
        return $this->family_member_status_id;
    }
}
